<?php

namespace Files\Exception;

/**
 * The API answered with an HTTP 5xx status, whatever its body holds, even a Files.com error object.
 * The exception code is the status.
 */
class ApiServerException extends ApiConnectionException
{
}
