<?php

declare(strict_types=1);

namespace Files;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class DecimalContractTest extends TestCase
{
    public function testJsonBodyPreservesDecimalStringAndDoubleNumber()
    {
        Files::setApiKey('test-key');

        $history = [];
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{}'),
        ]);

        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));
        Files::setHandler($stack);

        Api::sendRequest('/api_keys', 'POST', [
            'amount' => '1.23',
            'ratio' => 1.23,
        ]);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $decoded = json_decode((string) $request->getBody(), true);

        if (method_exists($this, 'assertIsArray')) {
            $this->assertIsArray($decoded);
        } else {
            $this->assertInternalType('array', $decoded);
        }
        $this->assertSame('1.23', $decoded['amount']);

        if (method_exists($this, 'assertIsFloat')) {
            $this->assertIsFloat($decoded['ratio']);
        } else {
            $this->assertInternalType('float', $decoded['ratio']);
        }
        $this->assertSame(1.23, $decoded['ratio']);
    }
}
