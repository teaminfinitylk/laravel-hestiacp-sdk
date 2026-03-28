<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\Contracts\ResourceInterface;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

class BackupResource implements ResourceInterface
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    public function list(): array
    {
        $response = $this->connector->get('/api/v1/list/backup/');

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to list backups: ' . $response->get('error', 'Unknown error'));
        }

        return $response->get('data', []);
    }

    public function get(string $id): ?array
    {
        $response = $this->connector->get('/api/v1/list/backup/' . $id);

        if (!$response->isSuccessful()) {
            return null;
        }

        return $response->get('data', [])[0] ?? null;
    }

    public function create(array $data): array
    {
        $response = $this->connector->post('/api/v1/add/backup/', $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to create backup: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function update(string $id, array $data): array
    {
        $response = $this->connector->post('/api/v1/edit/backup/' . $id, $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to update backup: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function delete(string $id): bool
    {
        $response = $this->connector->post('/api/v1/delete/backup/', ['backup' => $id]);

        return $response->isSuccessful();
    }

    public function restore(string $backup): array
    {
        $response = $this->connector->post('/api/v1/restore/backup/', ['backup' => $backup]);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to restore backup: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }
}