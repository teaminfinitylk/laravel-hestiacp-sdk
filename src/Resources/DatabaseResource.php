<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\Contracts\ResourceInterface;
use TeamInfinityLK\HestiaCP\DTOs\DatabaseDto;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

class DatabaseResource implements ResourceInterface
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    public function list(): array
    {
        $response = $this->connector->get('/api/v1/list/db/');

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to list databases: ' . $response->get('error', 'Unknown error'));
        }

        return array_map(
            fn(array $data) => DatabaseDto::fromArray($data),
            $response->get('data', [])
        );
    }

    public function get(string $id): ?array
    {
        $response = $this->connector->get('/api/v1/list/db/' . $id);

        if (!$response->isSuccessful()) {
            return null;
        }

        return $response->get('data', [])[0] ?? null;
    }

    public function create(array $data): array
    {
        $response = $this->connector->post('/api/v1/add/db/', $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to create database: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function update(string $id, array $data): array
    {
        $response = $this->connector->post('/api/v1/edit/db/' . $id, $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to update database: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function delete(string $id): bool
    {
        $response = $this->connector->post('/api/v1/delete/db/', ['database' => $id]);

        return $response->isSuccessful();
    }
}