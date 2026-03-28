<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TeamInfinityLK\HestiaCP\Http\Response;

class ResponseTest extends TestCase
{
    public function testCanCreateResponse(): void
    {
        $response = new Response(
            statusCode: 200,
            statusMessage: 'OK',
            body: '{"success": true}',
            headers: ['Content-Type' => 'application/json']
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->statusCode);
        $this->assertEquals('OK', $response->statusMessage);
    }

    public function testIsSuccessfulFor2xx(): void
    {
        $response = new Response(
            statusCode: 200,
            statusMessage: 'OK'
        );

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isError());
    }

    public function testIsErrorFor4xxAnd5xx(): void
    {
        $response = new Response(
            statusCode: 404,
            statusMessage: 'Not Found'
        );

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isError());
    }

    public function testCanParseJsonBody(): void
    {
        $response = new Response(
            statusCode: 200,
            statusMessage: 'OK',
            body: '{"data": ["item1", "item2"]}'
        );

        $this->assertTrue($response->has('data'));
        $this->assertEquals(['item1', 'item2'], $response->get('data'));
    }

    public function testGetWithDefaultValue(): void
    {
        $response = new Response(
            statusCode: 200,
            statusMessage: 'OK',
            body: '{"existing": true}'
        );

        $this->assertEquals('default', $response->get('nonexistent', 'default'));
        $this->assertTrue($response->get('existing', false));
    }

    public function testArrayAccess(): void
    {
        $response = new Response(
            statusCode: 200,
            statusMessage: 'OK',
            body: '{"key": "value"}'
        );

        $this->assertTrue(isset($response['key']));
        $this->assertEquals('value', $response['key']);
    }

    public function testCountable(): void
    {
        $response = new Response(
            statusCode: 200,
            statusMessage: 'OK',
            body: '{"a": 1, "b": 2}'
        );

        $this->assertCount(2, $response);
    }

    public function testIterator(): void
    {
        $response = new Response(
            statusCode: 200,
            statusMessage: 'OK',
            body: '["first", "second", "third"]'
        );

        $values = [];
        foreach ($response as $value) {
            $values[] = $value;
        }

        $this->assertCount(3, $values);
    }
}