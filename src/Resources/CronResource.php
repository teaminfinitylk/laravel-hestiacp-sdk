<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\Contracts\ResourceInterface;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

class CronResource implements ResourceInterface
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    public function list(): array
    {
        $response = $this->connector->get('/api/v1/list/cron/');

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to list cron jobs: ' . $response->get('error', 'Unknown error'));
        }

        return $response->get('data', []);
    }

    public function get(string $id): ?array
    {
        $response = $this->connector->get('/api/v1/list/cron/' . $id);

        if (!$response->isSuccessful()) {
            return null;
        }

        return $response->get('data', [])[0] ?? null;
    }

    public function create(array $data): array
    {
        $response = $this->connector->post('/api/v1/add/cron/', $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to create cron job: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function update(string $id, array $data): array
    {
        $response = $this->connector->post('/api/v1/edit/cron/' . $id, $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to update cron job: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function delete(string $id): bool
    {
        $response = $this->connector->post('/api/v1/delete/cron/', ['job_id' => $id]);

        return $response->isSuccessful();
    }
}