<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\DTOs;

use JsonSerializable;

class UserDto implements JsonSerializable
{
    public function __construct(
        public readonly string $user,
        public readonly ?string $contact = null,
        public readonly ?string $package = null,
        public readonly ?string $language = null,
        public readonly ?string $theme = null,
        public readonly ?bool $active = null,
        public readonly ?int $suspended = null,
        public readonly ?string $suspendedReason = null,
        public readonly ?string $createdDate = null,
        public readonly ?string $createdTime = null,
        public readonly ?string $lastLogin = null,
        public readonly ?string $lastIp = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            user: $data['USER'] ?? $data['user'] ?? '',
            contact: $data['CONTACT'] ?? $data['contact'] ?? null,
            package: $data['PACKAGE'] ?? $data['package'] ?? null,
            language: $data['LANGUAGE'] ?? $data['language'] ?? null,
            theme: $data['THEME'] ?? $data['theme'] ?? null,
            active: isset($data['ACTIVE']) ? (bool) $data['ACTIVE'] : ($data['active'] ?? null),
            suspended: isset($data['SUSPENDED']) ? (int) $data['SUSPENDED'] : ($data['suspended'] ?? null),
            suspendedReason: $data['SUSPENDED_REASON'] ?? $data['suspended_reason'] ?? null,
            createdDate: $data['DATE'] ?? $data['created_date'] ?? null,
            createdTime: $data['TIME'] ?? $data['created_time'] ?? null,
            lastLogin: $data['LAST_LOGIN'] ?? $data['last_login'] ?? null,
            lastIp: $data['LAST_IP'] ?? $data['last_ip'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'user' => $this->user,
            'contact' => $this->contact,
            'package' => $this->package,
            'language' => $this->language,
            'theme' => $this->theme,
            'active' => $this->active,
            'suspended' => $this->suspended,
            'suspended_reason' => $this->suspendedReason,
            'created_date' => $this->createdDate,
            'created_time' => $this->createdTime,
            'last_login' => $this->lastLogin,
            'last_ip' => $this->lastIp,
        ];
    }
}