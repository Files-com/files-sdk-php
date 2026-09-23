<?php

declare(strict_types=1);

namespace Files\Util;

use Error;

/**
 * Class PathUtil
 *
 * @package Files
 */
class PathUtil
{
    private static $nullByte = '/\x00{1,}/';
    private static $leadingAndTrailingSlashes = '/(\/)*$|^(\/)*/';
    private static $twoOrMoreSlashes = '/(\/){2,}/';
    private static $comparisonMap;

    /**
     * @param string[] $args
     * @return string
     */
    public static function normalizeForComparison(...$args)
    {
        if (self::$comparisonMap === null) {
            $data = json_decode(file_get_contents(__DIR__ . '/../../shared/path_comparison.json'), true);
            self::$comparisonMap = [];
            foreach ($data['mapping'] as $hex => $replacement) {
                $character = mb_convert_encoding(pack('N', hexdec(strval($hex))), 'UTF-8', 'UTF-32BE');
                self::$comparisonMap[$character] = $replacement;
            }
        }
        return preg_replace_callback('/[^ -@\[-~]/u', function ($match) {
            return isset(self::$comparisonMap[$match[0]]) ? self::$comparisonMap[$match[0]] : $match[0];
        }, self::normalize($args));
    }

    /**
     * @param string $a
     * @param string $b
     * @return boolean
     */
    public static function same($a, $b)
    {
        return self::normalizeForComparison($a) === self::normalizeForComparison($b);
    }

    public static function cleanpath($path)
    {
        $path = preg_replace(self::$nullByte, "", $path);
        $path = str_replace('\\', '/', $path);
        $path = preg_replace(self::$leadingAndTrailingSlashes, "", $path);
        $path = preg_replace(self::$twoOrMoreSlashes, addslashes("/"), $path);
        if ($path == "." || $path == "..") {
            return "";
        }
        return $path;
    }

    public static function normalize(...$args)
    {
        $allPaths = [];
        if (count($args) == 1 && is_array($args[0])) {
            $args = $args[0];
        }

        foreach ($args as $arg) {
            $paths = explode('/', str_replace('\\', '/', strval($arg)));
            foreach ($paths as $path) {
                $path = self::u8($path);
                $path = preg_replace(self::$nullByte, "", $path);
                $path = self::cleanpath($path);
                if ($path != null && strlen($path) > 0) {
                    $allPaths[] = $path;
                }
            }
        }

        return implode('/', $allPaths);
    }

    private static function u8($str)
    {
        try {
            return mb_convert_encoding($str, 'UTF-8');
        } catch (Error $e) {
            // NOOP
        }
    }
}
