<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\DTOs\WebDomainDto;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

class WebResource
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    /**
     * v-list-web-domains USER [FORMAT]
     * List all web domains for a specific user
     */
    public function list(string $username = 'admin'): array
    {
        $response = $this->connector->execute('v-list-web-domains', [$username, 'json'], false);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to list web domains: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        $domains = [];
        foreach ($response->all() as $domain => $data) {
            if (!is_array($data)) continue;
            $domains[] = WebDomainDto::fromArray(array_merge($data, [
                'DOMAIN'        => $domain,
                'PARENT_DOMAIN' => $domain,
            ]));
        }

        return $domains;
    }

    /**
     * List all domains across ALL users
     * Loops v-list-web-domains per user
     */
    public function listAll(array $usernames): array
    {
        $all = [];
        foreach ($usernames as $username) {
            try {
                $domains = $this->list($username);
                foreach ($domains as $domain) {
                    $all[] = $domain;
                }
            } catch (\Exception) {
                continue;
            }
        }
        return $all;
    }

    /**
     * v-list-web-domain USER DOMAIN [FORMAT]
     */
    public function get(string $username, string $domain): ?array
    {
        $response = $this->connector->execute('v-list-web-domain', [$username, $domain, 'json'], false);

        if ($response->isError()) {
            return null;
        }

        $data = $response->all();
        return $data[$domain] ?? (array_values($data)[0] ?? null);
    }

    /**
     * v-add-web-domain USER DOMAIN [IP] [RESTART] [ALIASES] [PROXY_EXTENSIONS]
     */
    public function create(array $data): array
    {
        $response = $this->connector->execute('v-add-web-domain', [
            $data['user'],
            $data['domain'],
            $data['ip']      ?? '',
            $data['restart'] ?? 'yes',
            $data['aliases'] ?? '',
        ]);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to create web domain: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return $response->all();
    }

    /**
     * v-delete-web-domain USER DOMAIN [RESTART]
     */
    public function delete(string $username, string $domain, bool $restart = true): bool
    {
        $response = $this->connector->execute('v-delete-web-domain', [
            $username,
            $domain,
            $restart ? 'yes' : 'no',
        ]);

        return $response->isSuccessful();
    }

    /**
     * v-add-letsencrypt-domain USER DOMAIN [ALIASES] [MAIL]
     */
    public function addSsl(string $username, string $domain, string $aliases = ''): bool
    {
        $response = $this->connector->execute('v-add-letsencrypt-domain', [
            $username,
            $domain,
            $aliases,
        ]);

        return $response->isSuccessful();
    }

    /**
     * v-suspend-web-domain USER DOMAIN [RESTART]
     */
    public function suspend(string $username, string $domain): bool
    {
        $response = $this->connector->execute('v-suspend-web-domain', [$username, $domain, 'yes']);
        return $response->isSuccessful();
    }

    /**
     * v-unsuspend-web-domain USER DOMAIN [RESTART]
     */
    public function unsuspend(string $username, string $domain): bool
    {
        $response = $this->connector->execute('v-unsuspend-web-domain', [$username, $domain, 'yes']);
        return $response->isSuccessful();
    }
}