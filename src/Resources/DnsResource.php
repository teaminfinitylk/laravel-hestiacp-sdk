<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\DTOs\DnsRecordDto;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

/**
 * DNS domain & record management via HestiaCP API.
 *
 * All operations POST to /api/index.php using the v-* CLI commands.
 */
class DnsResource
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    // ──────────────────────────────────────────────────────────────
    // DNS Zones (Domains)
    // ──────────────────────────────────────────────────────────────

    /**
     * v-list-dns-domains USER [FORMAT]
     * List all DNS zones for a user.
     */
    public function list(string $username): array
    {
        $response = $this->connector->execute('v-list-dns-domains', [$username, 'json'], false);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to list DNS domains: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return $response->all();
    }

    /**
     * v-list-dns-domain USER DOMAIN [FORMAT]
     * Get details of a single DNS zone.
     */
    public function get(string $username, string $domain): ?array
    {
        $response = $this->connector->execute('v-list-dns-domain', [$username, $domain, 'json'], false);

        if ($response->isError()) {
            return null;
        }

        $data = $response->all();
        return $data[$domain] ?? (array_values($data)[0] ?? null);
    }

    /**
     * v-add-dns-domain USER DOMAIN IP [NS1] [NS2] [EXP] [SOA]
     * Create a new DNS zone.
     *
     * $data keys: user, domain, ip, ns1, ns2, exp, soa
     */
    public function create(array $data): bool
    {
        $response = $this->connector->execute('v-add-dns-domain', [
            $data['user'],
            $data['domain'],
            $data['ip']  ?? '',
            $data['ns1'] ?? '',
            $data['ns2'] ?? '',
            $data['exp'] ?? '',
            $data['soa'] ?? '',
        ]);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to create DNS domain: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return true;
    }

    /**
     * v-suspend-dns-domain USER DOMAIN
     */
    public function suspend(string $username, string $domain): bool
    {
        $response = $this->connector->execute('v-suspend-dns-domain', [$username, $domain]);
        return $response->isSuccessful();
    }

    /**
     * v-unsuspend-dns-domain USER DOMAIN
     */
    public function unsuspend(string $username, string $domain): bool
    {
        $response = $this->connector->execute('v-unsuspend-dns-domain', [$username, $domain]);
        return $response->isSuccessful();
    }

    /**
     * v-delete-dns-domain USER DOMAIN
     */
    public function delete(string $username, string $domain): bool
    {
        $response = $this->connector->execute('v-delete-dns-domain', [$username, $domain]);
        return $response->isSuccessful();
    }

    // ──────────────────────────────────────────────────────────────
    // DNS Records
    // ──────────────────────────────────────────────────────────────

    /**
     * v-list-dns-records USER DOMAIN [FORMAT]
     * List all DNS records for a domain.
     *
     * @return DnsRecordDto[]
     */
    public function listRecords(string $username, string $domain): array
    {
        $response = $this->connector->execute('v-list-dns-records', [$username, $domain, 'json'], false);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to list DNS records: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        $records = [];
        foreach ($response->all() as $id => $data) {
            if (!is_array($data)) {
                continue;
            }
            $records[] = DnsRecordDto::fromArray(array_merge($data, ['ID' => $id]));
        }

        return $records;
    }

    /**
     * v-add-dns-record USER DOMAIN RECORD TYPE VALUE [PRIORITY] [ID] [RESTART] [TTL]
     * Add a DNS record to a zone.
     *
     * $data keys: user, domain, record, type, value, priority, ttl
     */
    public function addRecord(string $username, string $domain, array $data): bool
    {
        $response = $this->connector->execute('v-add-dns-record', [
            $username,
            $domain,
            $data['record']   ?? '',
            $data['type']     ?? 'A',
            $data['value']    ?? '',
            $data['priority'] ?? '0',
            '',    // ID — auto-assigned
            'yes', // RESTART
            $data['ttl'] ?? '14400',
        ]);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to add DNS record: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return true;
    }

    /**
     * v-delete-dns-record USER DOMAIN ID
     */
    public function deleteRecord(string $username, string $domain, int $recordId): bool
    {
        $response = $this->connector->execute('v-delete-dns-record', [
            $username,
            $domain,
            (string) $recordId,
        ]);
        return $response->isSuccessful();
    }

    /**
     * v-update-dns-record USER DOMAIN ID RECORD TYPE VALUE [PRIORITY] [TTL]
     * Update an existing DNS record.
     *
     * $data keys: record, type, value, priority, ttl
     */
    public function updateRecord(string $username, string $domain, int $recordId, array $data): bool
    {
        $response = $this->connector->execute('v-update-dns-record', [
            $username,
            $domain,
            (string) $recordId,
            $data['record']   ?? '',
            $data['type']     ?? 'A',
            $data['value']    ?? '',
            $data['priority'] ?? '0',
            $data['ttl']      ?? '14400',
        ]);
        return $response->isSuccessful();
    }
}