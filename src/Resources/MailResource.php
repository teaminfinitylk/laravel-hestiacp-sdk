<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Resources;

use TeamInfinityLK\HestiaCP\DTOs\MailDomainDto;
use TeamInfinityLK\HestiaCP\Http\Connector;
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;

/**
 * Mail domain & account management via HestiaCP API.
 *
 * All operations use POST /api/index.php with the appropriate
 * v-* CLI command name — there is no REST-style endpoint.
 */
class MailResource
{
    public function __construct(
        private readonly Connector $connector
    ) {}

    // ──────────────────────────────────────────────────────────────
    // Mail Domains
    // ──────────────────────────────────────────────────────────────

    /**
     * v-list-mail-domains USER [FORMAT]
     * List all mail domains for a user.
     *
     * @return MailDomainDto[]
     */
    public function list(string $username): array
    {
        $response = $this->connector->execute('v-list-mail-domains', [$username, 'json'], false);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to list mail domains: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        $domains = [];
        foreach ($response->all() as $domain => $data) {
            if (!is_array($data)) {
                continue;
            }
            $domains[] = MailDomainDto::fromArray(array_merge($data, ['DOMAIN' => $domain]));
        }

        return $domains;
    }

    /**
     * v-list-mail-domain USER DOMAIN [FORMAT]
     * Get a single mail domain.
     */
    public function get(string $username, string $domain): ?array
    {
        $response = $this->connector->execute('v-list-mail-domain', [$username, $domain, 'json'], false);

        if ($response->isError()) {
            return null;
        }

        $data = $response->all();
        return $data[$domain] ?? (array_values($data)[0] ?? null);
    }

    /**
     * v-add-mail-domain USER DOMAIN [ANTISPAM] [ANTIVIRUS] [DKIM] [SSL] [CATCHALL]
     * Create a new mail domain.
     *
     * $data keys: user, domain, antispam, antivirus, dkim, ssl, catchall
     */
    public function create(array $data): bool
    {
        $response = $this->connector->execute('v-add-mail-domain', [
            $data['user'],
            $data['domain'],
            $data['antispam']  ?? 'yes',
            $data['antivirus'] ?? 'yes',
            $data['dkim']      ?? 'yes',
            $data['ssl']       ?? 'no',
            $data['catchall']  ?? '',
        ]);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to create mail domain: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return true;
    }

    /**
     * v-delete-mail-domain USER DOMAIN
     */
    public function delete(string $username, string $domain): bool
    {
        $response = $this->connector->execute('v-delete-mail-domain', [$username, $domain]);
        return $response->isSuccessful();
    }

    /**
     * v-suspend-mail-domain USER DOMAIN
     */
    public function suspend(string $username, string $domain): bool
    {
        $response = $this->connector->execute('v-suspend-mail-domain', [$username, $domain]);
        return $response->isSuccessful();
    }

    /**
     * v-unsuspend-mail-domain USER DOMAIN
     */
    public function unsuspend(string $username, string $domain): bool
    {
        $response = $this->connector->execute('v-unsuspend-mail-domain', [$username, $domain]);
        return $response->isSuccessful();
    }

    // ──────────────────────────────────────────────────────────────
    // Mail Accounts
    // ──────────────────────────────────────────────────────────────

    /**
     * v-list-mail-accounts USER DOMAIN [FORMAT]
     * List all mail accounts for a domain.
     */
    public function listAccounts(string $username, string $domain): array
    {
        $response = $this->connector->execute('v-list-mail-accounts', [$username, $domain, 'json'], false);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to list mail accounts: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return $response->all();
    }

    /**
     * v-add-mail-account USER DOMAIN ACCOUNT PASSWORD [QUOTA]
     * Create a mail account.
     *
     * $data keys: user, domain, account, password, quota
     */
    public function addAccount(array $data): bool
    {
        $response = $this->connector->execute('v-add-mail-account', [
            $data['user'],
            $data['domain'],
            $data['account'],
            $data['password'],
            $data['quota'] ?? '0',
        ]);

        if ($response->isError()) {
            throw new ApiException(
                'Failed to add mail account: ' . $response->getErrorName(),
                $response->hestiaCode
            );
        }

        return true;
    }

    /**
     * v-delete-mail-account USER DOMAIN ACCOUNT
     */
    public function deleteAccount(string $username, string $domain, string $account): bool
    {
        $response = $this->connector->execute('v-delete-mail-account', [$username, $domain, $account]);
        return $response->isSuccessful();
    }

    /**
     * v-change-mail-account-password USER DOMAIN ACCOUNT PASSWORD
     */
    public function changeAccountPassword(string $username, string $domain, string $account, string $password): bool
    {
        $response = $this->connector->execute('v-change-mail-account-password', [
            $username,
            $domain,
            $account,
            $password,
        ]);
        return $response->isSuccessful();
    }
}