<?php

namespace Files\Util;

/**
 * Best-effort HTTP modification-date precondition for one download. It is not a strong version or resume identity.
 * @internal
 */
final class DownloadDate
{
    private $baseline = null;
    private $enabled;

    public function __construct($enabled = true)
    {
        $this->enabled = $enabled;
    }

    public function header()
    {
        return !$this->enabled || $this->baseline === null ? null : gmdate('D, d M Y H:i:s', $this->baseline) . ' GMT';
    }

    // False only when a successful response declares a newer valid date, before its body may be used.
    public function accept($response)
    {
        if (!$this->enabled) {
            return true;
        }
        $values = $response->getHeader('Last-Modified');
        $date = count($values) === 1 ? self::parse($values[0]) : null;
        if ($date === null) {
            return true;
        }
        if ($this->baseline !== null && $date > $this->baseline) {
            return false;
        }
        if ($this->baseline === null) {
            $this->baseline = $date;
        }
        return true;
    }

    private static function parse($value)
    {
        // HTTP-date's three forms only; strtotime also accepts relative dates and arbitrary human-readable text.
        $forms = [
            '/\A[A-Za-z]{3}, [0-9]{2} [A-Za-z]{3} [0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2} GMT\z/' => '!D, d M Y H:i:s \G\M\T',
            '/\A[A-Za-z]+, [0-9]{2}-[A-Za-z]{3}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2} GMT\z/' => '!l, d-M-y H:i:s \G\M\T',
            '/\A[A-Za-z]{3} [A-Za-z]{3} [ 0-9][0-9] [0-9]{2}:[0-9]{2}:[0-9]{2} [0-9]{4}\z/' => '!D M j H:i:s Y',
        ];
        foreach ($forms as $pattern => $format) {
            if (!preg_match($pattern, $value)) {
                continue;
            }
            $date = \DateTimeImmutable::createFromFormat($format, $value, new \DateTimeZone('UTC'));
            $errors = \DateTimeImmutable::getLastErrors();
            $day = strpos($value, ',') === false ? (int) substr($value, 8, 2) : (int) substr($value, strpos($value, ',') + 2, 2);
            return $date !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
                && $date->format('D') === substr($value, 0, 3) && (int) $date->format('j') === $day
                ? $date->getTimestamp() : null;
        }
        return null;
    }
}
