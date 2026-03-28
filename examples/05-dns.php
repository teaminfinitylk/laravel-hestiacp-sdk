<?php

/**
 * HestiaCP SDK — DNS Zone & Record Examples
 * ============================================
 * Demonstrates: list zones, create zone, list records,
 *               add record, update record, delete record
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
// List all DNS zones for a user
// ─────────────────────────────────────────────────────────────
echo "=== DNS Zones for '{$username}' ===\n";

$zones = $client->dns()->list($username);

foreach ($zones as $zone => $info) {
    echo "  " . $zone . "  (expiry: " . ($info['EXP'] ?? 'n/a') . ")\n";
}

// ─────────────────────────────────────────────────────────────
// Get a single DNS zone
// ─────────────────────────────────────────────────────────────
echo "\n=== Get DNS Zone: example.com ===\n";

$data = $client->dns()->get($username, 'example.com');

if ($data) {
    echo "SOA   : " . ($data['SOA']      ?? 'n/a') . "\n";
    echo "NS1   : " . ($data['NS']       ?? 'n/a') . "\n";
    echo "Expiry: " . ($data['EXP']      ?? 'n/a') . "\n";
    echo "Serial: " . ($data['SRC']      ?? 'n/a') . "\n";
}

// ─────────────────────────────────────────────────────────────
// Create a DNS zone
// ─────────────────────────────────────────────────────────────
echo "\n=== Create DNS Zone ===\n";

try {
    $client->dns()->create([
        'user'   => $username,
        'domain' => 'newdomain.com',
        'ip'     => '192.168.1.100',   // optional — IP for A record
        'ns1'    => '',                // optional — custom nameserver
        'ns2'    => '',                // optional
    ]);
    echo "DNS zone 'newdomain.com' created.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// ─────────────────────────────────────────────────────────────
// List all DNS records for a zone
// ─────────────────────────────────────────────────────────────
echo "\n=== DNS Records for 'example.com' ===\n";

$records = $client->dns()->listRecords($username, 'example.com');

foreach ($records as $record) {
    echo sprintf(
        "  ID=%-4d  %-10s  %-6s  %s  (TTL=%d)\n",
        $record->id,
        $record->record ?: '@',
        $record->type,
        $record->value,
        $record->ttl ?? 14400
    );
}

// ─────────────────────────────────────────────────────────────
// Add DNS records
// ─────────────────────────────────────────────────────────────
echo "\n=== Add DNS Records ===\n";

// A record
try {
    $client->dns()->addRecord($username, 'example.com', [
        'record' => 'www',
        'type'   => 'A',
        'value'  => '192.168.1.100',
        'ttl'    => '14400',
    ]);
    echo "A record 'www' added.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// MX record
try {
    $client->dns()->addRecord($username, 'example.com', [
        'record'   => '',               // blank = root domain @
        'type'     => 'MX',
        'value'    => 'mail.example.com.',
        'priority' => '10',
        'ttl'      => '14400',
    ]);
    echo "MX record added.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// TXT record (SPF)
try {
    $client->dns()->addRecord($username, 'example.com', [
        'record' => '',
        'type'   => 'TXT',
        'value'  => 'v=spf1 mx ~all',
        'ttl'    => '14400',
    ]);
    echo "SPF TXT record added.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// CNAME record
try {
    $client->dns()->addRecord($username, 'example.com', [
        'record' => 'mail',
        'type'   => 'CNAME',
        'value'  => 'example.com.',
        'ttl'    => '14400',
    ]);
    echo "CNAME record 'mail' added.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// ─────────────────────────────────────────────────────────────
// Delete a DNS record (by its numeric ID)
// ─────────────────────────────────────────────────────────────
echo "\n=== Delete DNS Record ===\n";

// $ok = $client->dns()->deleteRecord($username, 'example.com', 5);
// echo $ok ? "Record deleted.\n" : "Failed.\n";
echo "Delete is commented out for safety.\n";

// ─────────────────────────────────────────────────────────────
// Suspend / Unsuspend / Delete a zone
// ─────────────────────────────────────────────────────────────
echo "\n=== Suspend DNS Zone ===\n";

$ok = $client->dns()->suspend($username, 'newdomain.com');
echo $ok ? "Zone suspended.\n" : "Failed.\n";

$ok = $client->dns()->unsuspend($username, 'newdomain.com');
echo $ok ? "Zone unsuspended.\n" : "Failed.\n";
