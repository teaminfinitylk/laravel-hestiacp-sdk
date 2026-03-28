<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\DTOs;

use JsonSerializable;

class MailDomainDto implements JsonSerializable
{
    public function __construct(
        public readonly string $domain,
        public readonly ?bool $antivirus = null,
        public readonly ?bool $antispam = null,
        public readonly ?bool $dkim = null,
        public readonly ?bool $catchAll = null,
        public readonly ?string $ssl = null,
        public readonly ?string $letsencrypt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            domain: $data['DOMAIN'] ?? $data['domain'] ?? '',
            antivirus: isset($data['ANTIVIRUS']) ? (bool) $data['ANTIVIRUS'] : ($data['antivirus'] ?? null),
            antispam: isset($data['ANTISPAM']) ? (bool) $data['ANTISPAM'] : ($data['antispam'] ?? null),
            dkim: isset($data['DKIM']) ? (bool) $data['DKIM'] : ($data['dkim'] ?? null),
            catchAll: isset($data['CATCHALL']) ? (bool) $data['CATCHALL'] : ($data['catchall'] ?? null),
            ssl: $data['SSL'] ?? $data['ssl'] ?? null,
            letsencrypt: $data['LETSENCRYPT'] ?? $data['letsencrypt'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'domain' => $this->domain,
            'antivirus' => $this->antivirus,
            'antispam' => $this->antispam,
            'dkim' => $this->dkim,
            'catchall' => $this->catchAll,
            'ssl' => $this->ssl,
            'letsencrypt' => $this->letsencrypt,
        ];
    }
}