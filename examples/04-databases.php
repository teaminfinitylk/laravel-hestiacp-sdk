<?php

/**
 * HestiaCP SDK — Database Examples
 * ===================================
 * Demonstrates: list, get, create, change password,
 *               suspend, unsuspend, delete
 *
 * IMPORTANT: HestiaCP automatically prefixes database and db user names
 * with the owner's username + underscore.
 * Example: user='john', database='blog' → stored as 'john_blog'
 *          user='john', dbuser='bloguser' → stored as 'john_bloguser'
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
// List all databases for a user
// ─────────────────────────────────────────────────────────────
echo "=== Databases for '{$username}' ===\n";

$databases = $client->databases()->list($username);

foreach ($databases as $db) {
    echo sprintf(
        "  %-20s  type=%-6s  host=%-12s  disk=%s MB\n",
        $db->database,
        $db->type,
        $db->host,
        $db->diskUsage ?? '0'
    );
}

echo "Total: " . count($databases) . " databases.\n";

// ─────────────────────────────────────────────────────────────
// Get a single database
// ─────────────────────────────────────────────────────────────
echo "\n=== Get Single Database ===\n";

$data = $client->databases()->get($username, 'john_blog');

if ($data) {
    echo "Name    : " . ($data['DATABASE'] ?? 'n/a') . "\n";
    echo "DB User : " . ($data['DBUSER']   ?? 'n/a') . "\n";
    echo "Host    : " . ($data['HOST']     ?? 'n/a') . "\n";
    echo "Type    : " . ($data['TYPE']     ?? 'n/a') . "\n";
    echo "Charset : " . ($data['CHARSET']  ?? 'n/a') . "\n";
} else {
    echo "Database not found.\n";
}

// ─────────────────────────────────────────────────────────────
// Create a database
// ─────────────────────────────────────────────────────────────
echo "\n=== Create Database ===\n";

try {
    $client->databases()->create([
        'user'     => $username,
        'database' => 'blog',          // stored as john_blog
        'dbuser'   => 'bloguser',      // stored as john_bloguser
        'dbpass'   => 'DbPass123!',
        'type'     => 'mysql',         // optional, default: mysql
        'host'     => 'localhost',     // optional, default: localhost
        'charset'  => 'utf8mb4',       // optional, default: utf8mb4
    ]);
    echo "Database 'john_blog' created with user 'john_bloguser'.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// Create another database (PostgreSQL example)
try {
    $client->databases()->create([
        'user'     => $username,
        'database' => 'analytics',
        'dbuser'   => 'analyticsuser',
        'dbpass'   => 'PgPass456!',
        'type'     => 'pgsql',
    ]);
    echo "PostgreSQL database 'john_analytics' created.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// ─────────────────────────────────────────────────────────────
// Change database user password
// ─────────────────────────────────────────────────────────────
echo "\n=== Change DB Password ===\n";

$ok = $client->databases()->changePassword(
    username: $username,
    database: 'john_blog',
    dbuser:   'john_bloguser',
    password: 'NewDbPass789!'
);
echo $ok ? "Database password changed.\n" : "Failed.\n";

// ─────────────────────────────────────────────────────────────
// Suspend / Unsuspend
// ─────────────────────────────────────────────────────────────
echo "\n=== Suspend Database ===\n";

$ok = $client->databases()->suspend($username, 'john_blog');
echo $ok ? "Database suspended.\n" : "Failed.\n";

$ok = $client->databases()->unsuspend($username, 'john_blog');
echo $ok ? "Database unsuspended.\n" : "Failed.\n";

// ─────────────────────────────────────────────────────────────
// Delete a database
// ─────────────────────────────────────────────────────────────
echo "\n=== Delete Database ===\n";

// $ok = $client->databases()->delete($username, 'john_blog');
// echo $ok ? "Database deleted.\n" : "Failed.\n";
echo "Delete is commented out for safety.\n";
