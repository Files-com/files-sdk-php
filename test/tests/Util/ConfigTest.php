<?php declare(strict_types=1);

use Files\Files;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{

  public function testConfig()
  {
    Files::setApiKey("test-key");
    Files::setBaseUrl('https://app.files.com');
    $this->assertEquals("test-key", Files::getApiKey());
    $this->assertEquals('https://app.files.com', Files::getBaseUrl());
  }
}
