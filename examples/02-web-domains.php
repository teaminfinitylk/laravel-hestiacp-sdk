<?php

/**
 * HestiaCP SDK — Web Domain Examples
 * =====================================
 * Demonstrates: list, get, create, SSL, suspend, unsuspend, delete
 */

require_once __DIR__ . '/../vendor/autoload.php';

use TeamInfinityLK\HestiaCP\HestiaClient;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

$client = HestiaClient::connect(
    'https://your-hestia-server.com:8083',
    'YOUR_ACCESS_KEY_ID:YOUR_SECRET_ACCESS_KEY'
);

$username = 'john'; // The HestiaCP user who owns these domains

// ─────────────────────────────────────────────────────────────
// List all web domains for a user
// ─────────────────────────────────────────────────────────────
echo "=== Web Domains for '{$username}' ===\n";

$domains = $client->web()->list($username);

foreach ($domains as $domain) {
    echo sprintf(
        "  %-25s  ip=%-15s  ssl=%s  suspended=%s\n",
        $domain->domain,
        $domain->ip,
        $domain->ssl ? 'yes' : 'no',
        $domain->suspended ?? 'no'
    );
}

echo "Total: " . count($domains) . " domains.\n";

// ─────────────────────────────────────────────────────────────
// Get a single domain
// ─────────────────────────────────────────────────────────────
echo "\n=== Get Single Domain ===\n";

$data = $client->web()->get($username, 'example.com');

if ($data) {
    echo "Domain    : " . ($data['DOMAIN']  ?? 'n/a') . "\n";
    echo "IP        : " . ($data['IP']      ?? 'n/a') . "\n";
    echo "SSL       : " . ($data['SSL']     ?? 'n/a') . "\n";
    echo "Docroot   : " . ($data['DOCROOT'] ?? 'n/a') . "\n";
} else {
    echo "Domain not found.\n";
}

// ─────────────────────────────────────────────────────────────
// Create a web domain
// ─────────────────────────────────────────────────────────────
echo "\n=== Create Web Domain ===\n";

try {
    $client->web()->create([
        'user'    => $username,
        'domain'  => 'newsite.com',
        'ip'      => '',          // optional — uses server's default IP
        'restart' => 'yes',       // optional — restart web server after
        'aliases' => 'www.newsite.com', // optional — space-separated aliases
    ]);
    echo "Domain 'newsite.com' created.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// ─────────────────────────────────────────────────────────────
// Add Let's Encrypt SSL
// ─────────────────────────────────────────────────────────────
echo "\n=== Add Let's Encrypt SSL ===\n";

try {
    $ok = $client->web()->addSsl($username, 'newsite.com', 'www.newsite.com');
    echo $ok ? "SSL certificate issued.\n" : "Failed to issue SSL.\n";
} catch (ApiException $e) {
    echo "SSL error: " . $e->getMessage() . "\n";
}

// ─────────────────────────────────────────────────────────────
// List ALL domains across all users (e.g. for admin dashboard)
// ─────────────────────────────────────────────────────────────
echo "\n=== All Domains Across All Users ===\n";

$allUsers   = $client->users()->list();
$usernames  = array_map(fn($u) => $u->user, $allUsers);
$allDomains = $client->web()->listAll($usernames);

foreach ($allDomains as $domain) {
    echo "  " . $domain->domain . "\n";
}

// ─────────────────────────────────────────────────────────────
// Suspend / Unsuspend
// ─────────────────────────────────────────────────────────────
echo "\n=== Suspend Domain ===\n";

$ok = $client->web()->suspend($username, 'newsite.com');
echo $ok ? "Domain suspended.\n" : "Failed to suspend.\n";

$ok = $client->web()->unsuspend($username, 'newsite.com');
echo $ok ? "Domain unsuspended.\n" : "Failed to unsuspend.\n";

// ─────────────────────────────────────────────────────────────
// Delete a domain
// ─────────────────────────────────────────────────────────────
echo "\n=== Delete Domain ===\n";

// $ok = $client->web()->delete($username, 'newsite.com');
// echo $ok ? "Domain deleted.\n" : "Failed to delete.\n";
echo "Delete is commented out for safety.\n";
