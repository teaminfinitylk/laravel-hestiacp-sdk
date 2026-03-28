<?php

/**
 * HestiaCP SDK — Cron Job Examples
 * ===================================
 * Demonstrates: list, get, create, suspend, unsuspend, delete
 *
 * Cron schedule uses standard cron format:
 *   min  hour  day  month  weekday
 *   *    *     *    *      *        = every minute
 *   0    2     *    *      *        = daily at 02:00
 *   0    0     1    *      *        = 1st of every month at midnight
 *   30   8     *    *      1        = every Monday at 08:30
 */

require_once __DIR__ . '/../vendor/autoload.php';

use TeamInfinityLK\HestiaCP\HestiaClient;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

$client = HestiaClient::connect(
    'https://your-hestia-server.com:8083',
    'YOUR_ACCESS_KEY_ID:YOUR_SECRET_ACCESS_KEY'
);

$username = 'john';

// ─────────────────────────────────────────────────────────────
// List all cron jobs
// ─────────────────────────────────────────────────────────────
echo "=== Cron Jobs for '{$username}' ===\n";

$jobs = $client->cron()->list($username);

foreach ($jobs as $id => $job) {
    echo sprintf(
        "  ID=%-4s  %s %s %s %s %s  CMD: %s\n",
        $id,
        $job['MIN']   ?? '*',
        $job['HOUR']  ?? '*',
        $job['DAY']   ?? '*',
        $job['MONTH'] ?? '*',
        $job['WDAY']  ?? '*',
        $job['CMD']   ?? ''
    );
}

// ─────────────────────────────────────────────────────────────
// Create cron jobs
// ─────────────────────────────────────────────────────────────
echo "\n=== Create Cron Jobs ===\n";

// Run every day at 2:00 AM
try {
    $client->cron()->create([
        'user'    => $username,
        'min'     => '0',
        'hour'    => '2',
        'day'     => '*',
        'month'   => '*',
        'wday'    => '*',
        'command' => '/home/john/scripts/daily-backup.sh',
    ]);
    echo "Daily backup job created (02:00 AM).\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// Run every hour
try {
    $client->cron()->create([
        'user'    => $username,
        'min'     => '0',
        'hour'    => '*',
        'day'     => '*',
        'month'   => '*',
        'wday'    => '*',
        'command' => 'php /home/john/web/example.com/public_html/artisan schedule:run >> /dev/null 2>&1',
    ]);
    echo "Laravel scheduler job created (every hour).\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// Run every Monday at 8:30 AM
try {
    $client->cron()->create([
        'user'    => $username,
        'min'     => '30',
        'hour'    => '8',
        'day'     => '*',
        'month'   => '*',
        'wday'    => '1',         // 0=Sunday, 1=Monday, ..., 6=Saturday
        'command' => '/home/john/scripts/weekly-report.sh',
    ]);
    echo "Weekly report job created (Monday 08:30).\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// ─────────────────────────────────────────────────────────────
// Get a single cron job
// ─────────────────────────────────────────────────────────────
echo "\n=== Get Cron Job by ID ===\n";

$job = $client->cron()->get($username, 1);

if ($job) {
    echo "Schedule: " . implode(' ', [
        $job['MIN']   ?? '*',
        $job['HOUR']  ?? '*',
        $job['DAY']   ?? '*',
        $job['MONTH'] ?? '*',
        $job['WDAY']  ?? '*',
    ]) . "\n";
    echo "Command : " . ($job['CMD'] ?? 'n/a') . "\n";
}

// ─────────────────────────────────────────────────────────────
// Suspend / Unsuspend a cron job
// ─────────────────────────────────────────────────────────────
echo "\n=== Suspend Cron Job ===\n";

$ok = $client->cron()->suspend($username, 1);
echo $ok ? "Job #1 suspended.\n" : "Failed.\n";

$ok = $client->cron()->unsuspend($username, 1);
echo $ok ? "Job #1 unsuspended.\n" : "Failed.\n";

// ─────────────────────────────────────────────────────────────
// Delete a cron job
// ─────────────────────────────────────────────────────────────
echo "\n=== Delete Cron Job ===\n";

// $ok = $client->cron()->delete($username, 1);
// echo $ok ? "Job #1 deleted.\n" : "Failed.\n";
echo "Delete is commented out for safety.\n";
