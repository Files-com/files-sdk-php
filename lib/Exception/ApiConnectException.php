<?php

namespace Files\Exception;

/**
 * The Files.com API could not be reached, or the request timed out, after any network retries.
 */
class ApiConnectException extends ApiConnectionException
{
}
