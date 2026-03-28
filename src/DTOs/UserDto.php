<?php

declare(strict_types=1);

namespace TeamInfinityLK\HestiaCP\DTOs;

use JsonSerializable;

/**
 * Represents a HestiaCP user account.
 *
 * Field names come from the JSON response of v-list-user / v-list-users
 * (uppercase keys). All fields beyond `user` are nullable so the DTO
 * can be safely constructed from partial data.
 */
class UserDto implements JsonSerializable
{
    public function __construct(
        public readonly string  $user,
        public readonly ?string $contact           = null,
        public readonly ?string $package           = null,
        public readonly ?string $language          = null,
        public readonly ?string $theme             = null,
        public readonly ?string $shell             = null,
        public readonly ?string $firstName         = null,
        public readonly ?string $lastName          = null,
        public readonly ?string $suspended         = null,
        public readonly ?string $suspendedReason   = null,
        public readonly ?string $ip                = null,
        public readonly ?string $ns                = null,
        // Resource quotas (as reported by HestiaCP — usually strings like "unlimited" or a number)
        public readonly ?string $quota             = null,
        public readonly ?string $bandwidth         = null,
        public readonly ?string $databases         = null,
        public readonly ?string $domains           = null,
        public readonly ?string $mailDomains       = null,
        public readonly ?string $dnsDomains        = null,
        public readonly ?string $cronJobs          = null,
        public readonly ?string $backups           = null,
        // Disk & traffic usage
        public readonly ?string $diskUsage         = null,
        public readonly ?string $bandwidthUsage    = null,
        // Timestamps
        public readonly ?string $createdDate       = null,
        public readonly ?string $createdTime       = null,
        public readonly ?string $lastLogin         = null,
        public readonly ?string $lastIp            = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            user:             $data['USER']             ?? $data['user']              ?? '',
            contact:          $data['CONTACT']          ?? $data['contact']           ?? null,
            package:          $data['PACKAGE']          ?? $data['package']           ?? null,
            language:         $data['LANGUAGE']         ?? $data['language']          ?? null,
            theme:            $data['THEME']            ?? $data['theme']             ?? null,
            shell:            $data['SHELL']            ?? $data['shell']             ?? null,
            firstName:        $data['NAME']             ?? $data['first_name']        ?? null,
            lastName:         $data['LNAME']            ?? $data['last_name']         ?? null,
            suspended:        $data['SUSPENDED']        ?? $data['suspended']         ?? null,
            suspendedReason:  $data['SUSPENDED_REASON'] ?? $data['suspended_reason']  ?? null,
            ip:               $data['IP']               ?? $data['ip']               ?? null,
            ns:               $data['NS']               ?? $data['ns']               ?? null,
            quota:            $data['QUOTA']            ?? $data['quota']             ?? null,
            bandwidth:        $data['BANDWIDTH']        ?? $data['bandwidth']         ?? null,
            databases:        $data['DATABASES']        ?? $data['databases']         ?? null,
            domains:          $data['WEB_DOMAINS']      ?? $data['domains']           ?? null,
            mailDomains:      $data['MAIL_DOMAINS']     ?? $data['mail_domains']      ?? null,
            dnsDomains:       $data['DNS_DOMAINS']      ?? $data['dns_domains']       ?? null,
            cronJobs:         $data['CRON_JOBS']        ?? $data['cron_jobs']         ?? null,
            backups:          $data['BACKUPS']          ?? $data['backups']           ?? null,
            diskUsage:        $data['U_DISK']           ?? $data['disk_usage']        ?? null,
            bandwidthUsage:   $data['U_BANDWIDTH']      ?? $data['bandwidth_usage']   ?? null,
            createdDate:      $data['DATE']             ?? $data['created_date']      ?? null,
            createdTime:      $data['TIME']             ?? $data['created_time']      ?? null,
            lastLogin:        $data['LAST_LOGIN']       ?? $data['last_login']        ?? null,
            lastIp:           $data['LAST_IP']          ?? $data['last_ip']           ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'user'              => $this->user,
            'contact'           => $this->contact,
            'package'           => $this->package,
            'language'          => $this->language,
            'theme'             => $this->theme,
            'shell'             => $this->shell,
            'first_name'        => $this->firstName,
            'last_name'         => $this->lastName,
            'suspended'         => $this->suspended,
            'suspended_reason'  => $this->suspendedReason,
            'ip'                => $this->ip,
            'ns'                => $this->ns,
            'quota'             => $this->quota,
            'bandwidth'         => $this->bandwidth,
            'databases'         => $this->databases,
            'domains'           => $this->domains,
            'mail_domains'      => $this->mailDomains,
            'dns_domains'       => $this->dnsDomains,
            'cron_jobs'         => $this->cronJobs,
            'backups'           => $this->backups,
            'disk_usage'        => $this->diskUsage,
            'bandwidth_usage'   => $this->bandwidthUsage,
            'created_date'      => $this->createdDate,
            'created_time'      => $this->createdTime,
            'last_login'        => $this->lastLogin,
            'last_ip'           => $this->lastIp,
        ];
    }
}