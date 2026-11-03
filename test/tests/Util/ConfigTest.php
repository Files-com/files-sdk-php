<?php

declare(strict_types=1);

namespace Files;

use Files\Files;
use Files\Test\UnboundTransportGuard;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    use UnboundTransportGuard;

    public function testConfig()
    {
        Files::setApiKey("test-key");
        Files::setBaseUrl('https://app.files.com');
        $this->assertEquals("test-key", Files::getApiKey());
        $this->assertEquals('https://app.files.com', Files::getBaseUrl());
    }
}
