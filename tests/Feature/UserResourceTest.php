<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Tests\Feature;

use PHPUnit\Framework\TestCase;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Http\Response;
use TeamInfinityLK\HestiaCP\Resources\UserResource;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

/**
 * Tests for UserResource.
 *
 * HestiaCP v-list-users returns JSON keyed by username:
 *   {"admin": {"CONTACT": "...", "PACKAGE": "...", ...}, "user1": {...}}
 *
 * UserResource uses execute() — the connector posts to /api/index.php.
 */
class UserResourceTest extends TestCase
{
    // ──────────────────────────────────────────────────────────────
    // list()
    // ──────────────────────────────────────────────────────────────

    public function testListUsersReturnsArrayOfUserDtos(): void
    {
        $connector = $this->createMock(Connector::class);
        $connector
            ->expects($this->once())
            ->method('execute')
            ->with('v-list-users', ['json'])
            ->willReturn(new Response(
                statusCode: 200,
                statusMessage: '',
                body: json_encode([
                    'admin' => [
                        'CONTACT'  => 'admin@example.com',
                        'PACKAGE'  => 'default',
                        'SUSPENDED'=> 'no',
                    ],
                    'user1' => [
                        'CONTACT'  => 'user1@example.com',
                        'PACKAGE'  => 'default',
                        'SUSPENDED'=> 'no',
                    ],
                ])
            ));

        $resource = new UserResource($connector);
        $users    = $resource->list();

        $this->assertCount(2, $users);
        $this->assertEquals('admin', $users[0]->user);
        $this->assertEquals('user1', $users[1]->user);
    }

    public function testListThrowsApiExceptionOnHestiaError(): void
    {
        $connector = $this->createMock(Connector::class);
        $connector
            ->method('execute')
            ->willReturn(new Response(
                statusCode: 200,
                statusMessage: '',
                body: '10', // hestia code 10 = E_FORBIDDEN
            ));

        $resource = new UserResource($connector);

        $this->expectException(ApiException::class);
        $resource->list();
    }

    // ──────────────────────────────────────────────────────────────
    // get()
    // ──────────────────────────────────────────────────────────────

    public function testGetUserReturnsDataArray(): void
    {
        $connector = $this->createMock(Connector::class);
        $connector
            ->method('execute')
            ->with('v-list-user', ['admin', 'json'])
            ->willReturn(new Response(
                statusCode: 200,
                statusMessage: '',
                body: json_encode([
                    'admin' => [
                        'CONTACT' => 'admin@example.com',
                        'PACKAGE' => 'default',
                    ],
                ])
            ));

        $resource = new UserResource($connector);
        $user     = $resource->get('admin');

        $this->assertNotNull($user);
        $this->assertEquals('admin@example.com', $user['CONTACT']);
    }

    public function testGetUserReturnsNullOnError(): void
    {
        $connector = $this->createMock(Connector::class);
        $connector
            ->method('execute')
            ->willReturn(new Response(
                statusCode: 200,
                statusMessage: '',
                body: '3', // E_NOTEXIST
            ));

        $resource = new UserResource($connector);
        $this->assertNull($resource->get('nonexistent'));
    }

    // ──────────────────────────────────────────────────────────────
    // create()
    // ──────────────────────────────────────────────────────────────

    public function testCreateUserReturnsTrueOnSuccess(): void
    {
        $connector = $this->createMock(Connector::class);
        $connector
            ->method('execute')
            ->with('v-add-user', $this->anything())
            ->willReturn(new Response(
                statusCode: 200,
                statusMessage: '',
                body: '0', // hestia OK
            ));

        $resource = new UserResource($connector);
        $result   = $resource->create([
            'user'     => 'newuser',
            'password' => 'password123',
            'email'    => 'newuser@example.com',
        ]);

        $this->assertIsArray($result);
    }

    public function testCreateUserThrowsApiExceptionOnError(): void
    {
        $connector = $this->createMock(Connector::class);
        $connector
            ->method('execute')
            ->willReturn(new Response(
                statusCode: 200,
                statusMessage: '',
                body: '4', // E_EXISTS
            ));

        $resource = new UserResource($connector);

        $this->expectException(ApiException::class);
        $resource->create([
            'user'     => 'admin',
            'password' => 'pass',
            'email'    => 'admin@example.com',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // delete()
    // ──────────────────────────────────────────────────────────────

    public function testDeleteUserReturnsTrueOnSuccess(): void
    {
        $connector = $this->createMock(Connector::class);
        $connector
            ->method('execute')
            ->with('v-delete-user', ['testuser'])
            ->willReturn(new Response(
                statusCode: 200,
                statusMessage: '',
                body: '0',
            ));

        $resource = new UserResource($connector);
        $this->assertTrue($resource->delete('testuser'));
    }

    // ──────────────────────────────────────────────────────────────
    // suspend / unsuspend
    // ──────────────────────────────────────────────────────────────

    public function testSuspendUserReturnsTrueOnSuccess(): void
    {
        $connector = $this->createMock(Connector::class);
        $connector
            ->method('execute')
            ->with('v-suspend-user', ['user1'])
            ->willReturn(new Response(statusCode: 200, statusMessage: '', body: '0'));

        $this->assertTrue((new UserResource($connector))->suspend('user1'));
    }

    public function testUnsuspendUserReturnsTrueOnSuccess(): void
    {
        $connector = $this->createMock(Connector::class);
        $connector
            ->method('execute')
            ->with('v-unsuspend-user', ['user1'])
            ->willReturn(new Response(statusCode: 200, statusMessage: '', body: '0'));

        $this->assertTrue((new UserResource($connector))->unsuspend('user1'));
    }
}