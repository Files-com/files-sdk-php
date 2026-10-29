<?php

declare(strict_types=1);

namespace Files\Transfer;

use Files\Exception\InvalidParameterException;

/**
 * How an adaptive upload sizes its parts. Nothing in the API's answers grants this by name: an upload chooses its own
 * part size only where its first answer shows every part going straight to Amazon S3 as one ordinary multipart upload
 * (route), whose signed URLs bind neither a part's length nor its bytes. There partsize and next_partsize are
 * recommendations, and each part is sent with a Content-Length of exactly its bytes. Everywhere else, including any
 * destination with direct_connection_info, the API sizes the parts. parallel_parts says only whether parts may be sent
 * at once, never how large they may be.
 *
 * The limits are the SDK's own for planning an upload to S3, within S3's: parts but the last of at least 5 MiB; parts
 * of at most 1 GiB, which bounds what one part holds for its retries; at most 10,000 parts, or fewer when the first
 * answer's available_parts says so; and files of at most 5 TiB. They promise nothing of Files.com's or S3's own limits
 * and bind no other destination: a file they cannot plan is sized by the API, unless it is of at most 5 TiB and the
 * job's buffer could not hold even the smallest part that fits it in the parts available, which the API's plan would
 * need as well. That file is refused (partSize), and its upload keeps no plan for it.
 */
final class UploadPlan
{
    const MIB = 1048576;
    const MIN_PART_BYTES = 5242880;
    const MAX_PART_BYTES = 1073741824;
    const MAX_PARTS = 10000;
    const MAX_FILE_BYTES = 5497558138880;
    // The size every part but the last has, unless the file needs larger parts to fit the parts available, or the
    // job's buffer smaller ones.
    const PREFERRED_PART_BYTES = 8388608;

    // The query parameters a presigned UploadPart may have: those naming the part and its upload, and SigV4's own.
    private static $queryParameters = [
        'partNumber', 'uploadId', 'X-Amz-Algorithm', 'X-Amz-Credential', 'X-Amz-Date', 'X-Amz-Expires',
        'X-Amz-SignedHeaders', 'X-Amz-Signature', 'X-Amz-Security-Token',
    ];

    // The size of every part but the last of an upload of $total bytes: PREFERRED_PART_BYTES, or the larger size, in
    // whole MiB, that fits $total in the parts available, but no larger than $most, the largest part the job's buffer
    // holds as the upload reserves it (PHP_INT_MAX when parts stream from a file), and never smaller than the least that
    // fits: the parts available, and 5 MiB unless the whole file is one part. A size above $most is a plan the buffer
    // cannot hold, which the upload refuses before anything is read or sent. Null, for the API to size the parts, when
    // $total is larger than MAX_FILE_BYTES or needs parts larger than MAX_PART_BYTES in the parts available: those are
    // only the SDK's limits. A total needing parts that large and larger than $most too throws an
    // InvalidParameterException instead: the API's parts, in the parts available, could not be smaller.
    public static function partSize($total, $availableParts, $most)
    {
        $parts = is_int($availableParts) && $availableParts > 0 ? min($availableParts, self::MAX_PARTS) : self::MAX_PARTS;
        if ($total > self::MAX_FILE_BYTES) {
            return null;
        }

        $needed = self::ceilDiv($total, $parts);
        if ($needed > self::MAX_PART_BYTES) {
            if ($needed <= $most) {
                return null;
            }

            throw new InvalidParameterException('The ' . $total . '-byte upload needs parts of at least ' . $needed . ' bytes in its ' . $parts . ' available parts, more than the ' . $most . ' bytes max_buffer_bytes can hold for a part');
        }

        $preferred = min(self::ceilDiv(max(self::PREFERRED_PART_BYTES, $needed), self::MIB) * self::MIB, self::MAX_PART_BYTES);
        $least = max($needed, self::MIN_PART_BYTES);
        return max(min($preferred, $most), $total <= $least ? $total : $least);
    }

    // The ordinary multipart upload straight to Amazon S3 that $grant, the API's answer (decoded JSON) for part
    // $number, is for: a digest of its endpoint, object and upload ID, or null when the answer is not exactly that.
    // Every condition is read from the answer as the API gave it, and all must hold:
    //
    //   action                  put/write: an ordinary write, not an append or restart
    //   http_method             PUT
    //   part_number             $number
    //   upload_uri              https on the default port, without user information or fragment, to an S3
    //                           general-purpose endpoint (generalPurposeEndpoint), and a SigV4 presigned UploadPart:
    //                           partNumber naming $number; uploadId; X-Amz-Algorithm AWS4-HMAC-SHA256; X-Amz-Credential
    //                           scoped to s3; X-Amz-SignedHeaders host alone, so that neither the length nor the bytes
    //                           are signed; X-Amz-Signature; each once, and no parameter but those of
    //                           $queryParameters, so none binding a checksum, digest or length
    //   headers, parameters     none
    //   send                    exactly the part's size as its Content-Length and its bytes as the body
    //   direct_connection_info  none
    //
    // Anything else, such as a host that only resembles S3's, a custom or S3-compatible endpoint, a Files.com or agent
    // URL, or a parallel flag on its own, is not this route. The URL is only read: the part goes to it exactly as given.
    public static function route(array $grant, $number)
    {
        $url = self::field($grant, 'upload_uri');
        if (
            self::field($grant, 'action') !== 'put/write' || self::field($grant, 'http_method') !== 'PUT'
            || self::field($grant, 'part_number') !== $number || self::field($grant, 'direct_connection_info') !== null
            || !self::none(self::field($grant, 'headers')) || !self::none(self::field($grant, 'parameters'))
            || !self::ordinarySend(self::field($grant, 'send'))
            || !is_string($url) || strpos($url, '?') === false || strpos($url, '#') !== false
        ) {
            return null;
        }

        $parts = parse_url($url);
        if (!is_array($parts) || !isset($parts['scheme'], $parts['host'], $parts['path'])) {
            return null;
        }

        if (
            strtolower($parts['scheme']) !== 'https' || (isset($parts['port']) && $parts['port'] !== 443)
            || isset($parts['user']) || isset($parts['pass']) || strlen($parts['path']) <= 1
            || !self::generalPurposeEndpoint($parts['host'])
        ) {
            return null;
        }

        list($resource, $query) = explode('?', $url, 2);
        $values = self::signedQuery($query);
        if (
            $values === null || (string) self::field($values, 'uploadId') === '' || self::field($values, 'partNumber') !== (string) $number
            || self::field($values, 'X-Amz-Algorithm') !== 'AWS4-HMAC-SHA256'
            || !self::endsWith((string) self::field($values, 'X-Amz-Credential'), '/s3/aws4_request')
            || self::field($values, 'X-Amz-SignedHeaders') !== 'host' || (string) self::field($values, 'X-Amz-Signature') === ''
        ) {
            return null;
        }

        return 'sha256:' . hash('sha256', $resource . "\n" . $values['uploadId']);
    }

