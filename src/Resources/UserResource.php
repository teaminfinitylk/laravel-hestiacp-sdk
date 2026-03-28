<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\DTOs\UserDto;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

class UserResource
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    /**
     * v-list-users [FORMAT]
     * Returns all users as array of UserDto
     */
    public function list(): array
    {
        $response = $this->connector->execute('v-list-users', ['json'], false);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to list users: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        $users = [];
        foreach ($response->all() as $username => $data) {
            if (!is_array($data)) continue;
            $users[] = UserDto::fromArray(array_merge($data, ['USER' => $username]));
        }

        return $users;
    }

    /**
     * v-list-user USER [FORMAT]
     */
    public function get(string $username): ?array
    {
        $response = $this->connector->execute('v-list-user', [$username, 'json'], false);

        if ($response->isError()) {
            return null;
        }

        $data = $response->all();
        // Response is {"username": {...data...}}
        return $data[$username] ?? (array_values($data)[0] ?? null);
    }

    /**
     * v-add-user USER PASSWORD EMAIL [PACKAGE] [NAME] [LASTNAME]
     */
    public function create(array $data): array
    {
        $response = $this->connector->execute('v-add-user', [
            $data['user'],
            $data['password'],
            $data['email'],
            $data['package']  ?? 'default',
            $data['name']     ?? '',
            $data['lastname'] ?? '',
        ]);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to create user: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return $response->all();
    }

    /**
     * v-change-user-password USER PASSWORD
     */
    public function changePassword(string $username, string $password): bool
    {
        $response = $this->connector->execute('v-change-user-password', [$username, $password]);
        return $response->isSuccessful();
    }

    /**
     * v-change-user-contact USER EMAIL
     */
    public function changeContact(string $username, string $email): bool
    {
        $response = $this->connector->execute('v-change-user-contact', [$username, $email]);
        return $response->isSuccessful();
    }

    /**
     * v-change-user-package USER PACKAGE
     */
    public function changePackage(string $username, string $package): bool
    {
        $response = $this->connector->execute('v-change-user-package', [$username, $package]);
        return $response->isSuccessful();
    }

    /**
     * v-delete-user USER
     */
    public function delete(string $username): bool
    {
        $response = $this->connector->execute('v-delete-user', [$username]);
        return $response->isSuccessful();
    }

    /**
     * v-suspend-user USER
     */
    public function suspend(string $username): bool
    {
        $response = $this->connector->execute('v-suspend-user', [$username]);
        return $response->isSuccessful();
    }

    /**
     * v-unsuspend-user USER
     */
    public function unsuspend(string $username): bool
    {
        $response = $this->connector->execute('v-unsuspend-user', [$username]);
        return $response->isSuccessful();
    }
}