<?php

declare(strict_types=1);

namespace Files;

use Files\Test\Fixtures;
use Files\Test\UnboundTransportGuard;
use Files\Util;
use PHPUnit\Framework\TestCase;

class PathUtilTest extends TestCase
{
    use UnboundTransportGuard;

    public function testEmpty()
    {
        $this->assertEquals(true, Util\PathUtil::same("", ""));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSameProvider()
    {
        return Fixtures::requiredJsonArray(__DIR__ . '/../../../shared/normalization_for_comparison_test_data.json');
    }

    /**
     * @dataProvider testSameProvider
     */
    public function testSame($a, $b)
    {
        $this->assertSame($b, Util\PathUtil::normalizeForComparison($a));
        $this->assertSame($b, Util\PathUtil::normalizeForComparison($b));
        $this->assertEquals(true, Util\PathUtil::same($a, $b), "PathUtil::same failed for $a==$b");
    }

    public function testServerComparisonExamples()
    {
        $examples = Fixtures::requiredJsonArray(__DIR__ . '/../../../shared/comparison_examples.json');
        foreach ($examples as $pair) {
            $this->assertSame($pair[1], Util\PathUtil::normalizeForComparison($pair[0]));
        }
    }

    public function testDistinctNumericPathNames()
    {
        $this->assertFalse(Util\PathUtil::same("0e1", "0e2"));
        $this->assertFalse(Util\PathUtil::same("0", "00"));
    }

    public function testSplatOnNormalizeForComparison()
    {
        $this->assertEquals('a/b/c', Util\PathUtil::normalizeForComparison("a", "b", "c"));
    }

    public function testNormalizePreservesPathIdentity()
    {
        $this->assertEquals('remote/path/to/file.txt', Util\PathUtil::normalize("/../../remote\\path//./to/file.txt"));
        $this->assertEquals('remote/path/to/file.txt', Util\PathUtil::normalize('remote/../path/to/file.txt'));
        $this->assertEquals(
            '_/RemoteServers/42/remote/path/to/file.txt',
            Util\PathUtil::normalize('_', 'RemoteServers', '42', "/../../remote\\path//./to/file.txt")
        );
    }
}
