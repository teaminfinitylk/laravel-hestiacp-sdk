<?php

/**
 * HestiaCP SDK — Setup & Connection
 * ===================================
 * This file shows how to connect to your HestiaCP server.
 * All other example files require this setup.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use TeamInfinityLK\HestiaCP\HestiaClient;

// ─────────────────────────────────────────────────────────────
// Option 1: API Access Key (Recommended)
// Generate from HestiaCP Panel → Admin → Access Keys
// Key format: "ACCESS_KEY_ID:SECRET_ACCESS_KEY"
// ─────────────────────────────────────────────────────────────
$client = HestiaClient::connect(
    baseUrl: 'https://your-hestia-server.com:8083',
    apiKey:  'YOUR_ACCESS_KEY_ID:YOUR_SECRET_ACCESS_KEY'
);

// ─────────────────────────────────────────────────────────────
// Option 2: Username + Password
// ─────────────────────────────────────────────────────────────
// $client = HestiaClient::connectWithCredentials(
//     baseUrl:  'https://your-hestia-server.com:8083',
//     username: 'admin',
//     password: 'your-password'
// );

// ─────────────────────────────────────────────────────────────
// Option 3: Load from .env manually
// ─────────────────────────────────────────────────────────────
// $client = HestiaClient::connect(
//     baseUrl: getenv('HESTIA_URL'),
//     apiKey:  getenv('HESTIA_API_KEY')
// );

// ─────────────────────────────────────────────────────────────
// Optional: Disable SSL verification (for self-signed certs)
// ─────────────────────────────────────────────────────────────
// $client = new \TeamInfinityLK\HestiaCP\HestiaClient(
//     baseUrl:   'https://your-hestia-server.com:8083',
//     timeout:   30,
//     verifySsl: false
// );
// $client->authenticateWithApiKey('KEY:SECRET');

echo "SDK connected to: https://your-hestia-server.com:8083\n";

return $client;
