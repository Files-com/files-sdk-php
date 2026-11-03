<?php

namespace Files\Exception;

/**
 * A response the SDK could not use, such as an HTTP error status below 500 without a readable
 * Files.com error object, a model that is not a JSON object, a list that is not a JSON array, or a list
 * cursor that was already followed.
 */
class ApiBadResponseException extends ApiConnectionException
{
}
