<?php

/**
 * HestiaCP SDK — User Management Examples
 * ==========================================
 * Demonstrates: list, get, create, change password,
 *               change package, suspend, unsuspend, delete
 */

require_once __DIR__ . '/../vendor/autoload.php';

use TeamInfinityLK\HestiaCP\HestiaClient;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

$client = HestiaClient::connect(
    'https://your-hestia-server.com:8083',
    'YOUR_ACCESS_KEY_ID:YOUR_SECRET_ACCESS_KEY'
);

// ─────────────────────────────────────────────────────────────
// List all users
// ─────────────────────────────────────────────────────────────
echo "=== All Users ===\n";

$users = $client->users()->list();

foreach ($users as $user) {
    echo sprintf(
        "  %-15s  %-30s  package=%-10s  suspended=%s\n",
        $user->user,
        $user->contact,
        $user->package,
        $user->suspended
    );
}

// ─────────────────────────────────────────────────────────────
// Get a single user
// ─────────────────────────────────────────────────────────────
echo "\n=== Get Single User ===\n";

$data = $client->users()->get('admin');

if ($data) {
    echo "Username  : " . ($data['USER']    ?? 'n/a') . "\n";
    echo "Email     : " . ($data['CONTACT'] ?? 'n/a') . "\n";
    echo "Package   : " . ($data['PACKAGE'] ?? 'n/a') . "\n";
    echo "Disk Usage: " . ($data['U_DISK']  ?? 'n/a') . " MB\n";
} else {
    echo "User not found.\n";
}

// ─────────────────────────────────────────────────────────────
// Create a new user
// ─────────────────────────────────────────────────────────────
echo "\n=== Create User ===\n";

try {
    $client->users()->create([
        'user'     => 'john',
        'password' => 'SuperSecret123!',
        'email'    => 'john@example.com',
        'package'  => 'default',   // optional
        'name'     => 'John',      // optional
        'lastname' => 'Doe',       // optional
    ]);
    echo "User 'john' created successfully.\n";
} catch (ApiException $e) {
    echo "Failed to create user: " . $e->getMessage() . "\n";
}

// ─────────────────────────────────────────────────────────────
// Change user password
// ─────────────────────────────────────────────────────────────
echo "\n=== Change Password ===\n";

$ok = $client->users()->changePassword('john', 'NewPassword456!');
echo $ok ? "Password changed.\n" : "Failed to change password.\n";

// ─────────────────────────────────────────────────────────────
// Change user package
// ─────────────────────────────────────────────────────────────
echo "\n=== Change Package ===\n";

$ok = $client->users()->changePackage('john', 'premium');
echo $ok ? "Package changed to 'premium'.\n" : "Failed to change package.\n";

// ─────────────────────────────────────────────────────────────
// Change contact email
// ─────────────────────────────────────────────────────────────
echo "\n=== Change Contact ===\n";

$ok = $client->users()->changeContact('john', 'john.doe@example.com');
echo $ok ? "Contact email updated.\n" : "Failed to update contact.\n";

// ─────────────────────────────────────────────────────────────
// Suspend a user (e.g. for non-payment)
// ─────────────────────────────────────────────────────────────
echo "\n=== Suspend User ===\n";

$ok = $client->users()->suspend('john');
echo $ok ? "User 'john' suspended.\n" : "Failed to suspend user.\n";

// ─────────────────────────────────────────────────────────────
// Unsuspend a user (e.g. payment received)
// ─────────────────────────────────────────────────────────────
echo "\n=== Unsuspend User ===\n";

$ok = $client->users()->unsuspend('john');
echo $ok ? "User 'john' unsuspended.\n" : "Failed to unsuspend user.\n";

// ─────────────────────────────────────────────────────────────
// Delete a user (CAUTION: deletes all data)
// ─────────────────────────────────────────────────────────────
echo "\n=== Delete User ===\n";

// $ok = $client->users()->delete('john');
// echo $ok ? "User 'john' deleted.\n" : "Failed to delete user.\n";
echo "Delete is commented out for safety. Uncomment when needed.\n";
