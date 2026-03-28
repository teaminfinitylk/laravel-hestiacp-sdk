# Laravel HestiaCP SDK

A modern Laravel SDK for the Hestia Control Panel (HestiaCP) API.

## Installation

```bash
composer require teaminfinitylk/laravel-hestiacp-sdk
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="TeamInfinityLK\HestiaCP\Laravel\HestiaServiceProvider"
```

Add the following to your `.env` file:

```env
HESTIA_URL=https://your-hestia-server:8083
HESTIA_API_KEY=your-api-key
```

Or use username/password authentication:

```env
HESTIA_URL=https://your-hestia-server:8083
HESTIA_USERNAME=admin
HESTIA_PASSWORD=your-password
```

## Usage

### Using the Facade

```php
use Hestia;

// List all users
$users = Hestia::users()->list();

// Create a new web domain
Hestia::web()->create([
    'domain' => 'example.com',
    'ip' => '192.168.1.1',
    'aliases' => 'www.example.com',
]);
```

### Using Dependency Injection

```php
use TeamInfinityLK\HestiaCP\HestiaClient;

class MyController
{
    public function __construct(
        private HestiaClient $hestia
    ) {}

    public function listUsers()
    {
        return $this->hestia->users()->list();
    }
}
```

### Manual Connection

```php
use TeamInfinityLK\HestiaCP\HestiaClient;

// Using API Key
$client = HestiaClient::connect('https://hestia.example.com:8083', 'your-api-key');

// Using Credentials
$client = HestiaClient::connectWithCredentials('https://hestia.example.com:8083', 'admin', 'password');

// Access resources
$users = $client->users()->list();
```

## Available Resources

- `users()` - User management
- `web()` - Web domains
- `mail()` - Mail domains
- `databases()` - Databases
- `dns()` - DNS zones and records
- `cron()` - Cron jobs
- `backups()` - Backup management
- `packages()` - Hosting packages
- `ips()` - IP addresses
- `firewall()` - Firewall rules

## DTOs

The SDK includes typed DTOs for consistent data handling:

- `UserDto`
- `WebDomainDto`
- `MailDomainDto`
- `DatabaseDto`
- `DnsRecordDto`

## Exception Handling

```php
use TeamInfinityLK\HestiaCP\Exceptions\ApiException;
use TeamInfinityLK\HestiaCP\Exceptions\AuthenticationException;
use TeamInfinityLK\HestiaCP\Exceptions\ConnectionException;

try {
    $users = Hestia::users()->list();
} catch (AuthenticationException $e) {
    // Handle authentication errors
} catch (ConnectionException $e) {
    // Handle connection errors
} catch (ApiException $e) {
    // Handle API errors
}
```

## Testing

```bash
./vendor/bin/pest
```

## License

MIT