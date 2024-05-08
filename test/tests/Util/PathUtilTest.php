<?php

declare(strict_types=1);

namespace Files;

use Files\Util;
use PHPUnit\Framework\TestCase;

class PathUtilTest extends TestCase
{
    public function testEmpty()
    {
        $this->assertEquals(true, Util\PathUtil::same("", ""));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSameProvider()
    {
        // parse your data file however you want
        $json = file_get_contents(__DIR__ . '/../../../shared/normalization_for_comparison_test_data.json');
        $json_items = json_decode($json, true);


        return $json_items;
    }

    /**
     * @dataProvider testSameProvider
     */
    public function testSame($a, $b)
    {
        $this->assertEquals(true, Util\PathUtil::same($a, $b), "PathUtil::same failed for $a==$b");
    }

    public function testSplatOnNormalizeForComparison()
    {
        $this->assertEquals('a/b/c', Util\PathUtil::normalizeForComparison("a", "b", "c"));
    }
}
