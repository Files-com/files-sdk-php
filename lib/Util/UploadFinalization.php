<?php

namespace Files\Util;

use Files\Api;
use Files\Exception\ApiBadResponseException;
use Files\Exception\ApiConnectionException;
use Files\Exception\ApiException;
use Files\Exception\ApiServerException;
use Files\Exception\InvalidParameterException;
use Files\Files;
use Files\Logger;

/**
 * @internal How the upload helpers end an upload (POST /files/{path}, action end) and wait for its outcome.
 *
 * Every helper's end asks for async_finalize. Files.com answers with the File (200 or 201) once the upload is complete,
 * as it always has for its own storage. For a destination it completes in the background (a remote server mount or an
 * Agent), it may answer 202 {"status": "pending", "ref", "progress"} instead: the helper then sends the identical
 * request again after each such answer, one, two, then three seconds later (pollSeconds), until the File or an error
 * comes back. Files.com keeps one finalization for the
 * upload's ref and answers each repeat from it, so a repeat never finalizes the upload again. Pending at progress 100
 * is still pending; only the File completes the upload.
 *
 * A 202 that is not a JSON object with status "pending" and the request's ref breaks that protocol after Files.com
 * accepted the request: it is an ApiBadResponseException, the upload's outcome is unknown, and nothing is sent again.
 * progress counts only as a JSON integer from 0 to 100; any other value is reported as null and is not an error.
 *
 * Once Files.com has answered pending, its later answers decide: the File, or a Files.com error (a 4xx other than 429),
 * which is the upload's outcome and is thrown as the SDK throws it for any request. A repeat that fails without an
 * answer, or with a 429 or a 5xx, is sent again within the SDK's network retries; when those are spent, or for an
 * answer the SDK cannot use, the failure is thrown with a message saying the outcome is unknown and Files.com may still
 * complete the upload (unknownOutcome).
 */
final class UploadFinalization
{
    // The seconds to wait between the $pendingAnswers-th answer that an upload is pending and the next request for its
    // outcome: one after the first, two after the second and three after each later one, counted for each upload.
    public static function pollSeconds($pendingAnswers)
    {
        return min($pendingAnswers, 3);
    }

    // The phrase every error after Files.com accepted the end uses, so that callers and logs can tell it apart.
    const MAY_STILL_COMPLETE = 'Files.com accepted the end of the upload and may still complete it';

    /**
     * The pending answer to the end of the upload of $ref to $path: null when the answer ($status and $data, its JSON
     * body decoded to an object or an array) is not a 202, and otherwise ['progress' => an integer from 0 to 100, or
     * null]. A 202 that is not exactly a pending finalization of $ref throws an ApiBadResponseException.
     */
    public static function pending($status, $data, $ref, $path)
    {
        if ((int) $status !== 202) {
            return null;
        }

        $fields = is_object($data) ? get_object_vars($data) : $data;
        $valid = is_array($fields) && isset($fields['status'], $fields['ref']) && $fields['status'] === 'pending'
            && is_string($fields['ref']) && $fields['ref'] === $ref;
        if (!$valid) {
            throw new ApiBadResponseException(
                'Files.com accepted the end of the upload to ' . $path . ' (HTTP 202), but its answer is not the pending finalization of ref '
                . $ref . ', so the upload\'s outcome is unknown: ' . self::MAY_STILL_COMPLETE . '.',
                202
            );
        }

        $progress = array_key_exists('progress', $fields) ? $fields['progress'] : null;
        return ['progress' => is_int($progress) && $progress >= 0 && $progress <= 100 ? $progress : null];
    }

    /**
     * Ends the upload to $path with $params (its action end, ref, ETags and size, and the caller's end parameters,
     * checked as File::create checks them) and $options, the generated methods' options, and waits for its outcome:
     * returns the answer holding the File. While Files.com finishes it, $report (a callable or null) is called with
     * each pending answer's progress (an integer from 0 to 100, or null); what it throws stops the wait and is thrown
     * as it was. The first request fails as it always has: a failure is thrown as the SDK throws it for any request.
     *
     * Every later request keeps the first one's context: its options and the SDK's configuration as it stood then (its
     * credential, workspace, language, site and endpoint, handler, network retries, timeouts and region policy), so a
     * callback that changes the SDK's settings while Files.com finishes the upload changes none of them.
     */
    public static function finish($path, array $params, array $options, $report)
    {
        if (isset($params['provided_mtime']) && !is_string($params['provided_mtime'])) {
            throw new InvalidParameterException('$provided_mtime must be of type string; received ' . gettype($params['provided_mtime']));
        }

        $params['path'] = $path;
        $params['async_finalize'] = true;
        $config = Files::snapshot();
        if (!array_key_exists('region_relocation', $options)) {
            $options['region_relocation'] = Files::getRegionRelocation();
        }

        $requestPath = '/files/' . rawurlencode(strval($path));
        $response = Api::sendSingleRequest($requestPath, 'POST', $params, $options, null, $config);
        $pending = self::pending($response->status, isset($response->data) ? $response->data : null, $params['ref'], $path);
        if ($pending === null) {
            Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status);
            return $response;
        }

