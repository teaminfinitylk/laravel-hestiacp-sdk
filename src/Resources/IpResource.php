<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\Contracts\ResourceInterface;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

class IpResource implements ResourceInterface
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    public function list(): array
    {
        $response = $this->connector->get('/api/v1/list/ip/');

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to list IPs: ' . $response->get('error', 'Unknown error'));
        }

        return $response->get('data', []);
    }

    public function get(string $id): ?array
    {
        $response = $this->connector->get('/api/v1/list/ip/' . $id);

        if (!$response->isSuccessful()) {
            return null;
        }

        return $response->get('data', [])[0] ?? null;
    }

    public function create(array $data): array
    {
        $response = $this->connector->post('/api/v1/add/ip/', $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to add IP: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function update(string $id, array $data): array
    {
        $response = $this->connector->post('/api/v1/edit/ip/' . $id, $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to update IP: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function delete(string $id): bool
    {
        $response = $this->connector->post('/api/v1/delete/ip/', ['ip' => $id]);

        return $response->isSuccessful();
    }
}