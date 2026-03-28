<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\Contracts\ResourceInterface;
use TeamInfinityLK\HestiaCP\DTOs\UserDto;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

class UserResource implements ResourceInterface
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    public function list(): array
    {
        $response = $this->connector->get('/api/v1/list/user/');

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to list users: ' . $response->get('error', 'Unknown error'));
        }

        return array_map(
            fn(array $data) => UserDto::fromArray($data),
            $response->get('data', [])
        );
    }

    public function get(string $id): ?array
    {
        $response = $this->connector->get('/api/v1/list/user/' . $id);

        if (!$response->isSuccessful()) {
            return null;
        }

        return $response->get('data', [])[0] ?? null;
    }

    public function create(array $data): array
    {
        $response = $this->connector->post('/api/v1/add/user/', $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to create user: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function update(string $id, array $data): array
    {
        $response = $this->connector->post('/api/v1/edit/user/' . $id, $data);

        if (!$response->isSuccessful()) {
            throw new ApiException('Failed to update user: ' . $response->get('error', 'Unknown error'));
        }

        return $response->all();
    }

    public function delete(string $id): bool
    {
        $response = $this->connector->post('/api/v1/delete/user/', ['user' => $id]);

        return $response->isSuccessful();
    }
}