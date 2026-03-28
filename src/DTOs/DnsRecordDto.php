<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\DTOs;

use JsonSerializable;

class DnsRecordDto implements JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly string $record,
        public readonly string $type,
        public readonly string $value,
        public readonly ?int $priority = null,
        public readonly ?int $ttl = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['ID'] ?? $data['id'] ?? 0),
            record: $data['RECORD'] ?? $data['record'] ?? '',
            type: $data['TYPE'] ?? $data['type'] ?? '',
            value: $data['VALUE'] ?? $data['value'] ?? '',
            priority: isset($data['PRIORITY']) ? (int) $data['PRIORITY'] : ($data['priority'] ?? null),
            ttl: isset($data['TTL']) ? (int) $data['TTL'] : ($data['ttl'] ?? null),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'record' => $this->record,
            'type' => $this->type,
            'value' => $this->value,
            'priority' => $this->priority,
            'ttl' => $this->ttl,
        ];
    }
}