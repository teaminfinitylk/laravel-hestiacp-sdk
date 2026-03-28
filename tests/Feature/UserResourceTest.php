<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Tests\Feature;

use PHPUnit\Framework\TestCase;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Http\Response;
use TeamInfinityLK\HestiaCP\Resources\UserResource;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

class UserResourceTest extends TestCase
{
    public function testListUsers(): void
    {
        $connector = $this->createMock(Connector::class);
        $connector->method('get')
            ->willReturn(new Response(
                statusCode: 200,
                statusMessage: 'OK',
                body: json_encode([
                    'data' => [
                        ['USER' => 'admin', 'CONTACT' => 'admin@example.com'],
                        ['USER' => 'user1', 'CONTACT' => 'user1@example.com'],
                    ]
                ])
            ));

        $resource = new UserResource($connector);
        $users = $resource->list();

        $this->assertCount(2, $users);
        $this->assertEquals('admin', $users[0]->user);
    }

    public function testGetUser(): void
    {
        $connector = $this->createMock(Connector::class);
        $connector->method('get')
            ->willReturn(new Response(
                statusCode: 200,
                statusMessage: 'OK',
                body: json_encode([
                    'data' => [
                        ['USER' => 'admin', 'CONTACT' => 'admin@example.com'],
                    ]
                ])
            ));

        $resource = new UserResource($connector);
        $user = $resource->get('admin');

        $this->assertNotNull($user);
    }

    public function testListThrowsExceptionOnError(): void
    {
        $connector = $this->createMock(Connector::class);
        $connector->method('get')
            ->willReturn(new Response(
                statusCode: 400,
                statusMessage: 'Bad Request',
                body: json_encode(['error' => 'Invalid request'])
            ));

        $resource = new UserResource($connector);

        $this->expectException(ApiException::class);
        $resource->list();
    }

    public function testCreateUser(): void
    {
        $connector = $this->createMock(Connector::class);
        $connector->method('post')
            ->willReturn(new Response(
                statusCode: 200,
                statusMessage: 'OK',
                body: json_encode(['result' => 'OK'])
            ));

        $resource = new UserResource($connector);
        $result = $resource->create([
            'user' => 'newuser',
            'password' => 'password123',
            'email' => 'newuser@example.com'
        ]);

        $this->assertEquals('OK', $result['result'] ?? null);
    }

    public function testDeleteUser(): void
    {
        $connector = $this->createMock(Connector::class);
        $connector->method('post')
            ->willReturn(new Response(
                statusCode: 200,
                statusMessage: 'OK',
                body: json_encode(['result' => 'OK'])
            ));

        $resource = new UserResource($connector);
        $result = $resource->delete('admin');

        $this->assertTrue($result);
    }
}