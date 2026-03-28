<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

/**
 * Firewall rule management via HestiaCP API.
 *
 * All operations POST to /api/index.php using the v-* CLI commands.
 */
class FirewallResource
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    // ──────────────────────────────────────────────────────────────
    // Firewall Rules
    // ──────────────────────────────────────────────────────────────

    /**
     * v-list-firewall [FORMAT]
     * List all firewall rules.
     */
    public function list(): array
    {
        $response = $this->connector->execute('v-list-firewall', ['json'], false);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to list firewall rules: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return $response->all();
    }

    /**
     * v-list-firewall-rule RULE [FORMAT]
     * Get a single firewall rule by its numeric ID.
     */
    public function get(int $ruleId): ?array
    {
        $response = $this->connector->execute('v-list-firewall-rule', [(string) $ruleId, 'json'], false);

        if ($response->isError()) {
            return null;
        }

        $data = $response->all();
        return $data[(string) $ruleId] ?? (array_values($data)[0] ?? null);
    }

    /**
     * v-add-firewall-rule ACTION IP PORT [PROTOCOL] [COMMENT] [RULE]
     * Create a new firewall rule.
     *
     * $data keys: action (ACCEPT|DROP|REJECT), ip, port, protocol, comment
     *
     * Examples:
     *   action='ACCEPT', ip='0.0.0.0/0', port='80', protocol='TCP'
     *   action='DROP',   ip='1.2.3.4',   port='*',  protocol='UDP'
     */
    public function create(array $data): bool
    {
        $response = $this->connector->execute('v-add-firewall-rule', [
            strtoupper($data['action'] ?? 'ACCEPT'),
            $data['ip']       ?? '0.0.0.0/0',
            $data['port']     ?? '*',
            strtoupper($data['protocol'] ?? 'TCP'),
            $data['comment']  ?? '',
        ]);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to create firewall rule: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return true;
    }

    /**
     * v-delete-firewall-rule RULE
     */
    public function delete(int $ruleId): bool
    {
        $response = $this->connector->execute('v-delete-firewall-rule', [(string) $ruleId]);
        return $response->isSuccessful();
    }

    // ──────────────────────────────────────────────────────────────
    // Firewall Bans (fail2ban)
    // ──────────────────────────────────────────────────────────────

    /**
     * v-list-firewall-ban CHAIN [FORMAT]
     * List currently banned IPs for a given chain (e.g. 'MAIL', 'WEB', 'CUSTOM').
     * Pass 'all' or omit to list all chains.
     */
    public function listBans(string $chain = 'CUSTOM'): array
    {
        $response = $this->connector->execute('v-list-firewall-ban', [strtoupper($chain), 'json'], false);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to list firewall bans: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return $response->all();
    }

    /**
     * v-add-firewall-ban IP CHAIN
     * Ban an IP address on a specific chain (e.g. 'CUSTOM', 'MAIL', 'WEB').
     */
    public function banIp(string $ip, string $chain = 'CUSTOM'): bool
    {
        $response = $this->connector->execute('v-add-firewall-ban', [$ip, strtoupper($chain)]);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to ban IP: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return true;
    }

    /**
     * v-delete-firewall-ban IP CHAIN
     * Remove a firewall ban for an IP address.
     */
    public function unbanIp(string $ip, string $chain = 'CUSTOM'): bool
    {
        $response = $this->connector->execute('v-delete-firewall-ban', [$ip, strtoupper($chain)]);
        return $response->isSuccessful();
    }
}