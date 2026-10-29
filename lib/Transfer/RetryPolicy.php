<?php

declare(strict_types=1);

namespace Files\Transfer;

/**
 * The retry rules of adaptive transfers, kept apart for the Files.com API and for storage (the signed URLs parts and
 * downloads go to), and the expiry of signed URLs.
 *
 * API requests that can be repeated safely (a part's renewal, a later batch of parts, a download's URL) are retried
 * when they get no status (a connection or transfer failure other than TLS, URL or redirect errors), 429 or a 5xx other
 * than 501, waiting 100 ms and doubling the wait for each later retry. The request that begins an upload and the one
 * that ends it keep the SDK's existing rule, retrying only a request that could not connect, as their effect may have
 * happened when an answer is lost: only a failure that shows the request never reached a server (isConnectFailure).
 *
 * Storage requests are retried for no status (with the same exclusions), 5xx other than 501, 429 and 503, and an
 * HTTP 400 whose body is exactly S3's RequestTimeout error. 429 and 503 wait for Retry-After (in seconds or as an HTTP
 * date) when it is given and not at all otherwise; everything else waits 100 ms. A Retry-After longer than
 * MAX_DELAY_SECONDS fails the request instead of being shortened. A part whose grant says retry_parts false is never
 * retried. Every retry counts against Files::$maxNetworkRetries (or the job's max_retries).
 */
final class RetryPolicy
{
    const STORAGE_DELAY = 0.1;
    const API_DELAY = 0.1;
    const MAX_DELAY_SECONDS = 3600;
    // A signed URL due to expire within this many seconds is renewed before it is used.
    const EXPIRY_MARGIN_SECONDS = 15;
    // The most of an error response's body kept to classify it.
    const BODY_LIMIT = 65536;

    // cURL errors that a retry cannot fix: unsupported protocol, malformed URL, too many redirects and TLS failures.
    private static $permanentCurlErrors = [1, 3, 47, 35, 51, 53, 54, 58, 59, 60, 64, 66, 77, 80, 82, 83, 90, 91, 98];

    // What expiryNow reads instead of time() while a test has set it (through reflection); null otherwise.
    private static $expiryClock = null;

    // What to do about a storage response of $status: ['throttle', seconds] (429 or 503), ['retry', seconds] or
    // ['fail', 0.0]. $headers are the response's, $body (at most BODY_LIMIT bytes of it) its body, and $complete whether
    // $body is all of it: a body that was cut short, or went on past what was kept, is never S3's RequestTimeout error.
    public static function storageResponse($status, array $headers, $body, $complete = true)
    {
        if ($status === 429 || $status === 503) {
            $delay = self::retryAfter($headers);
            if ($delay === false) {
                return ['fail', 0.0];
            }

            return ['throttle', $delay === null ? 0.0 : $delay];
        }

        if ($status >= 500 && $status !== 501) {
            return ['retry', self::STORAGE_DELAY];
        }

        if ($status === 400 && $complete === true && self::isRequestTimeout($body)) {
            return ['retry', self::STORAGE_DELAY];
        }

        return ['fail', 0.0];
    }

    // Whether an API response of $status may be retried by a request that can be repeated safely.
    public static function apiStatusRetryable($status)
    {
        return $status === 429 || ($status >= 500 && $status !== 501);
    }

    // The wait before API retry number $retry (1 for the first): 100 ms, doubled for each later retry.
    public static function apiDelay($retry)
    {
        return self::API_DELAY * pow(2, max(0, $retry - 1));
    }

    // The wait before retry number $retry of a request under the SDK's existing connection rule.
    public static function connectDelay($retry, $minimum, $maximum)
    {
        return min($retry * $minimum, $maximum);
    }

    // Whether $error, a request's failure that brought no response, may be retried: true but for TLS, URL and redirect
    // failures.
    public static function networkRetryable($error)
    {
        if ($error instanceof \GuzzleHttp\Exception\TooManyRedirectsException) {
            return false;
        }

        if (!($error instanceof \GuzzleHttp\Exception\TransferException)) {
            return false;
        }

        $errno = null;
        if (method_exists($error, 'getHandlerContext')) {
            $context = $error->getHandlerContext();
            $errno = isset($context['errno']) ? (int) $context['errno'] : null;
        }

        return $errno === null || !in_array($errno, self::$permanentCurlErrors, true);
    }

