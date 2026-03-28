<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Http;

use ArrayAccess;
use Countable;
use Iterator;

class Response implements ArrayAccess, Countable, Iterator
{
    private array $data;
    private int $position = 0;

    public function __construct(
        public readonly int $statusCode,
        public readonly string $statusMessage,
        public readonly ?string $body = null,
        public readonly array $headers = [],
    ) {
        $this->data = $this->parseBody();
    }

    private function parseBody(): array
    {
        if ($this->body === null) {
            return [];
        }

        $decoded = json_decode($this->body, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function isError(): bool
    {
        return $this->statusCode >= 400;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]) || array_key_exists($key, $this->data);
    }

    public function all(): array
    {
        return $this->data;
    }

    // ArrayAccess implementation
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]) || array_key_exists($offset, $this->data);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

    // Countable implementation
    public function count(): int
    {
        return count($this->data);
    }

    // Iterator implementation
    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): mixed
    {
        return $this->data[$this->position] ?? null;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->data[$this->position]) || array_key_exists($this->position, $this->data);
    }
}