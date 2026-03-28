<?php

/**
 * HestiaCP SDK — Firewall, IP & Package Examples
 * =================================================
 * Demonstrates:
 *   Firewall: list rules, add rules, ban/unban IPs
 *   IPs:      list, get, add
 *   Packages: list, get
 */

require_once __DIR__ . '/../vendor/autoload.php';

use TeamInfinityLK\HestiaCP\HestiaClient;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

$client = HestiaClient::connect(
    'https://your-hestia-server.com:8083',
    'YOUR_ACCESS_KEY_ID:YOUR_SECRET_ACCESS_KEY'
);

// ─────────────────────────────────────────────────────────────
// FIREWALL — List rules
// ─────────────────────────────────────────────────────────────
echo "=== Firewall Rules ===\n";

$rules = $client->firewall()->list();

foreach ($rules as $id => $rule) {
    echo sprintf(
        "  ID=%-4s  %-6s  %-20s  port=%-8s  proto=%-4s  %s\n",
        $id,
        $rule['ACTION']   ?? '?',
        $rule['IP']       ?? '?',
        $rule['PORT']     ?? '?',
        $rule['PROTOCOL'] ?? '?',
        $rule['COMMENT']  ?? ''
    );
}

// ─────────────────────────────────────────────────────────────
// FIREWALL — Add rules
// ─────────────────────────────────────────────────────────────
echo "\n=== Add Firewall Rules ===\n";

// Allow HTTP
try {
    $client->firewall()->create([
        'action'   => 'ACCEPT',
        'ip'       => '0.0.0.0/0',   // all IPs
        'port'     => '80',
        'protocol' => 'TCP',
        'comment'  => 'Allow HTTP',
    ]);
    echo "Rule: Allow HTTP (port 80) added.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// Allow HTTPS
try {
    $client->firewall()->create([
        'action'   => 'ACCEPT',
        'ip'       => '0.0.0.0/0',
        'port'     => '443',
        'protocol' => 'TCP',
        'comment'  => 'Allow HTTPS',
    ]);
    echo "Rule: Allow HTTPS (port 443) added.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// Block a specific IP
try {
    $client->firewall()->create([
        'action'   => 'DROP',
        'ip'       => '1.2.3.4',
        'port'     => '*',            // all ports
        'protocol' => 'TCP',
        'comment'  => 'Block attacker 1.2.3.4',
    ]);
    echo "Rule: Block IP 1.2.3.4 added.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// ─────────────────────────────────────────────────────────────
// FIREWALL — IP Bans (fail2ban)
// ─────────────────────────────────────────────────────────────
echo "\n=== Firewall Bans ===\n";

// List current bans
$bans = $client->firewall()->listBans('CUSTOM');
echo "Current CUSTOM bans: " . count($bans) . "\n";
foreach ($bans as $ip => $info) {
    echo "  Banned: " . $ip . "\n";
}

// Ban an IP
try {
    $client->firewall()->banIp('5.6.7.8', 'CUSTOM');
    echo "IP 5.6.7.8 banned.\n";
} catch (ApiException $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// Unban an IP
$ok = $client->firewall()->unbanIp('5.6.7.8', 'CUSTOM');
echo $ok ? "IP 5.6.7.8 unbanned.\n" : "Failed to unban.\n";

// ─────────────────────────────────────────────────────────────
// IP ADDRESSES — System IPs (admin only)
// ─────────────────────────────────────────────────────────────
echo "\n=== System IP Addresses ===\n";

$ips = $client->ips()->list();

foreach ($ips as $ip => $info) {
    echo sprintf(
        "  %-18s  netmask=%-16s  owner=%-10s  status=%s\n",
        $ip,
        $info['NETMASK'] ?? '?',
        $info['OWNER']   ?? '?',
        $info['STATUS']  ?? '?'
    );
}

// Get a single IP
$ipData = $client->ips()->get('192.168.1.100');

if ($ipData) {
    echo "\nIP Details:\n";
    echo "  Netmask  : " . ($ipData['NETMASK']   ?? 'n/a') . "\n";
    echo "  Interface: " . ($ipData['INTERFACE'] ?? 'n/a') . "\n";
    echo "  Status   : " . ($ipData['STATUS']    ?? 'n/a') . "\n";
    echo "  NAT IP   : " . ($ipData['NAT']       ?? 'n/a') . "\n";
}

// ─────────────────────────────────────────────────────────────
// PACKAGES — Hosting packages
// ─────────────────────────────────────────────────────────────
echo "\n=== Hosting Packages ===\n";

$packages = $client->packages()->list();

foreach ($packages as $name => $pkg) {
    echo sprintf(
        "  %-15s  domains=%-5s  dbs=%-5s  mail=%-5s  bw=%-10s  disk=%s\n",
        $name,
        $pkg['WEB_DOMAINS']  ?? '?',
        $pkg['DATABASES']    ?? '?',
        $pkg['MAIL_DOMAINS'] ?? '?',
        $pkg['BANDWIDTH']    ?? '?',
        $pkg['DISK']         ?? '?'
    );
}

// Get a specific package
$pkg = $client->packages()->get('default');

if ($pkg) {
    echo "\nPackage 'default' details:\n";
    echo "  Web Domains : " . ($pkg['WEB_DOMAINS']  ?? 'n/a') . "\n";
    echo "  Databases   : " . ($pkg['DATABASES']    ?? 'n/a') . "\n";
    echo "  Mail Domains: " . ($pkg['MAIL_DOMAINS'] ?? 'n/a') . "\n";
    echo "  Bandwidth   : " . ($pkg['BANDWIDTH']    ?? 'n/a') . " MB\n";
    echo "  Disk        : " . ($pkg['DISK']         ?? 'n/a') . " MB\n";
    echo "  Backups     : " . ($pkg['BACKUPS']      ?? 'n/a') . "\n";
}
