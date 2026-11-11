<?php

namespace Files\Util;

/**
 * When a signed URL expires, by its own query or the API's expires (expiresAt), and storage's word that it has, judged
 * from a refusal's status and the start of its body (isExpiryResponse). It is the one judge for every transfer:
 * Transfer\RetryPolicy (adaptive transfers) asks it, as do the helpers that transfer without them (ExpiryRenewal). It
 * lives outside the Transfer namespace, whose files declare strict_types, so that the helpers can use it on PHP 5, where
 * that declaration prints a warning.
 *
 * @internal
 */
final class StorageExpiry
{
    // The most of a refused response's body looked at for storage's word that its signed URL has expired.
    const SCAN_BYTES = 4096;

    // What storage says, in its XML error, when a signed URL has expired, even while the expiry the URL states is still
    // to come, or when it states none: Amazon S3 and storage compatible with it ("Request has expired"), Amazon and
    // Google (ExpiredToken) and Azure ("Signature not valid in the specified time frame"). Matched without regard to
    // case, as the Python and .NET SDKs match them.
    private static $markers = ['request has expired', '<code>expiredtoken</code>', 'signature not valid in the specified time frame'];

    // The types of the JSON error Files.com answers an expired download or upload URL of its own with.
    private static $types = ['download_request_expired', 'upload_request_expired'];

    // Whether storage refused a request because the signed URL it was sent to has expired, whatever expiry the URL
    // states: a 400 or 403 whose whole body, within SCAN_BYTES, is a JSON object whose type is one of $types, or, when
    // the body does not begin as JSON (an object, array or string, after JSON's white space, well formed or not), whose
    // first SCAN_BYTES hold one of $markers. A marker counts in a body cut short or kept only in part ($complete false);
    // JSON only when all of it was seen, and by its type alone, never by text inside it. Any other refusal, such as a
    // 403 that denies access, is not.
    public static function isExpiryResponse($status, $body, $complete = true)
    {
        if (($status !== 400 && $status !== 403) || !is_string($body)) {
            return false;
        }

        $scanned = (string) substr($body, 0, self::SCAN_BYTES);
        if (!preg_match('/\A[ \t\r\n]*[\[{"]/', $scanned)) {
            // ASCII letters alone, whatever the locale (strtolower follows it before PHP 8.2).
            $lowered = strtr($scanned, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
            foreach (self::$markers as $marker) {
                if (strpos($lowered, $marker) !== false) {
                    return true;
                }
            }

            return false;
        }

        if ($complete !== true || strlen($body) > self::SCAN_BYTES) {
            return false;
        }

        $error = json_decode($scanned, true);
        return is_array($error) && isset($error['type']) && in_array($error['type'], self::$types, true);
    }

    // When a signed URL stops working, as a Unix time, by the earlier of what its own query states and $apiExpires (the
    // API's expires for it, a date and time, when it gave one); null when neither states one.
    public static function expiresAt($url, $apiExpires = null)
    {
        $times = [];
        $fromUrl = self::urlExpiry($url);
        if (is_int($fromUrl)) {
            $times[] = $fromUrl;
        }

        if (is_string($apiExpires) && $apiExpires !== '') {
            $time = strtotime($apiExpires);
            if ($time !== false) {
                $times[] = $time;
            }
        }

        return $times ? min($times) : null;
    }

    // When a signed URL stops working, as its own query states it, as a Unix time: X-Amz-Date (Amazon S3),
    // X-Files-Date (Files.com) or X-Goog-Date (Google Cloud Storage), each a date and time such as 20220101T120000Z,
    // plus the X-Amz-Expires, X-Files-Expires or X-Goog-Expires seconds after it; or Azure's se, a UTC date and time
    // such as 2022-01-01T12:00:00Z. Null when the query states no expiry. False when it states one malformed: a
    // parameter given twice, a date without its lifetime or the reverse, a value of the wrong form, or an empty segment
    // in a query that has these parameters. Neither null nor false is an expiry, and none is made up for them.
    public static function urlExpiry($url)
    {
        $query = parse_url((string) $url, PHP_URL_QUERY);
        if (!is_string($query) || $query === '') {
            return null;
        }

        $values = [];
        $emptySegment = false;
        foreach (explode('&', $query) as $segment) {
            if ($segment === '') {
                $emptySegment = true;
                continue;
            }

            $pair = explode('=', $segment, 2);
            $values[rawurldecode($pair[0])][] = count($pair) === 2 ? rawurldecode($pair[1]) : null;
        }

        $found = null;
        foreach ([['X-Amz-Date', 'X-Amz-Expires'], ['X-Files-Date', 'X-Files-Expires'], ['X-Goog-Date', 'X-Goog-Expires']] as $names) {
            list($dateName, $lifetimeName) = $names;
            if (!isset($values[$dateName]) && !isset($values[$lifetimeName])) {
                continue;
            }

            if ($emptySegment || !isset($values[$dateName], $values[$lifetimeName]) || count($values[$dateName]) !== 1 || count($values[$lifetimeName]) !== 1) {
                return false;
            }

            $date = self::compactDate($values[$dateName][0]);
            $lifetime = $values[$lifetimeName][0];
            if ($date === null || !is_string($lifetime) || !preg_match('/\A[0-9]{1,9}\z/', $lifetime)) {
                return false;
            }

            $expiry = $date + (int) $lifetime;
            $found = $found === null ? $expiry : min($found, $expiry);
        }

        if (isset($values['se'])) {
            if ($emptySegment || count($values['se']) !== 1) {
                return false;
            }

            $expiry = self::isoDate($values['se'][0]);
            if ($expiry === null) {
                return false;
            }

            $found = $found === null ? $expiry : min($found, $expiry);
        }

        return $found;
    }

    // 20220101T120000Z as a Unix time, or null.
    private static function compactDate($value)
    {
        if (!is_string($value) || !preg_match('/\A([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2})([0-9]{2})([0-9]{2})Z\z/', $value, $match)) {
            return null;
        }

        return self::utc($match[1], $match[2], $match[3], $match[4], $match[5], $match[6]);
    }

    // 2022-01-01T12:00:00Z, 2022-01-01T12:00Z or 2022-01-01 as a Unix time, or null.
    private static function isoDate($value)
    {
        if (!is_string($value) || !preg_match('/\A([0-9]{4})-([0-9]{2})-([0-9]{2})(?:T([0-9]{2}):([0-9]{2})(?::([0-9]{2}))?Z)?\z/', $value, $match)) {
            return null;
        }

        $hour = isset($match[4]) ? $match[4] : '0';
        $minute = isset($match[5]) ? $match[5] : '0';
        $second = isset($match[6]) && $match[6] !== '' ? $match[6] : '0';
        return self::utc($match[1], $match[2], $match[3], $hour, $minute, $second);
    }

    private static function utc($year, $month, $day, $hour, $minute, $second)
    {
        list($year, $month, $day, $hour, $minute, $second) = array_map('intval', [$year, $month, $day, $hour, $minute, $second]);
        if (!checkdate($month, $day, $year) || $hour > 23 || $minute > 59 || $second > 59) {
            return null;
        }

        return gmmktime($hour, $minute, $second, $month, $day, $year);
    }
}
