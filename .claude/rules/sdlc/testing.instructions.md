---
applyTo: "**/{tests,Test,Tests}/**"
description: Testing guidance
---

# Testing

Generate tests that validate observable behavior.

## Discover

Determine whether the project uses:

- PHPUnit
- Pest

Mirror the existing style.

## Test Design

Cover:

- happy path
- edge cases
- failure cases

Prefer:

- descriptive test names
- data providers where appropriate
- deterministic tests
- isolated tests

Avoid:

- unnecessary mocking
- testing implementation details
- duplicated assertions

## Test Quality

Tests should be:

- readable
- maintainable
- independent
- repeatable

## Changes

When adding features:

- update existing tests when appropriate
- add only the minimum new tests required