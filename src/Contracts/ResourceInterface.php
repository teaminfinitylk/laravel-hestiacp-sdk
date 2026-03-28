<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Contracts;

use TeamInfinityLK\HestiaCP\Http\Connector;

interface ResourceInterface
{
    public function __construct(Connector $connector);

    public function list(): array;

    public function get(string $id): ?array;

    public function create(array $data): array;

    public function update(string $id, array $data): array;

    public function delete(string $id): bool;
}