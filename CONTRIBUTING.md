# Contributing to Laravel HestiaCP SDK

Thank you for considering contributing! Every contribution — bug reports, feature suggestions, code, or documentation — is welcome and appreciated.

---

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How to Report a Bug](#how-to-report-a-bug)
- [How to Request a Feature](#how-to-request-a-feature)
- [Development Setup](#development-setup)
- [Making Changes](#making-changes)
- [Coding Standards](#coding-standards)
- [Running Tests](#running-tests)
- [Pull Request Process](#pull-request-process)

---

## Code of Conduct

By participating, you agree to abide by our [Code of Conduct](CODE_OF_CONDUCT.md). Please read it before contributing.

---

## How to Report a Bug

> **Security vulnerabilities** must be reported privately. See [SECURITY.md](SECURITY.md).

For general bugs, [open an issue](https://github.com/teaminfinitylk/laravel-hestiacp-sdk/issues/new?template=bug_report.md) with:

- **SDK version**: `composer show teaminfinitylk/laravel-hestiacp-sdk`
- **PHP version**: `php -v`
- **HestiaCP version**: from your panel footer
- **Minimal reproduction** (what you called, what you expected, what happened)
- **Error message** (if any)

---

## How to Request a Feature

[Open a feature request](https://github.com/teaminfinitylk/laravel-hestiacp-sdk/issues/new?template=feature_request.md) and describe:

- What HestiaCP CLI command(s) you want to wrap
- Your use case / why it's useful for billing automation
- The API signature you'd expect

---

## Development Setup

```bash
# 1. Fork the repo on GitHub, then clone your fork
git clone https://github.com/<your-username>/laravel-hestiacp-sdk.git
cd laravel-hestiacp-sdk

# 2. Install dependencies
composer install

# 3. Run the test suite to confirm everything works
./vendor/bin/pest
```

---

## Making Changes

```bash
# 1. Create a branch from main
git checkout -b feat/my-feature-name
# or
git checkout -b fix/my-bug-fix

# 2. Make your changes

# 3. Write or update tests

# 4. Run the full test suite — it must pass
./vendor/bin/pest

# 5. Commit with a clear message (see below)
git commit -m "feat: add v-list-sys-services support to SystemResource"

# 6. Push and open a PR against the main branch
git push origin feat/my-feature-name
```

### Commit Message Format

We use [Conventional Commits](https://www.conventionalcommits.org/):

| Prefix | When to use |
|--------|-------------|
| `feat:` | New feature or new resource method |
| `fix:` | Bug fix |
| `docs:` | Documentation only |
| `test:` | Test additions or corrections |
| `refactor:` | Code change that doesn't add a feature or fix a bug |
| `chore:` | Dependency updates, build scripts |

---

## Coding Standards

- **PHP 8.2+** — use readonly properties, match expressions, named arguments where appropriate
- **Strict types** — all files must have `declare(strict_types=1);`
- **No REST endpoints** — all HestiaCP interactions use `$connector->execute('v-command', [...args])`. See [Connector.php](src/Http/Connector.php)
- **List/get commands** → pass `false` as the third arg to `execute()` (no `returncode=yes`)
- **Action commands** → use the default `true` (send `returncode=yes`)
- **DTOs** → resource `list()` methods should return typed DTOs, not raw arrays
- Follow existing patterns from `UserResource.php` and `WebResource.php` as reference implementations

---

## Running Tests

```bash
# Run all tests
./vendor/bin/pest

# Run a specific test file
./vendor/bin/pest tests/Feature/UserResourceTest.php

# Run with coverage (requires Xdebug or PCOV)
./vendor/bin/pest --coverage
```

All PRs **must** include tests for new functionality or bug fixes.

---

## Pull Request Process

1. Ensure the test suite passes (`./vendor/bin/pest` exits with code 0)
2. Update documentation ([README.md](README.md) and relevant example in `examples/`) if the public API changes
3. Update [CHANGELOG.md](CHANGELOG.md) under the `[Unreleased]` section
4. A maintainer will review your PR within a few days

---

## Adding a New Resource

If you're adding a new HestiaCP resource class, follow this checklist:

- [ ] Create `src/Resources/YourResource.php` using `execute()` pattern
- [ ] Use `execute('v-list-...', [..., 'json'], false)` for list/get methods
- [ ] Use `execute('v-add-...', [...])` for action methods (default `returnCode=true`)
- [ ] Add the resource accessor to `HestiaClient.php`
- [ ] Create a DTO in `src/DTOs/` if the list method returns structured data
- [ ] Add `tests/Feature/YourResourceTest.php` with unit tests
- [ ] Add `examples/XX-your-resource.php` with clear examples
- [ ] Update `README.md` with the new resource section

---

Thank you for contributing! 🎉
