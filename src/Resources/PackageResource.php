<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

/**
 * Hosting package management via HestiaCP API.
 *
 * All operations POST to /api/index.php using the v-* CLI commands.
 *
 * Note: Package creation/update requires writing a temp file on the server.
 *       There is no single-command way to create packages via the API;
 *       that must typically be done through the panel UI or SSH.
 *       This resource exposes the read operations which are fully API-accessible.
 */
class PackageResource
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    /**
     * v-list-user-packages [FORMAT]
     * List all hosting packages defined on the server.
     */
    public function list(): array
    {
        $response = $this->connector->execute('v-list-user-packages', ['json'], false);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to list packages: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return $response->all();
    }

    /**
     * v-list-user-package PACKAGE [FORMAT]
     * Get details of a specific hosting package.
     */
    public function get(string $package): ?array
    {
        $response = $this->connector->execute('v-list-user-package', [$package, 'json'], false);

        if ($response->isError()) {
            return null;
        }

        $data = $response->all();
        return $data[$package] ?? (array_values($data)[0] ?? null);
    }

    /**
     * v-delete-user-package PACKAGE
     * Delete a hosting package.
     *
     * The package must not be in use by any active user accounts.
     */
    public function delete(string $package): bool
    {
        $response = $this->connector->execute('v-delete-user-package', [$package]);
        return $response->isSuccessful();
    }
}