## Summary

<!-- Describe what this PR does. What problem does it solve? What feature does it add? -->

## Type of Change

- [ ] 🐛 Bug fix (non-breaking change that fixes an issue)
- [ ] ✨ New feature (non-breaking change that adds functionality)
- [ ] 💥 Breaking change (fix or feature that would cause existing functionality to change)
- [ ] 📝 Documentation update
- [ ] 🧪 Test improvement
- [ ] ♻️ Refactor (no functional change)

## Related Issues

Closes #<!-- issue number -->

## Changes Made

<!-- List the files changed and what changed in each -->

- 

## HestiaCP Commands Used

<!-- If adding a new resource method, list the v-* commands it wraps -->

| Method | Command |
|--------|---------|
| `list()` | `v-list-` |
| `create()` | `v-add-` |

## Checklist

- [ ] `./vendor/bin/pest` passes (exit code 0)
- [ ] New/changed functionality has tests in `tests/`
- [ ] `README.md` updated if the public API changed
- [ ] `examples/` updated if applicable
- [ ] `CHANGELOG.md` updated under `[Unreleased]`
- [ ] List/get commands use `execute(..., false)` (no `returncode=yes`)
- [ ] Action commands use `execute(...)` default (with `returncode=yes`)
- [ ] `declare(strict_types=1)` present in all new PHP files
