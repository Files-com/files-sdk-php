<?php

declare(strict_types=1);

namespace Files;

use Files\Api;
use Files\ApiRequestException;
use Files\ApiServerException;
use Files\Model\ApiKey;
use Files\Model\Bundle;
use Files\Model\Folder;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
  public function testListApiKeys()
  {
    Files::setApiKey('test-key');
    $mock = new MockHandler([
      new Response(200, ['Content-Type' => 'application/json', 'X-Files-Cursor' => '1234'], '[{"id": 1},{"id": 2}]'),
      new Response(200, ['Content-Type' => 'application/json'], '[{"id": 3},{"id": 4}]')
    ]);
    Files::setHandler($mock);

    $items = ApiKey::all(['per_page' => 2]);

    $this->assertEquals(count($items), 4);
    $this->assertEquals($items[0]->id, 1);
    $this->assertEquals($items[1]->id, 2);
    $this->assertEquals($items[2]->id, 3);
    $this->assertEquals($items[3]->id, 4);
  }

  public function testNoResponse()
  {
    Files::setApiKey('test-key');
    $mock = new MockHandler([
      new Response(200, [], ''),
    ]);
    Files::setHandler($mock);

    $bundle = new Bundle(['id' => 1]);

    $this->assertNull($bundle->share());
  }

  public function testEmptyResponse()
  {
    Files::setApiKey('test-key');
    $mock = new MockHandler([
      new Response(200, ['Content-Type' => 'application/json'], '[]'),
    ]);
    Files::setHandler($mock);

    $items = ApiKey::all();

    $this->assertEquals(count($items), 0);
  }

  public function testBadGateway()
  {
    Files::setApiKey('test-key');
    $mock = new MockHandler([
      new Response(502, [], '<html><head><title>502 Bad Gateway</title></head><body><center><h1>502 Bad Gateway</h1></center><hr><center>files.com</center></body></html>')
    ]);
    Files::setHandler($mock);

    $this->expectException(ApiServerException::class);
    $this->expectExceptionCode(502);
    $this->expectExceptionMessage('Bad Gateway');
    ApiKey::all();
  }

  public function testRequestException()
  {
    Files::setApiKey('test-key');
    $error = 'cURL error 60: SSL certificate problem: unable to get local issuer certificate';
    $mock = new MockHandler([
      new RequestException($error, new Request('GET', '/api_keys'))
    ]);
    Files::setHandler($mock);

    $this->expectException(ApiRequestException::class);
    $this->expectExceptionCode(0);
    $this->expectExceptionMessage($error);
    ApiKey::all();
  }

  public function testRegionMismatch()
  {
    Files::setApiKey('test-key');
    $mock = new MockHandler([
      new Response(403, [], '{
        "data": {
          "host": "test.host"
        },
        "error": "You have connected to a URL that has different security settings than those required for your site.",
        "http-code": 403,
        "title": "Lockout Region Mismatch",
        "type": "not-authenticated/lockout-region-mismatch"
      }')
    ]);
    Files::setHandler($mock);

    try {
      ApiKey::all();
      $this->fail('Expected exception was not thrown');
    } catch (\Files\NotAuthenticated\LockoutRegionMismatchException $error) {
      $this->assertEquals($error->getError(), 'You have connected to a URL that has different security settings than those required for your site.');
      $this->assertEquals($error->getType(), 'not-authenticated/lockout-region-mismatch');
      $this->assertEquals($error->getTitle(), 'Lockout Region Mismatch');
      $this->assertEquals($error->getData()->host, 'test.host');
      $this->assertEquals($error->getHttpCode(), 403);
    }
  }

  public function testNotFound()
  {
    Files::setApiKey('test-key');
    $mock = new MockHandler([
      new Response(404, [], '{
        "type": "not-found/folder-not-found",
        "http-code": 404,
        "error": "Folder missing not found."
      }')
    ]);
    Files::setHandler($mock);

    try {
      Folder::listFor('/missing', [ 'sort_by' => [ 'path' => 'desc' ] ]);
      $this->fail('Expected exception was not thrown');
    } catch (\Files\NotFound\FolderNotFoundException $error) {
      $this->assertEquals($error->getError(), 'Folder missing not found.');
      $this->assertEquals($error->getType(), 'not-found/folder-not-found');
      $this->assertEquals($error->getHttpCode(), 404);
    }
  }
}