    // Whether $error, the failure of a request that brought no response, shows that the request never reached a server:
    // the one failure the SDK's existing connection rule lets the request that begins or ends an upload retry. curl
    // could not resolve the proxy or the host or connect to it (errno 5, 6 or 7), or ran out of time (28) before it had
    // sent any of the request (request_size 0, as curl reports it in the handler context), and no hop of its redirect
    // chain was answered ($answered: that server has the request, whatever became of a later hop). The exception's class
    // is no evidence: Guzzle reports a request that was sent and then timed out or got an empty reply as a
    // ConnectException too, though its server may have acted on it. TLS, URL and redirect failures are never retried.
    public static function isConnectFailure($error, $answered = false)
    {
        if ($answered || !($error instanceof \GuzzleHttp\Exception\ConnectException) || !method_exists($error, 'getHandlerContext')) {
            return false;
        }

        $context = $error->getHandlerContext();
        if (!isset($context['errno'], $context['request_size'])) {
            return false;
        }

        return in_array((int) $context['errno'], [5, 6, 7, 28], true) && (int) $context['request_size'] === 0;
    }

    // The SDK's exception for $error, a request for $what (such as "Part 2 of the upload to a.txt") that brought no
    // response: ApiConnectException, ApiTooManyRedirectsException or ApiRequestException, its message without any URL.
    public static function networkError($error, $what)
    {
        $detail = is_object($error) && method_exists($error, 'getMessage') ? self::redact($error->getMessage()) : 'no response';
        $message = $what . ' failed: ' . $detail;
        if ($error instanceof \GuzzleHttp\Exception\ConnectException) {
            return new \Files\Exception\ApiConnectException($message, 0);
        }

        if ($error instanceof \GuzzleHttp\Exception\TooManyRedirectsException) {
            return new \Files\Exception\ApiTooManyRedirectsException($message, 0);
        }

        return new \Files\Exception\ApiRequestException($message, 0);
    }

    // The SDK's exception for storage refusing a request for $what with $status: ApiServerException for a 5xx,
    // ApiBadResponseException otherwise, with the status as its code.
    public static function storageError($what, $status)
    {
        $message = $what . ' was refused by storage (status ' . $status . ')';
        if ($status >= 500) {
            return new \Files\Exception\ApiServerException($message, $status);
        }

        return new \Files\Exception\ApiBadResponseException($message, $status);
    }

    // $text without the URLs in it, such as the signed URL a transport error names.
    public static function redact($text)
    {
        return (string) preg_replace('@[A-Za-z][A-Za-z0-9+.\-]*://[^\s`\'"<>]+@', '[redacted]', (string) $text);
    }

    // Retry-After as seconds to wait: null when there is none (or it is not a number of seconds or an HTTP date), and
    // false when it is longer than MAX_DELAY_SECONDS.
    public static function retryAfter(array $headers)
    {
        $value = null;
        foreach ($headers as $name => $values) {
            if (strcasecmp((string) $name, 'Retry-After') === 0) {
                $value = trim(is_array($values) ? (string) reset($values) : (string) $values);
            }
        }

        if ($value === null || $value === '') {
            return null;
        }

        if (preg_match('/\A[0-9]+\z/', $value)) {
            $seconds = strlen($value) > 9 ? self::MAX_DELAY_SECONDS + 1 : (int) $value;
        } else {
            $time = strtotime($value);
            if ($time === false) {
                return null;
            }

            $seconds = max(0, $time - time());
        }

        return $seconds > self::MAX_DELAY_SECONDS ? false : (float) $seconds;
    }

    // Whether $body is exactly S3's RequestTimeout error: a complete, well-formed XML document of at most BODY_LIMIT
    // bytes whose root is an unnamespaced Error with a direct Code of RequestTimeout. Nothing but an optional XML
    // declaration may come before the root: no DTD, comment, CDATA section or processing instruction anywhere. An end
    // tag carries its name alone; every attribute is named once and quoted; every & begins one of XML's five entities or
    // a character reference to a character XML allows; and no control character but tab, line feed and carriage return
    // appears. A substring, truncated or malformed XML, or any other shape is not. Whether the body was cut short is the
    // caller's to say (storageResponse's $complete): a prefix that happens to be well formed is no evidence of the whole.
    public static function isRequestTimeout($body)
    {
        if (
            !is_string($body) || $body === '' || strlen($body) > self::BODY_LIMIT || strpos($body, '<!') !== false
            || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $body)
        ) {
            return false;
        }

        $text = preg_replace('/\A\xEF\xBB\xBF/', '', $body);
        $text = preg_replace('/\A<\?xml\s+version\s*=\s*(["\'])1\.[0-9]+\1(?:\s+encoding\s*=\s*(["\'])[A-Za-z][A-Za-z0-9._\-]*\2)?(?:\s+standalone\s*=\s*(["\'])(?:yes|no)\3)?\s*\?>/', '', $text, 1);
        if (strpos($text, '<?') !== false || !preg_match('/\A\s*<Error>(.*)<\/Error>\s*\z/s', $text, $match)) {
            return false;
        }

