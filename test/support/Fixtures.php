<?php

declare(strict_types=1);

namespace Files\Test;

/**
 * Local fixtures that tests require: a missing, unreadable, invalid or empty one fails the test that loads it,
 * rather than giving it nothing to check.
 */
final class Fixtures
{
    /**
     * The non-empty JSON array in the file at $path.
     *
     * @throws \UnexpectedValueException when the file is missing, cannot be read, is not JSON, or is not a
     *     non-empty array
     */
    public static function requiredJsonArray($path)
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new \UnexpectedValueException("The required test fixture $path is missing or cannot be read.");
        }
        $json = file_get_contents($path);
        if ($json === false) {
            throw new \UnexpectedValueException("The required test fixture $path could not be read.");
        }
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException(
                "The required test fixture $path is not valid JSON: " . json_last_error_msg()
            );
        }
        if (!is_array($data) || $data === []) {
            throw new \UnexpectedValueException("The required test fixture $path holds no cases.");
        }

        return $data;
    }
}
