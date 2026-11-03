<?php

namespace Files {

    /**
     * Rethrows a failed request as the SDK exception that describes it.
     *
     * Every 5xx response is an Exception\ApiServerException, whatever its body. Any other HTTP error response whose
     * body is a Files.com error object is an Exception\ApiException, or the generated subclass its `type` names; one
     * without a readable error object is an Exception\ApiBadResponseException. A request that could not connect or
     * timed out is an Exception\ApiConnectException; other failures of the HTTP client are
     * ApiTooManyRedirectsException, ApiRequestException or ApiTransferException. All of these but ApiException
     * extend Exception\ApiConnectionException. An SDK exception is rethrown as it is, and anything else becomes a
     * FilesException.
     *
     * The message for an HTTP error response names the request's method and status, and an API error's type, never
     * the response body or the request URL, which a body can echo in any encoding. A transport error's message keeps
     * the HTTP client's text with the request URL, as the client printed it, replaced with [redacted]. DEBUG logging
     * shows the HTTP client's full message.
     *
     * @internal
     */
    function handleErrorResponse($error)
    {
        if ($error instanceof Exception\FilesException) {
            throw $error;
        }

        $message = $error->getMessage();
        if (method_exists($error, 'getRequest')) {
            Logger::debug($message);
            $message = withoutRequestUrl($message, $error->getRequest()->getUri());
        }
        $code = $error->getCode();

        if ($error instanceof \GuzzleHttp\Exception\ConnectException) {
            throw new Exception\ApiConnectException($message, $code);
        }

        if ($error instanceof \GuzzleHttp\Exception\BadResponseException) {
            $failure = 'The ' . $error->getRequest()->getMethod() . ' request failed with HTTP ' . statusText($code);

            // Every 5xx stays an ApiServerException, as callers that catch it rely on, even with an error object.
            if ($error instanceof \GuzzleHttp\Exception\ServerException) {
                throw new Exception\ApiServerException("$failure.", $code);
            }

            $errorData = errorObjectOf($error->getResponse());
            if ($errorData === null) {
                throw new Exception\ApiBadResponseException(
                    "$failure, and the response held no readable Files.com error object.",
                    $code
                );
            }

            $type = errorTypeOf($errorData);
            $exceptionClass = apiExceptionClassFor($type);
            throw new $exceptionClass($type === null ? "$failure." : "$failure ($type).", $code, $errorData);
        }

        if ($error instanceof \GuzzleHttp\Exception\TooManyRedirectsException) {
            throw new Exception\ApiTooManyRedirectsException($message, $code);
        }

        if ($error instanceof \GuzzleHttp\Exception\RequestException) {
            throw new Exception\ApiRequestException($message, $code);
        }

        if ($error instanceof \GuzzleHttp\Exception\TransferException) {
            throw new Exception\ApiTransferException($message, $code);
        }

        throw new Exception\FilesException($message, $code);
    }

    /**
     * Replaces the request URL in a transport error's message, such as a cURL error, with [redacted]: as sent, and as
     * Guzzle 7 prints it, with any password masked.
     *
     * @internal
     */
    function withoutRequestUrl($message, $uri)
    {
        $printedForms = [(string) $uri];
        $userInfo = $uri->getUserInfo();
        if (strpos($userInfo, ':') !== false) {
            $printedForms[] = (string) $uri->withUserInfo(strstr($userInfo, ':', true), '***');
        }

        return $printedForms[0] === '' ? $message : str_replace($printedForms, '[redacted]', $message);
    }

    /**
     * An HTTP status with its standard reason phrase, such as '404 Not Found', rather than the text the server sent.
     *
     * @internal
     */
    function statusText($status)
    {
        $status = (int) $status;
        if ($status < 100 || $status > 599) {
            return (string) $status;
        }

        return trim($status . ' ' . (new \GuzzleHttp\Psr7\Response($status))->getReasonPhrase());
    }

    /**
     * The Files.com error object in a response's body, or null when it has none: the body is empty, not JSON, JSON
     * other than an object, or a stream that cannot be read, such as a destination open for writing only. An array
     * contributes its first element.
     *
     * @internal
     */
    function errorObjectOf($response)
    {
        try {
            $body = $response === null ? null : $response->getBody();
            if ($body === null || !$body->isReadable()) {
                return null;
            }
            $errorData = json_decode($body->getContents());
        } catch (\Exception $readError) {
            return null;
        }
        if (is_array($errorData)) {
            $errorData = isset($errorData[0]) ? $errorData[0] : null;
        }

        return is_object($errorData) ? $errorData : null;
    }

    /**
     * An error object's type, such as 'not-authenticated/lockout-region-mismatch', or null when it is missing or not in
     * the form the API uses.
     *
     * @internal
     */
    function errorTypeOf($errorData)
    {
        $type = isset($errorData->type) ? $errorData->type : null;

        return is_string($type) && preg_match('@\A[a-z0-9]+(-[a-z0-9]+)*(/[a-z0-9]+(-[a-z0-9]+)*)?\z@', $type)
          ? $type
          : null;
    }

    /**
     * The ApiException subclass for an error type, such as NotAuthenticated\LockoutRegionMismatchException for
     * 'not-authenticated/lockout-region-mismatch'. A null or unknown type, or one naming a class that is not an
     * ApiException, gives ApiException itself.
     *
     * @internal
     */
    function apiExceptionClassFor($type)
    {
        if ($type === null) {
            return Exception\ApiException::class;
        }

        $toPascalCase = function ($typePart) {
            return implode('', array_map('\\ucfirst', explode('-', $typePart)));
        };
        $className = 'Files\\Exception\\' . implode('\\', array_map($toPascalCase, explode('/', $type))) . 'Exception';

        return class_exists($className) && is_subclass_of($className, Exception\ApiException::class)
          ? $className
          : Exception\ApiException::class;
    }
}
