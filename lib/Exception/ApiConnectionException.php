<?php

namespace Files\Exception;

/**
 * The parent of the exceptions that classify how a request the SDK sent failed, apart from an API error
 * (ApiException): ApiConnectException when the API could not be reached, ApiRequestException, ApiTransferException
 * and ApiTooManyRedirectsException when the HTTP client failed, ApiServerException for any 5xx response, whatever its
 * body, and ApiBadResponseException for a response the SDK could not use. ApiClientException is deprecated and not
 * thrown. For an HTTP error response, the exception code is its status.
 */
class ApiConnectionException extends FilesException
{
}
