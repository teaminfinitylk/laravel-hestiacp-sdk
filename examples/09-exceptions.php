<?php

/**
 * HestiaCP SDK — Exception Handling Examples
 * =============================================
 * Demonstrates how to handle all SDK exceptions properly.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use TeamInfinityLK\HestiaCP\HestiaClient;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;
use TeamInfinityLK\HestiaCP\Exceptions\AuthenticationException;
use TeamInfinityLK\HestiaCP\Exceptions\ConnectionException;
use TeamInfinityLK\HestiaCP\Exceptions\HestiaException;

// ─────────────────────────────────────────────────────────────
// Basic exception handling
// ─────────────────────────────────────────────────────────────
$client = HestiaClient::connect(
    'https://your-hestia-server.com:8083',
    'YOUR_ACCESS_KEY_ID:YOUR_SECRET_ACCESS_KEY'
);

try {
    $users = $client->users()->list();
    echo "Got " . count($users) . " users.\n";

} catch (AuthenticationException $e) {
    // Wrong API key or credentials
    echo "[AUTH ERROR] " . $e->getMessage() . "\n";
    echo "Check your API key in .env: HESTIA_API_KEY=KEYID:SECRET\n";

} catch (ConnectionException $e) {
    // Server unreachable, timeout, SSL error
    echo "[CONNECTION ERROR] " . $e->getMessage() . "\n";
    echo "Check server URL and HESTIA_VERIFY_SSL setting.\n";

} catch (ApiException $e) {
    // HestiaCP returned a non-zero return code
    echo "[API ERROR] " . $e->getMessage() . "\n";
    echo "  Code : " . $e->getCode() . "\n";  // e.g. 10 = E_FORBIDDEN

} catch (HestiaException $e) {
    // Catch-all for any SDK exception
    echo "[SDK ERROR] " . $e->getMessage() . "\n";
}

// ─────────────────────────────────────────────────────────────
// HestiaCP error codes
// ─────────────────────────────────────────────────────────────
echo "\n=== HestiaCP Error Codes Reference ===\n";

$errors = [
    0  => 'OK          — Success',
    1  => 'E_ARGS      — Missing or invalid arguments',
    2  => 'E_INVALID   — Invalid object value',
    3  => 'E_NOTEXIST  — Object does not exist',
    4  => 'E_EXISTS    — Object already exists',
    5  => 'E_SUSPENDED — Object is suspended',
    6  => 'E_UNSUSPENDED — Object is not suspended',
    7  => 'E_INUSE     — Object is in use',
    8  => 'E_LIMIT     — Resource limit reached (package quota)',
    9  => 'E_PASSWORD  — Invalid password',
    10 => 'E_FORBIDDEN — Command not permitted (wrong API key)',
    11 => 'E_DISABLED  — Feature is disabled',
    12 => 'E_PARSING   — Configuration parsing error',
    13 => 'E_DISK      — Not enough disk space',
    14 => 'E_LA        — Server load average too high',
    15 => 'E_CONNECT   — Connection to service failed',
    16 => 'E_FTP       — FTP error',
    17 => 'E_DB        — Database error',
    18 => 'E_RDD       — Remote DNS domain error',
    19 => 'E_UPDATE    — Update error',
    20 => 'E_RESTART   — Service restart failed',
];

foreach ($errors as $code => $desc) {
    echo "  {$code}: {$desc}\n";
}

// ─────────────────────────────────────────────────────────────
// Handle specific HestiaCP error codes
// ─────────────────────────────────────────────────────────────
echo "\n=== Handling Specific Error Codes ===\n";

try {
    $client->users()->create([
        'user'     => 'admin', // already exists!
        'password' => 'pass',
        'email'    => 'admin@example.com',
    ]);
} catch (ApiException $e) {
    switch ($e->getCode()) {
        case 4:
            echo "User already exists. Try a different username.\n";
            break;
        case 8:
            echo "User limit reached. Upgrade the admin package.\n";
            break;
        case 2:
            echo "Invalid username or password format.\n";
            break;
        default:
            echo "Error {$e->getCode()}: {$e->getMessage()}\n";
    }
}

// ─────────────────────────────────────────────────────────────
// Safe list helper — return empty array on error instead of throwing
// ─────────────────────────────────────────────────────────────
echo "\n=== Safe List (no exception on error) ===\n";

function safeList(callable $fn, array $default = []): array
{
    try {
        return $fn();
    } catch (HestiaException) {
        return $default;
    }
}

$users   = safeList(fn() => $client->users()->list());
$domains = safeList(fn() => $client->web()->list('john'));

echo "Users  : " . count($users)   . "\n";
echo "Domains: " . count($domains) . "\n";
