<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

/**
 * System IP address management via HestiaCP API.
 *
 * All operations POST to /api/index.php using the v-* CLI commands.
 *
 * Note: These are system-level IPs, not user-assigned IPs.
 * Only the admin user can manage system IPs.
 */
class IpResource
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    /**
     * v-list-sys-ips [FORMAT]
     * List all system IP addresses.
     */
    public function list(): array
    {
        $response = $this->connector->execute('v-list-sys-ips', ['json'], false);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to list IPs: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return $response->all();
    }

    /**
     * v-list-sys-ip IP [FORMAT]
     * Get details of a specific system IP.
     */
    public function get(string $ip): ?array
    {
        $response = $this->connector->execute('v-list-sys-ip', [$ip, 'json'], false);

        if ($response->isError()) {
            return null;
        }

        $data = $response->all();
        return $data[$ip] ?? (array_values($data)[0] ?? null);
    }

    /**
     * v-add-sys-ip IP NETMASK INTERFACE [USER] [IP_STATUS] [IP_NAME] [NAT_IP]
     * Add a new IP address to the server.
     *
     * $data keys: ip, netmask, interface, user, status, name, nat_ip
     *
     * Example:
     *   ip='192.168.1.100', netmask='255.255.255.0', interface='eth0'
     */
    public function create(array $data): bool
    {
        $response = $this->connector->execute('v-add-sys-ip', [
            $data['ip'],
            $data['netmask'],
            $data['interface'],
            $data['user']    ?? 'admin',
            $data['status']  ?? 'shared',
            $data['name']    ?? $data['ip'],
            $data['nat_ip']  ?? '',
        ]);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to add IP: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return true;
    }

    /**
     * v-change-sys-ip-owner IP USER
     * Change the owner user of a system IP.
     */
    public function changeOwner(string $ip, string $username): bool
    {
        $response = $this->connector->execute('v-change-sys-ip-owner', [$ip, $username]);
        return $response->isSuccessful();
    }

    /**
     * v-change-sys-ip-name IP NAME
     * Change the name/label of a system IP.
     */
    public function changeName(string $ip, string $name): bool
    {
        $response = $this->connector->execute('v-change-sys-ip-name', [$ip, $name]);
        return $response->isSuccessful();
    }

    /**
     * v-delete-sys-ip IP
     * Remove a system IP address.
     */
    public function delete(string $ip): bool
    {
        $response = $this->connector->execute('v-delete-sys-ip', [$ip]);
        return $response->isSuccessful();
    }
}