<?php

declare(strict_types=1);

namespace Files {

    function handleErrorResponse($error)
    {
        $className = null;
        $errorData = null;

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
                $errorData = json_decode($response->getBody()->getContents());
            } else {
                $response = $error;
            }

            if ($response === null) {
                throw new Exception\FilesException($error->getMessage(), $error->getCode());
            }

            if ($errorData === null) {
                throw new Exception\FilesException($error->getMessage(), $error->getCode());
            }

            if (is_array($errorData)) {
                $errorData = $errorData[0];
            }

            if ($errorData) {
                if (!@$errorData->type) {
                    throw new Exception\FilesException($error->getMessage(), $error->getCode());
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

        throw new $ExceptionClass($error->getMessage(), $error->getCode(), $errorData);
    }
}
