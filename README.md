# Laravel HestiaCP SDK

A modern, fully-typed Laravel SDK for the [HestiaCP](https://hestiacp.com) Control Panel API.

[![PHP ^8.2](https://img.shields.io/badge/PHP-^8.2-blue)](https://www.php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-green)](LICENSE)
[![Laravel](https://github.com/teaminfinitylk/laravel-hestiacp-sdk/actions/workflows/tests.yml/badge.svg)](https://github.com/teaminfinitylk/laravel-hestiacp-sdk/actions/workflows/tests.yml)

---

## Installation

```bash
composer require teaminfinitylk/laravel-hestiacp-sdk
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="TeamInfinityLK\HestiaCP\Laravel\HestiaServiceProvider"
```

Add to your `.env`:

```env
HESTIA_URL=https://your-hestia-server:8083

# Option 1 — API Access Key (recommended)
HESTIA_API_KEY=ACCESS_KEY_ID:SECRET_ACCESS_KEY

# Option 2 — Username / Password
HESTIA_USERNAME=admin
HESTIA_PASSWORD=your-password

# Optional
HESTIA_TIMEOUT=30
HESTIA_VERIFY_SSL=true
```

> **Note:** HestiaCP's modern auth uses an access key pair in the format `ACCESS_KEY_ID:SECRET_ACCESS_KEY`.  
> Generate one from **Admin → Access Keys** in your HestiaCP panel.

---

## Quick Start

### Using the Facade

```php
use Hestia;

$users = Hestia::users()->list();
$domains = Hestia::web()->list('admin');
```

### Using Dependency Injection

```php
use TeamInfinityLK\HestiaCP\HestiaClient;

class HostingController extends Controller
{
    public function __construct(private HestiaClient $hestia) {}

    public function index()
    {
        return $this->hestia->users()->list();
    }
}
```

### Manual / Standalone Usage

```php
use TeamInfinityLK\HestiaCP\HestiaClient;

// With API key
$client = HestiaClient::connect('https://hestia.example.com:8083', 'KEYID:SECRET');

// With username + password
$client = HestiaClient::connectWithCredentials('https://hestia.example.com:8083', 'admin', 'password');
```

---

## Resources

All resources are accessed via the client. Methods that operate on user data require `$username` as the first argument.

### Users — `users()`

```php
$hestia->users()->list();                          // v-list-users
$hestia->users()->get('john');                     // v-list-user
$hestia->users()->create([
    'user'     => 'john',
    'password' => 'secret',
    'email'    => 'john@example.com',
    'package'  => 'default',    // optional
    'name'     => 'John',       // optional
    'lastname' => 'Doe',        // optional
]);                                                // v-add-user
$hestia->users()->changePassword('john', 'newpass');  // v-change-user-password
$hestia->users()->changeContact('john', 'email@example.com'); // v-change-user-contact
$hestia->users()->changePackage('john', 'premium');   // v-change-user-package
$hestia->users()->suspend('john');                 // v-suspend-user
$hestia->users()->unsuspend('john');               // v-unsuspend-user
$hestia->users()->delete('john');                  // v-delete-user
```

---

### Web Domains — `web()`

```php
$hestia->web()->list('john');                      // v-list-web-domains
$hestia->web()->get('john', 'example.com');        // v-list-web-domain
$hestia->web()->create([
    'user'    => 'john',
    'domain'  => 'example.com',
    'ip'      => '192.168.1.1',  // optional (defaults to server primary IP)
    'aliases' => 'www.example.com', // optional
]);                                                // v-add-web-domain
$hestia->web()->addSsl('john', 'example.com');    // v-add-letsencrypt-domain
$hestia->web()->suspend('john', 'example.com');   // v-suspend-web-domain
$hestia->web()->unsuspend('john', 'example.com'); // v-unsuspend-web-domain
$hestia->web()->delete('john', 'example.com');    // v-delete-web-domain
```

---

### Mail Domains & Accounts — `mail()`

```php
// Domains
$hestia->mail()->list('john');                     // v-list-mail-domains
$hestia->mail()->get('john', 'example.com');       // v-list-mail-domain
$hestia->mail()->create([
    'user'      => 'john',
    'domain'    => 'example.com',
    'antispam'  => 'yes',   // optional, default: yes
    'antivirus' => 'yes',   // optional, default: yes
    'dkim'      => 'yes',   // optional, default: yes
    'ssl'       => 'no',    // optional
]);                                                // v-add-mail-domain
$hestia->mail()->suspend('john', 'example.com');   // v-suspend-mail-domain
$hestia->mail()->unsuspend('john', 'example.com'); // v-unsuspend-mail-domain
$hestia->mail()->delete('john', 'example.com');    // v-delete-mail-domain

// Accounts
$hestia->mail()->listAccounts('john', 'example.com'); // v-list-mail-accounts
$hestia->mail()->addAccount([
    'user'     => 'john',
    'domain'   => 'example.com',
    'account'  => 'info',
    'password' => 'secret',
    'quota'    => '0',       // optional, 0 = unlimited
]);                                                // v-add-mail-account
$hestia->mail()->changeAccountPassword('john', 'example.com', 'info', 'newpass');
$hestia->mail()->deleteAccount('john', 'example.com', 'info'); // v-delete-mail-account
```

---

### Databases — `databases()`

> HestiaCP automatically prefixes database and DB user names with the owner's username.  
> e.g. user `john` + database `blog` → stored as `john_blog`.

```php
$hestia->databases()->list('john');                // v-list-databases
$hestia->databases()->get('john', 'john_blog');    // v-list-database
$hestia->databases()->create([
    'user'     => 'john',
    'database' => 'blog',      // stored as john_blog
    'dbuser'   => 'bloguser',  // stored as john_bloguser
    'dbpass'   => 'secret',
    'type'     => 'mysql',     // optional, default: mysql
    'charset'  => 'utf8mb4',   // optional
]);                                                // v-add-database
$hestia->databases()->changePassword('john', 'john_blog', 'john_bloguser', 'newpass');
$hestia->databases()->suspend('john', 'john_blog');   // v-suspend-database
$hestia->databases()->unsuspend('john', 'john_blog'); // v-unsuspend-database
$hestia->databases()->delete('john', 'john_blog');    // v-delete-database
```

---

### DNS — `dns()`

```php
// Zones
$hestia->dns()->list('john');                      // v-list-dns-domains
$hestia->dns()->get('john', 'example.com');        // v-list-dns-domain
$hestia->dns()->create([
    'user'   => 'john',
    'domain' => 'example.com',
    'ip'     => '192.168.1.1', // optional
]);                                                // v-add-dns-domain
$hestia->dns()->suspend('john', 'example.com');
$hestia->dns()->unsuspend('john', 'example.com');
$hestia->dns()->delete('john', 'example.com');    // v-delete-dns-domain

// Records
$hestia->dns()->listRecords('john', 'example.com');  // v-list-dns-records
$hestia->dns()->addRecord('john', 'example.com', [
    'record'   => 'mail',
    'type'     => 'A',
    'value'    => '192.168.1.2',
    'priority' => '0',         // optional
    'ttl'      => '14400',     // optional
]);                                                // v-add-dns-record
$hestia->dns()->updateRecord('john', 'example.com', 5, [...]);
$hestia->dns()->deleteRecord('john', 'example.com', 5); // v-delete-dns-record
```

---

### Cron Jobs — `cron()`

```php
$hestia->cron()->list('john');                     // v-list-cron-jobs
$hestia->cron()->get('john', 1);                   // v-list-cron-job (by numeric job ID)
$hestia->cron()->create([
    'user'    => 'john',
    'min'     => '0',
    'hour'    => '2',
    'day'     => '*',
    'month'   => '*',
    'wday'    => '*',
    'command' => '/home/john/scripts/backup.sh',
]);                                                // v-add-cron-job
$hestia->cron()->suspend('john', 1);              // v-suspend-cron-job
$hestia->cron()->unsuspend('john', 1);            // v-unsuspend-cron-job
$hestia->cron()->delete('john', 1);               // v-delete-cron-job
```

---

### Backups — `backups()`

```php
$hestia->backups()->list('john');                  // v-list-user-backups
$hestia->backups()->get('john', '2024-01-01_00-00-00.tar');
$hestia->backups()->create('john');                // v-backup-user (triggers backup now)
$hestia->backups()->restore('john', '2024-01-01_00-00-00.tar', [
    'web'  => 'yes',   // optional, default all yes
    'mail' => 'yes',
    'db'   => 'yes',
    'dns'  => 'yes',
]);                                                // v-restore-user
$hestia->backups()->delete('john', '2024-01-01_00-00-00.tar');
```

---

### Firewall — `firewall()`

```php
$hestia->firewall()->list();                       // v-list-firewall
$hestia->firewall()->get(1);                       // v-list-firewall-rule
$hestia->firewall()->create([
    'action'   => 'ACCEPT',      // ACCEPT | DROP | REJECT
    'ip'       => '0.0.0.0/0',
    'port'     => '80',
    'protocol' => 'TCP',         // optional
    'comment'  => 'Allow HTTP',  // optional
]);                                                // v-add-firewall-rule
$hestia->firewall()->delete(1);                    // v-delete-firewall-rule

// Bans (fail2ban)
$hestia->firewall()->listBans('CUSTOM');           // v-list-firewall-ban
$hestia->firewall()->banIp('1.2.3.4', 'CUSTOM');  // v-add-firewall-ban
$hestia->firewall()->unbanIp('1.2.3.4', 'CUSTOM');// v-delete-firewall-ban
```

---

### IP Addresses — `ips()`

> System-level IPs. Requires admin privileges.

```php
$hestia->ips()->list();                            // v-list-sys-ips
$hestia->ips()->get('192.168.1.100');              // v-list-sys-ip
$hestia->ips()->create([
    'ip'        => '192.168.1.100',
    'netmask'   => '255.255.255.0',
    'interface' => 'eth0',
    'user'      => 'admin',    // optional
    'status'    => 'shared',   // optional
]);                                                // v-add-sys-ip
$hestia->ips()->changeOwner('192.168.1.100', 'john');
$hestia->ips()->changeName('192.168.1.100', 'My IP');
$hestia->ips()->delete('192.168.1.100');           // v-delete-sys-ip
```

---

### Packages — `packages()`

```php
$hestia->packages()->list();                       // v-list-user-packages
$hestia->packages()->get('default');               // v-list-user-package
$hestia->packages()->delete('old-package');        // v-delete-user-package
```

---

## DTOs

Typed Data Transfer Objects are returned by `list()` calls:

| DTO | Returned by |
|-----|------------|
| `UserDto` | `users()->list()` |
| `WebDomainDto` | `web()->list()` |
| `MailDomainDto` | `mail()->list()` |
| `DatabaseDto` | `databases()->list()` |
| `DnsRecordDto` | `dns()->listRecords()` |

---

## Exception Handling

```php
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;
use TeamInfinityLK\HestiaCP\Exceptions\AuthenticationException;
use TeamInfinityLK\HestiaCP\Exceptions\ConnectionException;

try {
    $users = Hestia::users()->list();
} catch (AuthenticationException $e) {
    // Invalid API key / credentials
} catch (ConnectionException $e) {
    // Cannot reach the HestiaCP server
} catch (ApiException $e) {
    // HestiaCP returned an error code
    echo $e->getMessage();   // "Failed to list users: E_FORBIDDEN"
    echo $e->getCode();      // HestiaCP return code (1–20)
}
```

### HestiaCP Return Codes

| Code | Name | Meaning |
|------|------|---------|
| 0 | OK | Success |
| 1 | E_ARGS | Missing or invalid arguments |
| 2 | E_INVALID | Invalid object value |
| 3 | E_NOTEXIST | Object does not exist |
| 4 | E_EXISTS | Object already exists |
| 5 | E_SUSPENDED | Object is suspended |
| 6 | E_UNSUSPENDED | Object is not suspended |
| 7 | E_INUSE | Object is in use |
| 8 | E_LIMIT | Resource limit reached |
| 9 | E_PASSWORD | Invalid password |
| 10 | E_FORBIDDEN | Command not permitted |
| 11 | E_DISABLED | Feature disabled |
| 15 | E_CONNECT | Connection error |
| 17 | E_DB | Database error |

---

## Billing Automation Example

A common billing integration pattern — create a full hosting account:

```php
// 1. Create the user account
$hestia->users()->create([
    'user'     => 'john',
    'password' => 'generated-password',
    'email'    => 'john@example.com',
    'package'  => 'starter',
    'name'     => 'John',
    'lastname' => 'Doe',
]);

// 2. Add their web domain
$hestia->web()->create([
    'user'   => 'john',
    'domain' => 'johnssite.com',
]);

// 3. Enable Let's Encrypt SSL
$hestia->web()->addSsl('john', 'johnssite.com');

// 4. Set up mail
$hestia->mail()->create([
    'user'   => 'john',
    'domain' => 'johnssite.com',
]);
$hestia->mail()->addAccount([
    'user'     => 'john',
    'domain'   => 'johnssite.com',
    'account'  => 'info',
    'password' => 'mail-password',
]);

// 5. Create database
$hestia->databases()->create([
    'user'     => 'john',
    'database' => 'maindb',
    'dbuser'   => 'mainuser',
    'dbpass'   => 'db-password',
]);

// Suspend account on non-payment
$hestia->users()->suspend('john');

// Reactivate
$hestia->users()->unsuspend('john');

// Terminate
$hestia->users()->delete('john');
```

---

## Testing

```bash
./vendor/bin/pest
```

---

## Requirements

- PHP ^8.2
- cURL extension enabled
- HestiaCP server (any recent version)

---

## License

MIT © [TeamInfinity PVT LTD](https://teaminfinity.lk)