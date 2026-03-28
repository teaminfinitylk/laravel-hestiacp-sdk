# Security Policy

## Supported Versions

| Version | Supported |
|---------|-----------|
| 1.x (latest) | ✅ Yes |
| < 1.0 | ❌ No |

---

## Reporting a Vulnerability

**Please do NOT report security vulnerabilities as public GitHub issues.**

If you discover a security vulnerability — such as credential leakage, authentication bypass, or unsafe HTTP handling — please report it privately:

### How to Report

**Email:** security@teaminfinity.lk

Include as much detail as possible:

- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (optional)

### What to Expect

- **Acknowledgement** within 48 hours
- **Status update** within 5 business days
- We will work with you to understand and resolve the issue
- After the fix is released, you'll be credited in the CHANGELOG (unless you prefer to remain anonymous)

---

## Security Best Practices for SDK Users

### API Key Management

```php
// ✅ DO: Load credentials from environment variables
$client = HestiaClient::connect(
    baseUrl: env('HESTIA_URL'),
    apiKey:  env('HESTIA_API_KEY')
);

// ❌ DON'T: Hardcode credentials in source code
$client = HestiaClient::connect(
    'https://myserver.com:8083',
    'MYKEY:MYSECRET'   // Never commit this!
);
```

### Environment File

Add to `.env` (not committed to version control):

```env
HESTIA_URL=https://your-hestia-server.com:8083
HESTIA_API_KEY=ACCESS_KEY_ID:SECRET_ACCESS_KEY
```

Make sure `.env` is in `.gitignore`:

```bash
echo ".env" >> .gitignore
```

### SSL Verification

```php
// ✅ Always keep SSL verification enabled in production
HESTIA_VERIFY_SSL=true

// ⚠️ Only disable for local development with self-signed certs
HESTIA_VERIFY_SSL=false
```

### API Key Permissions

Create a **dedicated API key** with minimal required permissions:
- Generate from: HestiaCP Panel → Admin → Access Keys → Add Key
- Use a separate key per application
- Rotate keys regularly
- Delete unused keys immediately

### Logging

**Never log API requests or responses** that may contain passwords:

```php
// ❌ DO NOT log raw request bodies
Log::debug($response->all());  // May expose credentials

// ✅ Log only non-sensitive identifiers
Log::info('User created', ['username' => $username]);
```
