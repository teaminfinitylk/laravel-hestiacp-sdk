<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\DTOs;

use JsonSerializable;

class WebDomainDto implements JsonSerializable
{
    public function __construct(
        public readonly string $domain,
        public readonly string $parentDomain,
        public readonly ?string $ip = null,
        public readonly ?string $ipv6 = null,
        public readonly ?int $port = null,
        public readonly ?string $docroot = null,
        public readonly ?string $sslDir = null,
        public readonly ?bool $ssl = null,
        public readonly ?string $sslEmail = null,
        public readonly ?string $sslIssuer = null,
        public readonly ?string $sslSubject = null,
        public readonly ?string $sslNotBefore = null,
        public readonly ?string $sslNotAfter = null,
        public readonly ?bool $letsencrypt = null,
        public readonly ?bool $ftpUser = null,
        public readonly ?bool $stats = null,
        public readonly ?string $statsUser = null,
        public readonly ?string $statsPassword = null,
        public readonly ?string $backendTemplate = null,
        public readonly ?string $proxyTemplate = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            domain: $data['DOMAIN'] ?? $data['domain'] ?? '',
            parentDomain: $data['PARENT_DOMAIN'] ?? $data['parent_domain'] ?? $data['PARENT'] ?? '',
            ip: $data['IP'] ?? $data['ip'] ?? null,
            ipv6: $data['IPV6'] ?? $data['ipv6'] ?? null,
            port: isset($data['PORT']) ? (int) $data['PORT'] : ($data['port'] ?? null),
            docroot: $data['DOCROOT'] ?? $data['docroot'] ?? null,
            sslDir: $data['SSL_DIR'] ?? $data['ssl_dir'] ?? null,
            ssl: isset($data['SSL']) ? (bool) $data['SSL'] : ($data['ssl'] ?? null),
            sslEmail: $data['SSL_EMAIL'] ?? $data['ssl_email'] ?? null,
            sslIssuer: $data['SSL_ISSUER'] ?? $data['ssl_issuer'] ?? null,
            sslSubject: $data['SSL_SUBJECT'] ?? $data['ssl_subject'] ?? null,
            sslNotBefore: $data['SSL_NOT_BEFORE'] ?? $data['ssl_not_before'] ?? null,
            sslNotAfter: $data['SSL_NOT_AFTER'] ?? $data['ssl_not_after'] ?? null,
            letsencrypt: isset($data['LETSENCRYPT']) ? (bool) $data['LETSENCRYPT'] : ($data['letsencrypt'] ?? null),
            ftpUser: isset($data['FTP_USER']) ? (bool) $data['FTP_USER'] : ($data['ftp_user'] ?? null),
            stats: isset($data['STATS']) ? (bool) $data['STATS'] : ($data['stats'] ?? null),
            statsUser: $data['STATS_USER'] ?? $data['stats_user'] ?? null,
            statsPassword: $data['STATS_PASS'] ?? $data['stats_password'] ?? null,
            backendTemplate: $data['BACKEND'] ?? $data['backend_template'] ?? null,
            proxyTemplate: $data['PROXY'] ?? $data['proxy_template'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'domain' => $this->domain,
            'parent_domain' => $this->parentDomain,
            'ip' => $this->ip,
            'ipv6' => $this->ipv6,
            'port' => $this->port,
            'docroot' => $this->docroot,
            'ssl_dir' => $this->sslDir,
            'ssl' => $this->ssl,
            'ssl_email' => $this->sslEmail,
            'ssl_issuer' => $this->sslIssuer,
            'ssl_subject' => $this->sslSubject,
            'ssl_not_before' => $this->sslNotBefore,
            'ssl_not_after' => $this->sslNotAfter,
            'letsencrypt' => $this->letsencrypt,
            'ftp_user' => $this->ftpUser,
            'stats' => $this->stats,
            'stats_user' => $this->statsUser,
            'stats_password' => $this->statsPassword,
            'backend_template' => $this->backendTemplate,
            'proxy_template' => $this->proxyTemplate,
        ];
    }
}