<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use TeamInfinityLK\HestiaCP\HestiaClient;

/**
 * @method static HestiaClient connect(string $baseUrl, string $apiKey)
 * @method static HestiaClient connectWithCredentials(string $baseUrl, string $username, string $password)
 * @method static \TeamInfinityLK\HestiaCP\Resources\UserResource users()
 * @method static \TeamInfinityLK\HestiaCP\Resources\WebResource web()
 * @method static \TeamInfinityLK\HestiaCP\Resources\MailResource mail()
 * @method static \TeamInfinityLK\HestiaCP\Resources\DatabaseResource databases()
 * @method static \TeamInfinityLK\HestiaCP\Resources\DnsResource dns()
 * @method static \TeamInfinityLK\HestiaCP\Resources\CronResource cron()
 * @method static \TeamInfinityLK\HestiaCP\Resources\BackupResource backups()
 * @method static \TeamInfinityLK\HestiaCP\Resources\PackageResource packages()
 * @method static \TeamInfinityLK\HestiaCP\Resources\IpResource ips()
 * @method static \TeamInfinityLK\HestiaCP\Resources\FirewallResource firewall()
 *
 * @see \TeamInfinityLK\HestiaCP\HestiaClient
 */
class Hestia extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HestiaClient::class;
    }
}