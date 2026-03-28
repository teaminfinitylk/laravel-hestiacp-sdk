<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

/**
 * Backup management via HestiaCP API.
 *
 * All operations POST to /api/index.php using the v-* CLI commands.
 */
class BackupResource
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    /**
     * v-list-user-backups USER [FORMAT]
     * List all backups for a user.
     */
    public function list(string $username): array
    {
        $response = $this->connector->execute('v-list-user-backups', [$username, 'json'], false);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to list backups: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return $response->all();
    }

    /**
     * v-list-user-backup USER BACKUP [FORMAT]
     * Get details of a specific backup file.
     */
    public function get(string $username, string $backup): ?array
    {
        $response = $this->connector->execute('v-list-user-backup', [$username, $backup, 'json'], false);

        if ($response->isError()) {
            return null;
        }

        $data = $response->all();
        return $data[$backup] ?? (array_values($data)[0] ?? null);
    }

    /**
     * v-backup-user USER
     * Create a new backup for a user.
     */
    public function create(string $username): bool
    {
        $response = $this->connector->execute('v-backup-user', [$username]);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to create backup: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return true;
    }

    /**
     * v-restore-user USER BACKUP [WEB] [DNS] [MAIL] [DB] [CRON] [UDIR] [NOTIFY]
     * Restore a backup for a user.
     *
     * $options keys: web, dns, mail, db, cron, udir — default 'yes' to restore all
     */
    public function restore(string $username, string $backup, array $options = []): bool
    {
        $response = $this->connector->execute('v-restore-user', [
            $username,
            $backup,
            $options['web']   ?? 'yes',
            $options['dns']   ?? 'yes',
            $options['mail']  ?? 'yes',
            $options['db']    ?? 'yes',
            $options['cron']  ?? 'yes',
            $options['udir']  ?? 'yes',
        ]);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to restore backup: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return true;
    }

    /**
     * v-delete-user-backup USER BACKUP
     */
    public function delete(string $username, string $backup): bool
    {
        $response = $this->connector->execute('v-delete-user-backup', [$username, $backup]);
        return $response->isSuccessful();
    }
}