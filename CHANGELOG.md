# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
- Nothing yet

---

## [1.0.0] — 2026-03-29

### Added
- `HestiaClient` — main entry point with fluent resource accessors
- `Connector` — HTTP client posting to `/api/index.php` with modern Access Key auth and legacy username/password auth
- `UserResource` — `list()`, `get()`, `create()`, `changePassword()`, `changeContact()`, `changePackage()`, `suspend()`, `unsuspend()`, `delete()`
- `WebResource` — `list()`, `get()`, `create()`, `addSsl()`, `listAll()`, `suspend()`, `unsuspend()`, `delete()`
- `MailResource` — `list()`, `get()`, `create()`, `listAccounts()`, `addAccount()`, `changeAccountPassword()`, `deleteAccount()`, `suspend()`, `unsuspend()`, `delete()`
- `DatabaseResource` — `list()`, `get()`, `create()`, `changePassword()`, `suspend()`, `unsuspend()`, `delete()`
- `DnsResource` — `list()`, `get()`, `create()`, `listRecords()`, `addRecord()`, `updateRecord()`, `deleteRecord()`, `suspend()`, `unsuspend()`, `delete()`
- `CronResource` — `list()`, `get()`, `create()`, `suspend()`, `unsuspend()`, `delete()`
- `BackupResource` — `list()`, `get()`, `create()`, `restore()`, `delete()`
- `FirewallResource` — `list()`, `get()`, `create()`, `delete()`, `listBans()`, `banIp()`, `unbanIp()`
- `IpResource` — `list()`, `get()`, `create()`, `changeOwner()`, `changeName()`, `delete()`
- `PackageResource` — `list()`, `get()`, `delete()`
- DTOs: `UserDto`, `WebDomainDto`, `MailDomainDto`, `DatabaseDto`, `DnsRecordDto`
- `Response` — parses HestiaCP API responses, detects numeric error codes vs JSON data
- Laravel service provider and facade support
- Full `examples/` directory with 11 working PHP scripts
- Pest test suite

### Fixed
- **Critical:** All resource classes were calling non-existent REST endpoints (`/api/v1/list/...`). Rewrote to use HestiaCP's actual single endpoint `POST /api/index.php` with `v-*` CLI commands
- **Critical:** `list()` and `get()` methods returned empty arrays because `returncode=yes` suppresses JSON output from HestiaCP. Fixed by passing `$returnCode=false` to `execute()` for all read commands
- `ResourceInterface` — removed incompatible method signatures that didn't match HestiaCP's user-scoped command pattern
- `UserDto` — added missing fields (`NAME`, `LNAME`, `SHELL`, `IP`, `NS`, quota limits, disk/bandwidth usage)
- `MailDomainDto` — fixed `yes`/`no` → `bool` conversion; added missing fields
- `DatabaseDto` — corrected `DBUSER` key mapping; added `suspended`, `date`, `time` fields

---

[Unreleased]: https://github.com/teaminfinitylk/laravel-hestiacp-sdk/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/teaminfinitylk/laravel-hestiacp-sdk/releases/tag/v1.0.0
