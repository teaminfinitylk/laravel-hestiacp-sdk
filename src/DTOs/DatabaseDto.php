<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\DTOs;

use JsonSerializable;

class DatabaseDto implements JsonSerializable
{
    public function __construct(
        public readonly string $database,
        public readonly string $user,
        public readonly ?string $host = null,
        public readonly ?string $type = null,
        public readonly ?string $charset = null,
        public readonly ?string $diskUsage = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            database: $data['DATABASE'] ?? $data['database'] ?? '',
            user: $data['USER'] ?? $data['user'] ?? '',
            host: $data['HOST'] ?? $data['host'] ?? null,
            type: $data['TYPE'] ?? $data['type'] ?? null,
            charset: $data['CHARSET'] ?? $data['charset'] ?? null,
            diskUsage: $data['U_DISK'] ?? $data['disk_usage'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'database' => $this->database,
            'user' => $this->user,
            'host' => $this->host,
            'type' => $this->type,
            'charset' => $this->charset,
            'disk_usage' => $this->diskUsage,
        ];
    }
}