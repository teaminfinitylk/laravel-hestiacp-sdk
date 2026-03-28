<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\Contracts\ResourceInterface;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

class FirewallResource implements ResourceInterface
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    public function list(): array
    {
        $response = $this->connector->get('/api/v1/list/firewall/');

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to list firewall rules: ' . $response->get('error', 'Unknown error'));
        }

        return $response->get('data', []);
    }

    public function get(string $id): ?array
    {
        $response = $this->connector->get('/api/v1/list/firewall/' . $id);

        if (!$response->isSuccessful()) {
            return null;
        }

        return $response->get('data', [])[0] ?? null;
    }

    public function create(array $data): array
    {
        $response = $this->connector->post('/api/v1/add/firewall/', $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to create firewall rule: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function update(string $id, array $data): array
    {
        $response = $this->connector->post('/api/v1/edit/firewall/' . $id, $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to update firewall rule: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function delete(string $id): bool
    {
        $response = $this->connector->post('/api/v1/delete/firewall/', ['rule_id' => $id]);

        return $response->isSuccessful();
    }

    public function listBans(): array
    {
        $response = $this->connector->get('/api/v1/list/firewall/ban/');

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to list firewall bans: ' . $response->get('error', 'Unknown error'));
        }

        return $response->get('data', []);
    }

    public function banIp(string $ip): array
    {
        $response = $this->connector->post('/api/v1/add/firewall/ban/', ['ip' => $ip]);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to ban IP: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function unbanIp(string $ip): bool
    {
        $response = $this->connector->post('/api/v1/delete/firewall/ban/', ['ip' => $ip]);

        return $response->isSuccessful();
    }
}