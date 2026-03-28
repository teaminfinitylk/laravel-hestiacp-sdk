<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\Http;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * HestiaCP API responses:
 *  - Success with data: HTTP 200, body = JSON object/array
 *  - Success no data:   HTTP 200, body = "0"
 *  - Error:             HTTP 200, body = numeric error code (1-20)
 */
class Response implements ArrayAccess, Countable, Iterator
{
    private array $data;
    private int $position  = 0;
    public readonly int $hestiaCode; // 0 = OK, >0 = error

    public function __construct(
        public readonly int $statusCode,
        public readonly string $statusMessage,
        public readonly ?string $body = null,
        public readonly array $headers = [],
    ) {
        [$this->hestiaCode, $this->data] = $this->parseBody();
    }

    private function parseBody(): array
    {
        $raw = trim($this->body ?? '');

        if ($raw === '') {
            return [0, []];
        }

        // HestiaCP returns a plain numeric code on error or simple success
        // "0" = OK (no data), "1"-"20" = error codes
        if (is_numeric($raw)) {
            $code = (int) $raw;
            return [$code, ['_code' => $code]];
        }

        // JSON response (list/get commands with arg1=json)
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return [0, $decoded];
        }

        return [0, ['_raw' => $raw]];
    }

    /**
     * True when HestiaCP return code is 0 (OK).
     */
    public function isSuccessful(): bool
    {
        return $this->hestiaCode === 0 && $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function isError(): bool
    {
        return $this->hestiaCode > 0 || $this->statusCode >= 400;
    }

    /**
     * Human-readable error name from HestiaCP return code table.
     */
    public function getErrorName(): string
    {
        return match ($this->hestiaCode) {
            0  => 'OK',
            1  => 'E_ARGS',
            2  => 'E_INVALID',
            3  => 'E_NOTEXIST',
            4  => 'E_EXISTS',
            5  => 'E_SUSPENDED',
            6  => 'E_UNSUSPENDED',
            7  => 'E_INUSE',
            8  => 'E_LIMIT',
            9  => 'E_PASSWORD',
            10 => 'E_FORBIDDEN',
            11 => 'E_DISABLED',
            12 => 'E_PARSING',
            13 => 'E_DISK',
            14 => 'E_LA',
            15 => 'E_CONNECT',
            16 => 'E_FTP',
            17 => 'E_DB',
            18 => 'E_RDD',
            19 => 'E_UPDATE',
            20 => 'E_RESTART',
            default => 'E_UNKNOWN',
        };
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function all(): array
    {
        return $this->data;
    }

    // ArrayAccess
    public function offsetExists(mixed $offset): bool  { return array_key_exists($offset, $this->data); }
    public function offsetGet(mixed $offset): mixed    { return $this->data[$offset] ?? null; }
    public function offsetSet(mixed $offset, mixed $v): void { $this->data[$offset] = $v; }
    public function offsetUnset(mixed $offset): void   { unset($this->data[$offset]); }

    // Countable
    public function count(): int { return count($this->data); }

    // Iterator
    public function rewind(): void  { $this->position = 0; }
    public function current(): mixed { return array_values($this->data)[$this->position] ?? null; }
    public function key(): mixed    { return array_keys($this->data)[$this->position] ?? null; }
    public function next(): void    { ++$this->position; }
    public function valid(): bool   { return $this->position < count($this->data); }
}