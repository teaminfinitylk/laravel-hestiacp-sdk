<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

/**
 * Cron job management via HestiaCP API.
 *
 * All operations POST to /api/index.php using the v-* CLI commands.
 */
class CronResource
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    /**
     * v-list-cron-jobs USER [FORMAT]
     * List all cron jobs for a user.
     */
    public function list(string $username): array
    {
        $response = $this->connector->execute('v-list-cron-jobs', [$username, 'json'], false);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to list cron jobs: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return $response->all();
    }

    /**
     * v-list-cron-job USER JOB [FORMAT]
     * Get a single cron job by its numeric ID.
     */
    public function get(string $username, int $jobId): ?array
    {
        $response = $this->connector->execute('v-list-cron-job', [$username, (string) $jobId, 'json'], false);

        if ($response->isError()) {
            return null;
        }

        $data = $response->all();
        return $data[(string) $jobId] ?? (array_values($data)[0] ?? null);
    }

    /**
     * v-add-cron-job USER MIN HOUR DAY MONTH WDAY COMMAND [JOB] [RESTART]
     * Create a new cron job.
     *
     * $data keys: user, min, hour, day, month, wday, command
     *
     * Use '*' for wildcard values. Example:
     *   min='0', hour='2', day='*', month='*', wday='*', command='/script.sh'
     */
    public function create(array $data): bool
    {
        $response = $this->connector->execute('v-add-cron-job', [
            $data['user'],
            $data['min']     ?? '*',
            $data['hour']    ?? '*',
            $data['day']     ?? '*',
            $data['month']   ?? '*',
            $data['wday']    ?? '*',
            $data['command'],
        ]);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to create cron job: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return true;
    }

    /**
     * v-suspend-cron-job USER JOB
     */
    public function suspend(string $username, int $jobId): bool
    {
        $response = $this->connector->execute('v-suspend-cron-job', [$username, (string) $jobId]);
        return $response->isSuccessful();
    }

    /**
     * v-unsuspend-cron-job USER JOB
     */
    public function unsuspend(string $username, int $jobId): bool
    {
        $response = $this->connector->execute('v-unsuspend-cron-job', [$username, (string) $jobId]);
        return $response->isSuccessful();
    }

    /**
     * v-delete-cron-job USER JOB
     */
    public function delete(string $username, int $jobId): bool
    {
        $response = $this->connector->execute('v-delete-cron-job', [$username, (string) $jobId]);
        return $response->isSuccessful();
    }
}