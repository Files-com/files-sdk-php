<?php

declare(strict_types=1);

namespace Files {

    function handleErrorResponse($error)
    {
        if ($error instanceof Exception\FilesException) {
            throw $error;
        }

        $className = null;
        $errorData = null;
        $message = $error->getMessage();
        if (method_exists($error, 'getRequest')) {
            Logger::debug($message);
            $uri = (string) $error->getRequest()->getUri();
            if ($uri !== '') {
                $message = str_replace($uri, '[redacted]', $message);
            }
        }

        switch (get_class($error)) {
            case 'GuzzleHttp\\Exception\\TransferException':
                $className = 'ApiTransferException';
                break;

            case 'GuzzleHttp\\Exception\\ConnectException':
                $className = 'ApiConnectException';
                break;

            case 'GuzzleHttp\\Exception\\RequestException':
                $className = 'ApiRequestException';
                break;

            case 'GuzzleHttp\\Exception\\BadResponseException':
                $className = 'ApiBadResponseException';
                break;

            case 'GuzzleHttp\\Exception\\ServerException':
                $className = 'ApiServerException';
                break;

            case 'GuzzleHttp\\Exception\\TooManyRedirectsException':
                $className = 'ApiTooManyRedirectsException';
                break;
        }

        if (!$className) {
            if (method_exists($error, 'getResponse')) {
                $response = $error->getResponse();
                $errorData = $response === null ? null : json_decode($response->getBody()->getContents());
            } else {
                $response = $error;
            }

            if ($response === null) {
                throw new Exception\FilesException($message, $error->getCode());
            }

            if ($errorData === null) {
                throw new Exception\FilesException($message, $error->getCode());
            }

            if (is_array($errorData)) {
                $errorData = $errorData[0];
            }

            if ($errorData) {
                if (!@$errorData->type) {
                    throw new Exception\FilesException($message, $error->getCode());
                }

                $toPascalCase = function ($errorPart) {
                    return implode('', array_map('\\ucfirst', explode('-', $errorPart)));
                };

                $parts = explode('/', $errorData->type);

                if (count($parts) > 1) {
                    list($errorFamily, $errorType) = array_map($toPascalCase, $parts);
                    $className = $errorFamily . '\\' . $errorType . 'Exception';
                } else {
                    $errorType = $toPascalCase($parts[0]);
                    $className = $errorType . 'Exception';
                }
            }
        }

        if ($className) {
            $ExceptionClass = "\\Files\\Exception\\{$className}";
        } else {
            $ExceptionClass = '\\Files\\Exception\\ApiException';
        }

        throw new $ExceptionClass($message, $error->getCode(), $errorData);
    }
}
