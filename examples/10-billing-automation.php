<?php

/**
 * HestiaCP SDK — Complete Billing Automation Example
 * =====================================================
 * Shows a full hosting account lifecycle as used in a billing system:
 *   1. Provision new account (signup)
 *   2. Suspend account (non-payment)
 *   3. Unsuspend account (payment received)
 *   4. Terminate account (cancellation)
 */

require_once __DIR__ . '/../vendor/autoload.php';

use TeamInfinityLK\HestiaCP\HestiaClient;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

$client = HestiaClient::connect(
    'https://your-hestia-server.com:8083',
    'YOUR_ACCESS_KEY_ID:YOUR_SECRET_ACCESS_KEY'
);

// ─────────────────────────────────────────────────────────────
// STEP 1: Provision a New Hosting Account
// Called when a customer completes checkout
// ─────────────────────────────────────────────────────────────
function provisionAccount(HestiaClient $client, array $order): void
{
    $username = $order['username'];
    $domain   = $order['domain'];

    echo "=== Provisioning account for '{$username}' ===\n";

    // 1. Create the user
    $client->users()->create([
        'user'     => $username,
        'password' => $order['password'],
        'email'    => $order['email'],
        'package'  => $order['package'],  // e.g. 'starter', 'business'
        'name'     => $order['first_name'],
        'lastname' => $order['last_name'],
    ]);
    echo "  [✓] User account created\n";

    // 2. Add web domain
    $client->web()->create([
        'user'   => $username,
        'domain' => $domain,
    ]);
    echo "  [✓] Web domain '{$domain}' added\n";

    // 3. Issue SSL certificate
    try {
        $client->web()->addSsl($username, $domain, 'www.' . $domain);
        echo "  [✓] SSL certificate issued\n";
    } catch (ApiException $e) {
        // SSL might fail if DNS not propagated yet — non-fatal
        echo "  [!] SSL pending (DNS not ready): " . $e->getMessage() . "\n";
    }

    // 4. Create mail domain
    $client->mail()->create([
        'user'      => $username,
        'domain'    => $domain,
        'antispam'  => 'yes',
        'antivirus' => 'yes',
        'dkim'      => 'yes',
    ]);
    echo "  [✓] Mail domain configured\n";

    // 5. Add default email account (info@domain.com)
    $client->mail()->addAccount([
        'user'     => $username,
        'domain'   => $domain,
        'account'  => 'info',
        'password' => $order['mail_password'] ?? 'ChangeMePl3ase!',
    ]);
    echo "  [✓] Email account info@{$domain} created\n";

    // 6. Create MySQL database
    $client->databases()->create([
        'user'     => $username,
        'database' => 'main',           // stored as {username}_main
        'dbuser'   => 'mainuser',       // stored as {username}_mainuser
        'dbpass'   => $order['db_password'],
        'type'     => 'mysql',
    ]);
    echo "  [✓] Database {$username}_main created\n";

    // 7. Set up DNS zone
    $client->dns()->create([
        'user'   => $username,
        'domain' => $domain,
        'ip'     => $order['server_ip'] ?? '',
    ]);
    echo "  [✓] DNS zone configured\n";

    echo "\n  Account ready! Summary:\n";
    echo "    FTP/SSH: {$username} / {$order['password']}\n";
    echo "    Mail   : info@{$domain} / " . ($order['mail_password'] ?? 'ChangeMePl3ase!') . "\n";
    echo "    DB     : {$username}_main / {$username}_mainuser / {$order['db_password']}\n";
}

// ─────────────────────────────────────────────────────────────
// STEP 2: Suspend Account (non-payment)
// ─────────────────────────────────────────────────────────────
function suspendAccount(HestiaClient $client, string $username): void
{
    echo "\n=== Suspending account '{$username}' ===\n";

    $client->users()->suspend($username);
    echo "  [✓] Account suspended. All services offline.\n";
}

// ─────────────────────────────────────────────────────────────
// STEP 3: Unsuspend Account (payment received)
// ─────────────────────────────────────────────────────────────
function unsuspendAccount(HestiaClient $client, string $username): void
{
    echo "\n=== Reactivating account '{$username}' ===\n";

    $client->users()->unsuspend($username);
    echo "  [✓] Account reactivated. All services restored.\n";
}

// ─────────────────────────────────────────────────────────────
// STEP 4: Upgrade Package
// ─────────────────────────────────────────────────────────────
function upgradePackage(HestiaClient $client, string $username, string $newPackage): void
{
    echo "\n=== Upgrading '{$username}' to package '{$newPackage}' ===\n";

    $client->users()->changePackage($username, $newPackage);
    echo "  [✓] Package upgraded to '{$newPackage}'.\n";
}

// ─────────────────────────────────────────────────────────────
// STEP 5: Terminate Account (cancellation)
// WARNING: This deletes ALL data permanently
// ─────────────────────────────────────────────────────────────
function terminateAccount(HestiaClient $client, string $username): void
{
    echo "\n=== Terminating account '{$username}' ===\n";

    // Create a final backup before deletion
    try {
        $client->backups()->create($username);
        echo "  [✓] Final backup triggered\n";
    } catch (ApiException $e) {
        echo "  [!] Backup failed: " . $e->getMessage() . "\n";
    }

    // Delete the user (removes ALL domains, mail, dbs, files)
    $client->users()->delete($username);
    echo "  [✓] Account '{$username}' permanently deleted.\n";
}

// ─────────────────────────────────────────────────────────────
// RUN THE DEMO (comment/uncomment as needed)
// ─────────────────────────────────────────────────────────────

try {
    // New order data (this would come from your billing system)
    $order = [
        'username'     => 'johndoe',
        'password'     => 'Secure!Pass123',
        'email'        => 'john@example.com',
        'first_name'   => 'John',
        'last_name'    => 'Doe',
        'package'      => 'starter',
        'domain'       => 'johnssite.com',
        'mail_password'=> 'Mail!Pass456',
        'db_password'  => 'Db!Pass789',
        'server_ip'    => '192.168.1.100',
    ];

    // Provision
    provisionAccount($client, $order);

    // Suspend (e.g. invoice overdue)
    // suspendAccount($client, 'johndoe');

    // Reactivate (e.g. payment received)
    // unsuspendAccount($client, 'johndoe');

    // Upgrade
    // upgradePackage($client, 'johndoe', 'business');

    // Terminate (DESTRUCTIVE — deletes everything)
    // terminateAccount($client, 'johndoe');

} catch (ApiException $e) {
    echo "\n[ERROR] Code {$e->getCode()}: {$e->getMessage()}\n";
    exit(1);
}
