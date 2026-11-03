<?php

namespace Files\Exception;

/**
 * @deprecated Not thrown. A 4xx response is an ApiException when its body is a Files.com error
 * object, and an ApiBadResponseException otherwise.
 */
class ApiClientException extends ApiConnectionException
{
}
