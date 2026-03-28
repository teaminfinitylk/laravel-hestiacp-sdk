<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\DTOs;

use JsonSerializable;

/**
 * Represents a HestiaCP mail domain.
 *
 * HestiaCP returns booleans as 'yes'/'no' strings — we normalise them here.
 */
class MailDomainDto implements JsonSerializable
{
    public function __construct(
        public readonly string  $domain,
        public readonly ?bool   $antivirus   = null,
        public readonly ?bool   $antispam    = null,
        public readonly ?bool   $dkim        = null,
        public readonly ?string $catchAll    = null,  // email address or empty string
        public readonly ?bool   $ssl         = null,
        public readonly ?bool   $letsencrypt = null,
        public readonly ?string $diskUsage   = null,
        public readonly ?string $suspended   = null,
        public readonly ?string $date        = null,
        public readonly ?string $time        = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            domain:      $data['DOMAIN']      ?? $data['domain']      ?? '',
            antivirus:   self::toBool($data['ANTIVIRUS']   ?? $data['antivirus']   ?? null),
            antispam:    self::toBool($data['ANTISPAM']    ?? $data['antispam']    ?? null),
            dkim:        self::toBool($data['DKIM']        ?? $data['dkim']        ?? null),
            catchAll:    $data['CATCHALL']   ?? $data['catchall']   ?? null,
            ssl:         self::toBool($data['SSL']         ?? $data['ssl']         ?? null),
            letsencrypt: self::toBool($data['LETSENCRYPT'] ?? $data['letsencrypt'] ?? null),
            diskUsage:   $data['U_DISK']     ?? $data['disk_usage']  ?? null,
            suspended:   $data['SUSPENDED']  ?? $data['suspended']   ?? null,
            date:        $data['DATE']       ?? $data['date']        ?? null,
            time:        $data['TIME']       ?? $data['time']        ?? null,
        );
    }

    /**
     * Normalise HestiaCP's yes/no strings, existing bool, or null.
     */
    private static function toBool(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }
        if (is_bool($value)) {
            return $value;
        }
        return strtolower((string) $value) === 'yes';
    }

    public function jsonSerialize(): array
    {
        return [
            'domain'      => $this->domain,
            'antivirus'   => $this->antivirus,
            'antispam'    => $this->antispam,
            'dkim'        => $this->dkim,
            'catchall'    => $this->catchAll,
            'ssl'         => $this->ssl,
            'letsencrypt' => $this->letsencrypt,
            'disk_usage'  => $this->diskUsage,
            'suspended'   => $this->suspended,
            'date'        => $this->date,
            'time'        => $this->time,
        ];
    }
}