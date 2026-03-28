<?php

/**
 * HestiaCP SDK — Backup Examples
 * =================================
 * Demonstrates: list, get, create backup, restore, delete
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
// List all backups for a user
// ─────────────────────────────────────────────────────────────
echo "=== Backups for '{$username}' ===\n";

$backups = $client->backups()->list($username);

foreach ($backups as $filename => $info) {
    echo sprintf(
        "  %-40s  size=%s MB  date=%s %s\n",
        $filename,
        $info['SIZE'] ?? '?',
        $info['DATE'] ?? '?',
        $info['TIME'] ?? '?'
    );
}

echo "Total: " . count($backups) . " backup(s).\n";

// ─────────────────────────────────────────────────────────────
// Get details of one backup
// ─────────────────────────────────────────────────────────────
echo "\n=== Get Backup Details ===\n";

// Use actual backup filename from your list above
$backupFile = 'admin.2024-01-15_02-00-00.tar';
$data = $client->backups()->get($username, $backupFile);

if ($data) {
    echo "File : " . ($data['BACKUP']   ?? 'n/a') . "\n";
    echo "Size : " . ($data['SIZE']     ?? 'n/a') . " MB\n";
    echo "Date : " . ($data['DATE']     ?? 'n/a') . "\n";
    echo "Time : " . ($data['TIME']     ?? 'n/a') . "\n";
} else {
    echo "Backup not found.\n";
}

// ─────────────────────────────────────────────────────────────
// Create a backup now (runs immediately on server)
// ─────────────────────────────────────────────────────────────
echo "\n=== Create Backup ===\n";

try {
    $client->backups()->create($username);
    echo "Backup triggered for '{$username}'. Check the list in a moment.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// ─────────────────────────────────────────────────────────────
// Restore from a backup
// ─────────────────────────────────────────────────────────────
echo "\n=== Restore Backup ===\n";

// CAUTION: This overwrites current data!
// $backupFile = 'admin.2024-01-15_02-00-00.tar';

// try {
//     $client->backups()->restore($username, $backupFile, [
//         'web'  => 'yes',   // restore web domains
//         'mail' => 'yes',   // restore mail domains
//         'db'   => 'yes',   // restore databases
//         'dns'  => 'yes',   // restore DNS zones
//         'cron' => 'yes',   // restore cron jobs
//         'udir' => 'no',    // restore home directory files (optional)
//     ]);
//     echo "Restore started.\n";
// } catch (ApiException $e) {
//     echo "Restore failed: " . $e->getMessage() . "\n";
// }

echo "Restore is commented out for safety. Uncomment when needed.\n";

// ─────────────────────────────────────────────────────────────
// Delete a backup
// ─────────────────────────────────────────────────────────────
echo "\n=== Delete Backup ===\n";

// $ok = $client->backups()->delete($username, $backupFile);
// echo $ok ? "Backup deleted.\n" : "Failed.\n";
echo "Delete is commented out for safety.\n";
