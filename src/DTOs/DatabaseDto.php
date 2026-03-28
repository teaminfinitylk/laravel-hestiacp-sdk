<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\DTOs;

use JsonSerializable;

/**
 * Represents a HestiaCP database.
 *
 * Field names come from the JSON response of v-list-database / v-list-databases.
 * HestiaCP prepends the username to both the database name and the db user name,
 * e.g. user "john" + database "blog" → stored as "john_blog".
 */
class DatabaseDto implements JsonSerializable
{
    public function __construct(
        public readonly string  $database,
        public readonly ?string $dbUser     = null,
        public readonly ?string $host       = null,
        public readonly ?string $type       = null,
        public readonly ?string $charset    = null,
        public readonly ?string $diskUsage  = null,
        public readonly ?string $suspended  = null,
        public readonly ?string $date       = null,
        public readonly ?string $time       = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            database:  $data['DATABASE'] ?? $data['database']  ?? '',
            dbUser:    $data['DBUSER']   ?? $data['db_user']   ?? null,
            host:      $data['HOST']     ?? $data['host']      ?? null,
            type:      $data['TYPE']     ?? $data['type']      ?? null,
            charset:   $data['CHARSET']  ?? $data['charset']   ?? null,
            diskUsage: $data['U_DISK']   ?? $data['disk_usage']?? null,
            suspended: $data['SUSPENDED']?? $data['suspended'] ?? null,
            date:      $data['DATE']     ?? $data['date']      ?? null,
            time:      $data['TIME']     ?? $data['time']      ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'database'   => $this->database,
            'db_user'    => $this->dbUser,
            'host'       => $this->host,
            'type'       => $this->type,
            'charset'    => $this->charset,
            'disk_usage' => $this->diskUsage,
            'suspended'  => $this->suspended,
            'date'       => $this->date,
            'time'       => $this->time,
        ];
    }
}