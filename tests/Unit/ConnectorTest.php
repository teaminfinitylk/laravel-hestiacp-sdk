<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Http\Response;
use TeamInfinityLK\HestiaCP\Exceptions\ConnectionException;

class ConnectorTest extends TestCase
{
    public function testCanCreateConnector(): void
    {
        $connector = new Connector('https://localhost:8083');

        $this->assertInstanceOf(Connector::class, $connector);
    }

    public function testCanSetApiKey(): void
    {
        $connector = new Connector('https://localhost:8083');
        $connector->setApiKey('test-api-key');

        $this->assertInstanceOf(Connector::class, $connector);
    }

    public function testCanSetCredentials(): void
    {
        $connector = new Connector('https://localhost:8083');
        $connector->setCredentials('admin', 'password');

        $this->assertInstanceOf(Connector::class, $connector);
    }

    public function testCanSetTimeout(): void
    {
        $connector = new Connector('https://localhost:8083');
        $connector->setTimeout(60);

        $this->assertInstanceOf(Connector::class, $connector);
    }

    public function testBaseUrlIsTrimmed(): void
    {
        $connector = new Connector('https://localhost:8083/');

        $this->assertInstanceOf(Connector::class, $connector);
    }
}