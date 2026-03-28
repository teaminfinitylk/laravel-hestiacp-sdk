<?php

/**
 * HestiaCP SDK — Mail Domain & Account Examples
 * ================================================
 * Demonstrates: list domains, create domain, list accounts,
 *               add account, change password, delete account
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
// List all mail domains
// ─────────────────────────────────────────────────────────────
echo "=== Mail Domains for '{$username}' ===\n";

$domains = $client->mail()->list($username);

foreach ($domains as $domain) {
    echo sprintf(
        "  %-25s  antispam=%s  antivirus=%s  dkim=%s  ssl=%s\n",
        $domain->domain,
        $domain->antispam    ? 'yes' : 'no',
        $domain->antivirus   ? 'yes' : 'no',
        $domain->dkim        ? 'yes' : 'no',
        $domain->ssl         ? 'yes' : 'no',
    );
}

// ─────────────────────────────────────────────────────────────
// Create a mail domain
// ─────────────────────────────────────────────────────────────
echo "\n=== Create Mail Domain ===\n";

try {
    $client->mail()->create([
        'user'      => $username,
        'domain'    => 'example.com',
        'antispam'  => 'yes',   // optional, default: yes
        'antivirus' => 'yes',   // optional, default: yes
        'dkim'      => 'yes',   // optional, default: yes
        'ssl'       => 'no',    // optional
        'catchall'  => '',      // optional — set to an email for catch-all
    ]);
    echo "Mail domain 'example.com' created.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// ─────────────────────────────────────────────────────────────
// List mail accounts on a domain
// ─────────────────────────────────────────────────────────────
echo "\n=== Mail Accounts on 'example.com' ===\n";

$accounts = $client->mail()->listAccounts($username, 'example.com');

foreach ($accounts as $account => $info) {
    echo "  " . $account . "@example.com";
    echo "  quota=" . ($info['QUOTA'] ?? '0') . " MB\n";
}

// ─────────────────────────────────────────────────────────────
// Add a mail account
// ─────────────────────────────────────────────────────────────
echo "\n=== Add Mail Account ===\n";

try {
    $client->mail()->addAccount([
        'user'     => $username,
        'domain'   => 'example.com',
        'account'  => 'info',           // creates info@example.com
        'password' => 'MailPass123!',
        'quota'    => '0',              // optional: 0 = unlimited (MB)
    ]);
    echo "Account info@example.com created.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// Add another account
try {
    $client->mail()->addAccount([
        'user'     => $username,
        'domain'   => 'example.com',
        'account'  => 'support',
        'password' => 'Support456!',
        'quota'    => '500',            // 500 MB quota
    ]);
    echo "Account support@example.com created.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// ─────────────────────────────────────────────────────────────
// Change a mail account password
// ─────────────────────────────────────────────────────────────
echo "\n=== Change Mail Account Password ===\n";

$ok = $client->mail()->changeAccountPassword($username, 'example.com', 'info', 'NewMailPass789!');
echo $ok ? "Password changed for info@example.com.\n" : "Failed to change password.\n";

// ─────────────────────────────────────────────────────────────
// Suspend / Unsuspend mail domain
// ─────────────────────────────────────────────────────────────
echo "\n=== Suspend Mail Domain ===\n";

$ok = $client->mail()->suspend($username, 'example.com');
echo $ok ? "Mail domain suspended.\n" : "Failed.\n";

$ok = $client->mail()->unsuspend($username, 'example.com');
echo $ok ? "Mail domain unsuspended.\n" : "Failed.\n";

// ─────────────────────────────────────────────────────────────
// Delete a mail account
// ─────────────────────────────────────────────────────────────
echo "\n=== Delete Mail Account ===\n";

// $ok = $client->mail()->deleteAccount($username, 'example.com', 'info');
// echo $ok ? "Account deleted.\n" : "Failed.\n";
echo "Delete is commented out for safety.\n";

// ─────────────────────────────────────────────────────────────
// Delete a mail domain (removes ALL accounts on it)
// ─────────────────────────────────────────────────────────────
echo "\n=== Delete Mail Domain ===\n";

// $ok = $client->mail()->delete($username, 'example.com');
// echo $ok ? "Domain deleted.\n" : "Failed.\n";
echo "Delete is commented out for safety.\n";