    // s3, s3.<region>, s3-<region> or s3.dualstack.<region> under amazonaws.com or amazonaws.com.cn, alone, with the
    // bucket in the path, or after a bucket of one or more labels. Not S3's accelerate, FIPS, website, access point,
    // Object Lambda, Outposts, Express or control endpoints, which this SDK does not size parts for.
    public static function generalPurposeEndpoint($host)
    {
        $host = strtolower((string) $host);
        $within = null;
        foreach (['.amazonaws.com', '.amazonaws.com.cn'] as $suffix) {
            if (self::endsWith($host, $suffix)) {
                $within = substr($host, 0, strlen($host) - strlen($suffix));
                break;
            }
        }

        if ($within === null || $within === '' || $within === false) {
            return false;
        }

        $labels = explode('.', $within);
        $endpoint = null;
        for ($index = count($labels) - 1; $index >= 0; $index--) {
            if ($labels[$index] === 's3') {
                $endpoint = $index;
                break;
            }
        }

        if ($endpoint !== null) {
            $after = array_slice($labels, $endpoint + 1);
            $plain = count($after) === 0;
            $regional = count($after) === 1 && self::region($after[0]);
            $dualstack = count($after) === 2 && $after[0] === 'dualstack' && self::region($after[1]);
            if (!$plain && !$regional && !$dualstack) {
                return false;
            }
        } else {
            $endpoint = count($labels) - 1;
            $last = $labels[$endpoint];
            if (strpos($last, 's3-') !== 0 || !self::region(substr($last, 3))) {
                return false;
            }
        }

        foreach (array_slice($labels, 0, $endpoint) as $label) {
            if ($label === '') {
                return false;
            }
        }

        return true;
    }

    // A region such as us-east-1, eu-central-2 or us-gov-west-1: two letters, words of letters, then a number.
    private static function region($label)
    {
        $words = explode('-', (string) $label);
        if (count($words) < 3 || strlen($words[0]) !== 2) {
            return false;
        }

        $last = array_pop($words);
        foreach ($words as $word) {
            if (!preg_match('/\A[a-z]+\z/', $word)) {
                return false;
            }
        }

        return preg_match('/\A[0-9]+\z/', $last) === 1;
    }

    // The query's parameters, each name with its percent-decoded value, or null unless every one is among
    // $queryParameters, as it is written there, given once and with a value.
    private static function signedQuery($query)
    {
        $values = [];
        foreach (explode('&', (string) $query) as $pair) {
            $split = explode('=', $pair, 2);
            if (count($split) !== 2 || !in_array($split[0], self::$queryParameters, true) || array_key_exists($split[0], $values)) {
                return null;
            }

            $values[$split[0]] = self::unescape($split[1]);
        }

        return $values;
    }

    // Percent-decoding alone, as a query has it: a plus sign stays as it is.
    private static function unescape($text)
    {
        return preg_replace_callback('/%[0-9A-Fa-f]{2}/', function ($match) {
            return chr(hexdec(substr($match[0], 1)));
        }, $text);
    }

    // Exactly the part's size as its Content-Length and its bytes as the body.
    public static function ordinarySend($send)
    {
        if (!is_array($send) || count($send) !== 2 || !isset($send['partsize'], $send['partdata'])) {
            return false;
        }

        return is_string($send['partsize']) && strcasecmp($send['partsize'], 'required-header Content-Length') === 0 && $send['partdata'] === 'body';
    }

    private static function none($value)
    {
        return $value === null || $value === [];
    }

    private static function field(array $values, $name)
    {
        return array_key_exists($name, $values) ? $values[$name] : null;
    }

    private static function endsWith($text, $suffix)
    {
        return strlen($text) >= strlen($suffix) && substr($text, strlen($text) - strlen($suffix)) === $suffix;
    }

    // $dividend / $divisor rounded up, for a non-negative $dividend and positive $divisor, in integers.
    private static function ceilDiv($dividend, $divisor)
    {
        $remainder = $dividend % $divisor;
        return ($dividend - $remainder) / $divisor + ($remainder > 0 ? 1 : 0);
    }
}