        $inner = $match[1];
        $stack = [];
        $codes = [];
        $position = 0;
        $length = strlen($inner);
        while ($position < $length) {
            $next = strpos($inner, '<', $position);
            $text = $next === false ? substr($inner, $position) : substr($inner, $position, $next - $position);
            if (strpos($text, '>') !== false || !self::wellFormedText($text)) {
                return false;
            }

            if ($next === false) {
                break;
            }

            if (!preg_match('/\G<(\/?)([A-Za-z_][A-Za-z0-9_.\-]*)(\s+[^<>]*?)?(\/?)>/', $inner, $tag, 0, $next)) {
                return false;
            }

            $closing = $tag[1] === '/';
            $name = $tag[2];
            $attributes = isset($tag[3]) ? $tag[3] : '';
            $selfClosing = isset($tag[4]) && $tag[4] === '/';
            $end = $next + strlen($tag[0]);
            if ($closing) {
                // An end tag is its name alone: nothing but white space may follow it before its >.
                if ($selfClosing || !$stack || trim($attributes) !== '') {
                    return false;
                }

                $open = array_pop($stack);
                if ($open[0] !== $name) {
                    return false;
                }

                if (!$stack && $name === 'Code') {
                    $codes[] = $open[2] ? null : substr($inner, $open[1], $next - $open[1]);
                }
            } elseif (!self::wellFormedAttributes($attributes)) {
                return false;
            } elseif ($selfClosing) {
                if (!$stack && $name === 'Code') {
                    $codes[] = '';
                }
            } else {
                $stack[] = [$name, $end, trim($attributes) !== ''];
            }

            $position = $end;
        }

        return !$stack && count($codes) === 1 && $codes[0] === 'RequestTimeout';
    }

    // Whether $text, character data or an attribute's value, uses & only to begin one of XML's five predefined entities
    // or a character reference, decimal or hexadecimal, to a character XML allows (its Char: tab, line feed, carriage
    // return, U+0020 to U+D7FF, U+E000 to U+FFFD and U+10000 to U+10FFFF; never U+0000, a surrogate, U+FFFE or U+FFFF).
    private static function wellFormedText($text)
    {
        if (preg_match('/&(?!(?:lt|gt|amp|apos|quot|#[0-9]+|#x[0-9A-Fa-f]+);)/', $text)) {
            return false;
        }

        preg_match_all('/&#(x?)([0-9A-Fa-f]+);/', $text, $references, PREG_SET_ORDER);
        foreach ($references as $reference) {
            // Leading zeros name the same character; more digits than the highest character has are beyond it.
            $hex = $reference[1] === 'x';
            $digits = ltrim($reference[2], '0');
            if (strlen($digits) > ($hex ? 6 : 7)) {
                return false;
            }

            $code = $digits === '' ? 0 : ($hex ? (int) hexdec($digits) : (int) $digits);
            if (
                $code !== 0x9 && $code !== 0xA && $code !== 0xD && ($code < 0x20 || $code > 0xD7FF) && ($code < 0xE000 || $code > 0xFFFD)
                && ($code < 0x10000 || $code > 0x10FFFF)
            ) {
                return false;
            }
        }

        return true;
    }

    // Whether $attributes, what follows a start tag's name, are white space alone or attributes each named once, with a
    // quoted, well-formed value.
    private static function wellFormedAttributes($attributes)
    {
        if (trim($attributes) === '') {
            return true;
        }

        $pair = '\s+([A-Za-z_][A-Za-z0-9_.\-]*)\s*=\s*("[^"<]*"|\'[^\'<]*\')';
        if (!preg_match('/\A(?:' . $pair . ')+\s*\z/', $attributes) || !preg_match_all('/' . $pair . '/', $attributes, $pairs)) {
            return false;
        }

        foreach ($pairs[2] as $value) {
            if (!self::wellFormedText($value)) {
                return false;
            }
        }

        return count(array_unique($pairs[1])) === count($pairs[1]);
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

    // Whether a URL that stops working at $expiresAt (a Unix time, or null for unknown) is due for renewal now.
    public static function isExpiring($expiresAt)
    {
        return $expiresAt !== null && self::expiryNow() + self::EXPIRY_MARGIN_SECONDS >= $expiresAt;
    }

    // Whether a URL that stops working at $expiresAt has stopped working.
    public static function isExpired($expiresAt)
    {
        return $expiresAt !== null && self::expiryNow() >= $expiresAt;
    }

    // The Unix time isExpiring and isExpired judge expiries by: time(), unless a test has set $expiryClock, a callable
    // giving it, which there is no public way to do.
    private static function expiryNow()
    {
        return self::$expiryClock === null ? time() : (int) call_user_func(self::$expiryClock);
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
