# HestiaCP SDK Examples

Ready-to-run PHP examples for every SDK feature. Replace the connection details at the top of each file before running.

## Files

| File | Topic |
|------|-------|
| `00-setup.php` | Connection methods (API key, credentials, SSL options) |
| `01-users.php` | User management — list, create, suspend, delete |
| `02-web-domains.php` | Web domains — list, create, SSL, all-users loop |
| `03-mail.php` | Mail domains & accounts — create, add accounts, change password |
| `04-databases.php` | MySQL/PostgreSQL databases — create, change password, delete |
| `05-dns.php` | DNS zones & records — A, MX, TXT, CNAME examples |
| `06-cron.php` | Cron jobs — common schedules, suspend, delete |
| `07-backups.php` | Backups — list, create, restore |
| `08-firewall-ip-packages.php` | Firewall rules, IP bans, system IPs, hosting packages |
| `09-exceptions.php` | Exception handling with error code reference |
| `10-billing-automation.php` | **Full billing lifecycle** — provision, suspend, upgrade, terminate |

## Quick Start

```bash
# Edit the connection details in any file
nano 01-users.php

# Run it
php 01-users.php
```

## Connection Setup

Change these two lines at the top of each example:

```php
$client = HestiaClient::connect(
    'https://your-hestia-server.com:8083',       // ← your server
    'YOUR_ACCESS_KEY_ID:YOUR_SECRET_ACCESS_KEY'   // ← your API key
);
```

> Get your API key from: **HestiaCP Panel → Admin → Access Keys → Add Key**
