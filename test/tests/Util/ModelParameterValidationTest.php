<?php

declare(strict_types=1);

namespace Files;

use Files\Exception\InvalidParameterException;
use Files\Model\User;
use Files\Test\UnboundTransportGuard;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * A generated model operation checks each parameter it is given against the parameter's type before it sends
 * anything: false is not an integer or a string, but it is a boolean.
 */
class ModelParameterValidationTest extends TestCase
{
    use UnboundTransportGuard;

    private $requests = [];

    public function testMemberOperationRejectsFalseForAnIntegerParameter()
    {
        $this->assertRejectedBeforeRequest('$group_id must be of type int; received boolean', function () {
            (new User(['id' => 1]))->update(['group_id' => false]);
        });
    }

    public function testMemberOperationRejectsFalseForAStringParameter()
    {
        $this->assertRejectedBeforeRequest('$email must be of type string; received boolean', function () {
            (new User(['id' => 1]))->update(['email' => false]);
        });
    }

    public function testCollectionOperationRejectsFalseForAnIntegerParameter()
    {
        $this->assertRejectedBeforeRequest('$per_page must be of type int; received boolean', function () {
            User::all(['per_page' => false]);
        });
    }

    // A boolean parameter takes any scalar the API reads as one, but an array is not a boolean.
    public function testCollectionOperationRejectsAnArrayForABooleanParameter()
    {
        $this->assertRejectedBeforeRequest(
            '$include_parent_site_users must be of type boolean; received array',
            function () {
                User::all(['include_parent_site_users' => ['invalid-container']]);
            }
        );
    }

    // list is the operation's name, but reserved before PHP 7, so the model answers it through __callStatic with all.
    public function testListForwardsItsParametersToAll()
    {
        $this->assertRejectedBeforeRequest('$per_page must be of type int; received boolean', function () {
            call_user_func([User::class, 'list'], ['per_page' => false]);
        });
    }

    // false reaches the API as a JSON false, neither left out nor sent as something else.
    public function testMemberOperationSendsFalseForABooleanParameter()
    {
        $this->recordRequests();

        $user = (new User(['id' => 1]))->update(['disabled' => false]);

        $this->assertCount(1, $this->requests);
        $this->assertSame('PATCH', $this->requests[0]->getMethod());
        $this->assertStringEndsWith('/users/1', $this->requests[0]->getUri()->getPath());
        $sent = json_decode((string) $this->requests[0]->getBody(), true);
        $this->assertTrue(is_array($sent) && array_key_exists('disabled', $sent), 'The request names disabled');
        $this->assertFalse($sent['disabled']);
        $this->assertInstanceOf(User::class, $user);
    }

    // Answers every request as the service would, with the updated user, and records it.
    private function recordRequests()
    {
        $this->requests = [];
        Files::setHandler(function ($request, array $options) {
            $this->requests[] = $request;

            return new FulfilledPromise(
                new Response(200, ['Content-Type' => 'application/json'], '{"id":1,"disabled":false}')
            );
        });
    }

    private function assertRejectedBeforeRequest($message, callable $operation)
    {
        $this->recordRequests();

        try {
            $operation();
            $this->fail("Expected InvalidParameterException: $message");
        } catch (InvalidParameterException $rejected) {
            $this->assertSame($message, $rejected->getMessage());
        }

        $this->assertCount(0, $this->requests);
    }
}
