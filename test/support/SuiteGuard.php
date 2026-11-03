<?php

declare(strict_types=1);

namespace Files\Test;

/**
 * Keeps the default suite from reaching anything but the handlers that its tests bind.
 *
 * Before each test (UnboundTransportGuard), the SDK's global settings get a synthetic API key, no session or
 * workspace, the base URL that the SDK had when the suite started, and a handler that refuses every request: a
 * test that sends a request binds its own handler first. After each test, a request that reached the refusing
 * handler fails that test, even if the SDK caught the refusal. Configuration inherited from the environment, such
 * as a real API key, is cleared before the SDK loads; the suite does not start when that cannot be established.
 */
final class SuiteGuard
{
    private static $syntheticApiKey = 'synthetic-test-key';
    private static $defaultBaseUrl = null;
    private static $unboundRequests = [];

    /**
     * Removes the Files.com settings that the environment gives this process, other than FILES_SESSION_ENV,
     * which test.sh sets for the suite, and checks that none is left.
     *
     * @throws \UnexpectedValueException when the environment cannot be listed whole, or a setting is left
     */
    public static function clearInheritedConfiguration()
    {
        // Checked by the names found before clearing: before PHP 7.1 the list comes from the superglobals that
        // the clearing empties, so it would no longer show a setting that getenv() still gives.
        $names = self::inheritedSettingNames();
        foreach ($names as $name) {
            putenv($name);
            unset($_ENV[$name], $_SERVER[$name]);
        }
        $left = array_filter($names, function ($name) {
            return getenv($name) !== false;
        });
        if ($left !== []) {
            throw new \UnexpectedValueException('The environment still gives ' . implode(', ', $left) . '.');
        }
    }

    /**
     * The names of the FILES_* settings in this process's environment, other than FILES_SESSION_ENV.
     *
     * @throws \UnexpectedValueException when PHP cannot list the whole environment
     */
    private static function inheritedSettingNames()
    {
        if (PHP_VERSION_ID >= 70100) {
            $names = array_keys(getenv());
        } elseif (strpbrk((string) ini_get('variables_order'), 'ES') !== false) {
            // Before PHP 7.1, getenv() lists nothing: $_ENV (E) or $_SERVER (S) holds the whole environment.
            $names = array_keys($_ENV + $_SERVER);
        } else {
            throw new \UnexpectedValueException(
                'This PHP cannot list its environment: getenv() lists it only from PHP 7.1, and variables_order '
                    . 'has neither E nor S. Run the suite with variables_order including E or S.'
            );
        }

        return array_values(array_filter(array_unique($names), function ($name) {
            return is_string($name) && stripos($name, 'FILES_') === 0 && $name !== 'FILES_SESSION_ENV';
        }));
    }

    /**
     * Records the SDK's own base URL, once it has loaded, for resetSdkState() to restore.
     */
    public static function recordDefaults()
    {
        self::$defaultBaseUrl = \Files\Files::getBaseUrl();
    }

    public static function resetSdkState()
    {
        \Files\Files::setApiKey(self::$syntheticApiKey);
        \Files\Files::setSessionId(null);
        \Files\Files::setWorkspaceId(null);
        \Files\Files::setBaseUrl(self::$defaultBaseUrl);
        \Files\Files::setHandler(self::refusingHandler());
    }

    /**
     * The requests that reached the refusing handler since the last call, as "METHOD URI" lines.
     */
    public static function takeUnboundRequests()
    {
        $requests = self::$unboundRequests;
        self::$unboundRequests = [];

        return $requests;
    }

    // A Guzzle handler that sends nothing: it records each request and refuses it.
    private static function refusingHandler()
    {
        return function ($request, array $options) {
            $line = $request->getMethod() . ' ' . $request->getUri();
            self::$unboundRequests[] = $line;
            throw new UnboundRequestException(
                "No test handler was bound for $line; the default suite never sends a request anywhere else."
            );
        };
    }
}
