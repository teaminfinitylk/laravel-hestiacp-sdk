<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\DTOs\DatabaseDto;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

/**
 * Database management via HestiaCP API.
 *
 * All operations POST to /api/index.php using the v-* CLI commands.
 * Note: HestiaCP prepends the username to the database/dbuser names
 *       (e.g. user "john" + db "mydb" → stored as "john_mydb").
 */
class DatabaseResource
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    /**
     * v-list-databases USER [FORMAT]
     * List all databases for a user.
     *
     * @return DatabaseDto[]
     */
    public function list(string $username): array
    {
        $response = $this->connector->execute('v-list-databases', [$username, 'json'], false);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to list databases: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        $databases = [];
        foreach ($response->all() as $dbName => $data) {
            if (!is_array($data)) {
                continue;
            }
            $databases[] = DatabaseDto::fromArray(array_merge($data, ['DATABASE' => $dbName]));
        }

        return $databases;
    }

    /**
     * v-list-database USER DATABASE [FORMAT]
     * Get details of a single database.
     */
    public function get(string $username, string $database): ?array
    {
        $response = $this->connector->execute('v-list-database', [$username, $database, 'json'], false);

        if ($response->isError()) {
            return null;
        }

        $data = $response->all();
        return $data[$database] ?? (array_values($data)[0] ?? null);
    }

    /**
     * v-add-database USER DATABASE DBUSER DBPASS [TYPE] [HOST] [CHARSET]
     * Create a new database.
     *
     * $data keys: user, database, dbuser, dbpass, type, host, charset
     *
     * Note: HestiaCP will prepend $data['user'].'_' to database and dbuser names.
     */
    public function create(array $data): bool
    {
        $response = $this->connector->execute('v-add-database', [
            $data['user'],
            $data['database'],
            $data['dbuser'],
            $data['dbpass'],
            $data['type']    ?? 'mysql',
            $data['host']    ?? 'localhost',
            $data['charset'] ?? 'utf8mb4',
        ]);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to create database: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return true;
    }

    /**
     * v-change-database-password USER DATABASE DBUSER PASSWORD
     * Change the password for a database user.
     */
    public function changePassword(string $username, string $database, string $dbuser, string $password): bool
    {
        $response = $this->connector->execute('v-change-database-password', [
            $username,
            $database,
            $dbuser,
            $password,
        ]);
        return $response->isSuccessful();
    }

    /**
     * v-suspend-database USER DATABASE
     */
    public function suspend(string $username, string $database): bool
    {
        $response = $this->connector->execute('v-suspend-database', [$username, $database]);
        return $response->isSuccessful();
    }

    /**
     * v-unsuspend-database USER DATABASE
     */
    public function unsuspend(string $username, string $database): bool
    {
        $response = $this->connector->execute('v-unsuspend-database', [$username, $database]);
        return $response->isSuccessful();
    }

    /**
     * v-delete-database USER DATABASE
     */
    public function delete(string $username, string $database): bool
    {
        $response = $this->connector->execute('v-delete-database', [$username, $database]);
        return $response->isSuccessful();
    }
}