        for ($pendingAnswers = 1; true; $pendingAnswers++) {
            if ($report !== null) {
                call_user_func($report, $pending['progress']);
            }

            self::sleep(self::pollSeconds($pendingAnswers));
            $response = self::poll($requestPath, $params, $options, $config, $path);
            $pending = self::pending($response->status, isset($response->data) ? $response->data : null, $params['ref'], $path);
            if ($pending === null) {
                try {
                    Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status);
                } catch (ApiBadResponseException $error) {
                    throw self::unknownOutcome($error, $path, $params['ref']);
                }

                return $response;
            }
        }
    }

    /**
     * What a request for the outcome of the upload of $ref to $path that failed with $error (as the SDK throws it)
     * stops with: $error itself when it is Files.com's answer (an HTTP 4xx other than 429), and otherwise
     * unknownOutcome.
     */
    public static function pollFailure($error, $path, $ref)
    {
        return self::isAnswer($error) ? $error : self::unknownOutcome($error, $path, $ref);
    }

    /**
     * $error, a failure while waiting for the outcome of the upload of $ref to $path, as an error saying that outcome is
     * unknown: an ApiConnectionException of the same class (ApiConnectException, ApiServerException and so on) with the
     * same code and $error as its previous exception; anything else as an ApiBadResponseException.
     */
    public static function unknownOutcome($error, $path, $ref)
    {
        $message = 'Waiting for the outcome of the upload to ' . $path . ' (ref ' . $ref . ') failed, so it is unknown: '
            . self::MAY_STILL_COMPLETE . '. ' . $error->getMessage();
        $class = $error instanceof ApiConnectionException ? get_class($error) : ApiBadResponseException::class;
        return new $class($message, (int) $error->getCode(), $error);
    }

    // Whether $error, a request's failure as the SDK throws it, is Files.com's answer: an HTTP 4xx other than 429.
    private static function isAnswer($error)
    {
        $status = (int) $error->getCode();
        $answered = $error instanceof ApiException || ($error instanceof ApiBadResponseException && $status >= 400);
        return $answered && $status >= 400 && $status < 500 && $status !== 429;
    }

    // Whether a request for an upload's outcome that failed with $error is sent again: for a 429, or a 5xx other than
    // 501. A failure without an answer was already retried as every request is (Api), within the same network retries.
    private static function isRetryable($error)
    {
        $status = (int) $error->getCode();
        $answered = $error instanceof ApiException || $error instanceof ApiServerException || $error instanceof ApiBadResponseException;
        return $answered && ($status === 429 || ($status >= 500 && $status !== 501));
    }

    // One more request for the outcome of the upload to $path, with the first request's $options and $config, sent
    // again as isRetryable allows, waiting as Api waits between its retries.
    private static function poll($requestPath, array $params, array $options, array $config, $path)
    {
        $retries = 0;
        while (true) {
            try {
                return Api::sendSingleRequest($requestPath, 'POST', $params, $options, null, $config);
            } catch (\Files\Exception\FilesException $error) {
                if (!self::isRetryable($error) || $retries >= $config['max_network_retries']) {
                    throw self::pollFailure($error, $path, $params['ref']);
                }

                ++$retries;
                Logger::info('Retrying the request for the outcome of the upload to ' . $path . ' (retry #' . $retries . ')');
                self::sleep(min(($retries + 1) * $config['min_network_retry_delay'], $config['max_network_retry_delay']));
            }
        }
    }

    private static function sleep($seconds)
    {
        $clock = function () {
            return function_exists('hrtime') ? hrtime(true) / 1000000000 : microtime(true);
        };
        $until = $clock() + $seconds;
        while (($remaining = $until - $clock()) > 0) {
            // Keep usleep's argument within an integer on 32-bit PHP.
            usleep((int) min(1000000, ceil($remaining * 1000000)));
        }
    }
}
