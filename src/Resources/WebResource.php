<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\Contracts\ResourceInterface;
use TeamInfinityLK\HestiaCP\DTOs\WebDomainDto;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

class WebResource implements ResourceInterface
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    public function list(): array
    {
        $response = $this->connector->get('/api/v1/list/web/');

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to list web domains: ' . $response->get('error', 'Unknown error'));
        }

        return array_map(
            fn(array $data) => WebDomainDto::fromArray($data),
            $response->get('data', [])
        );
    }

    public function get(string $id): ?array
    {
        $response = $this->connector->get('/api/v1/list/web/' . $id);

        if (!$response->isSuccessful()) {
            return null;
        }

        return $response->get('data', [])[0] ?? null;
    }

    public function create(array $data): array
    {
        $response = $this->connector->post('/api/v1/add/web/', $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to create web domain: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function update(string $id, array $data): array
    {
        $response = $this->connector->post('/api/v1/edit/web/' . $id, $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to update web domain: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function delete(string $id): bool
    {
        $response = $this->connector->post('/api/v1/delete/web/', ['domain' => $id]);

        return $response->isSuccessful();
    }
}