<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP;

use TeamInfinityLK\HestiaCP\Auth\ApiKeyAuth;
use TeamInfinityLK\HestiaCP\Auth\CredentialAuth;
use TeamInfinityLK\HestiaCP\Contracts\AuthInterface;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Resources\UserResource;
use TeamInfinityLK\HestiaCP\Resources\WebResource;
use TeamInfinityLK\HestiaCP\Resources\MailResource;
use TeamInfinityLK\HestiaCP\Resources\DatabaseResource;
use TeamInfinityLK\HestiaCP\Resources\DnsResource;
use TeamInfinityLK\HestiaCP\Resources\CronResource;
use TeamInfinityLK\HestiaCP\Resources\BackupResource;
use TeamInfinityLK\HestiaCP\Resources\PackageResource;
use TeamInfinityLK\HestiaCP\Resources\IpResource;
use TeamInfinityLK\HestiaCP\Resources\FirewallResource;

class HestiaClient
{
    private Connector $connector;
    private ?AuthInterface $auth = null;

    public function __construct(
        private readonly string $baseUrl,
        ?int $timeout = 30,
        ?bool $verifySsl = null
    ) {
        $this->connector = new Connector($baseUrl);
        $this->connector->setTimeout($timeout);

        $verifySsl ??= filter_var(getenv('HESTIA_VERIFY_SSL') ?: 'true', FILTER_VALIDATE_BOOLEAN);
        $this->connector->setSslVerify($verifySsl);
    }

    public static function connect(string $baseUrl, string $apiKey): self
    {
        $client = new self($baseUrl);
        $client->authenticateWithApiKey($apiKey);
        return $client;
    }

    public static function connectWithCredentials(string $baseUrl, string $username, string $password): self
    {
        $client = new self($baseUrl);
        $client->authenticateWithCredentials($username, $password);
        return $client;
    }

    public function authenticateWithApiKey(string $apiKey): self
    {
        $this->auth = new ApiKeyAuth($apiKey);
        $this->auth->authenticate($this->connector);
        return $this;
    }

    public function authenticateWithCredentials(string $username, string $password): self
    {
        $this->auth = new CredentialAuth($username, $password);
        $this->auth->authenticate($this->connector);
        return $this;
    }

    public function getConnector(): Connector
    {
        return $this->connector;
    }

    public function users(): UserResource
    {
        return new UserResource($this->connector);
    }

    public function web(): WebResource
    {
        return new WebResource($this->connector);
    }

    public function mail(): MailResource
    {
        return new MailResource($this->connector);
    }

    public function databases(): DatabaseResource
    {
        return new DatabaseResource($this->connector);
    }

    public function dns(): DnsResource
    {
        return new DnsResource($this->connector);
    }

    public function cron(): CronResource
    {
        return new CronResource($this->connector);
    }

    public function backups(): BackupResource
    {
        return new BackupResource($this->connector);
    }

    public function packages(): PackageResource
    {
        return new PackageResource($this->connector);
    }

    public function ips(): IpResource
    {
        return new IpResource($this->connector);
    }

    public function firewall(): FirewallResource
    {
        return new FirewallResource($this->connector);
    }
}