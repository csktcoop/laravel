---
applyTo: "**/*.php"
description: Project-wide PHP coding standards
---

# PHP Development Instructions

##  PHP Language

Target the PHP version declared in `composer.json`.

Do not generate syntax unavailable in that version.

Prefer modern language features supported by the project's minimum PHP version.

<!-- DISABLED
## PHP Version Policy

- Minimum supported version: **PHP 8.3**
- Preferred target version: **PHP 8.5**
- Generated code must remain compatible with PHP 8.3 unless explicitly instructed otherwise.
- When a PHP 8.4 or PHP 8.5 feature significantly improves readability, maintainability, safety, or performance, prefer it **only if the project supports that version**.
-->

---

# General Coding Style

Write modern PHP.

Prefer:

- strict typing
- immutable objects where practical
- readonly properties/classes
- constructor property promotion
- enums instead of magic strings
- match instead of switch when appropriate
- named arguments when clarity improves readability
- first-class callables
- explicit visibility
- final classes unless inheritance is intentional
- explicit return types
- explicit parameter types
- union/intersection types where appropriate

Always include:

```php
declare(strict_types=1);
```

Avoid:

- mixed unless unavoidable
- dynamic properties
- magic values
- unnecessary inheritance
- public mutable state
- global state
- suppressing errors with @
- implicit type conversions
- loose comparisons (`==`)
```

---

# PHP 8.3 Features

Prefer using:

- typed class constants
- json_validate()
- readonly classes
- improved random extension
- anonymous readonly classes when appropriate

---

# PHP 8.4 Features

Use when project supports PHP 8.4:

- Property Hooks instead of boilerplate getters/setters.
- Asymmetric property visibility.
- New array helpers:
  - array_find()
  - array_find_key()
  - array_any()
  - array_all()
- BCMath objects.
- Constructor chaining improvements.
- Reduce unnecessary boilerplate using modern language features.

Do not generate traditional getters/setters unless they contain business logic.

---

# PHP 8.5 Features

Use when project supports PHP 8.5:

Prefer:

- Pipe operator (`|>`) for readable transformation pipelines.
- Closures in constant expressions.
- clone() with property replacement for immutable value objects.
- Standard-compliant URL/URI classes instead of parse_url() where applicable.
- #[NoDiscard] for APIs whose return values must be consumed.
- FILTER_THROW_ON_FAILURE instead of checking false/null manually.
- array_first()
- array_last()

Avoid older patterns when an equivalent PHP 8.5 feature is clearer.

---

# Architecture

Prefer:

- Composition over inheritance.
- Dependency Injection.
- Small focused classes.
- SOLID principles.
- PSR standards.
- Domain-driven naming.
- Immutable value objects.
- Pure functions where practical.

Avoid God Objects.

---

# Design Principles

For new code:

- KISS first.
- Apply YAGNI.
- Avoid speculative abstractions.

For existing code:

- Preserve the current architecture.
- Improve SOLID only where it reduces complexity.
- Refactor incrementally.

---

# Error Handling

Never silence exceptions.

Prefer:

- Exceptions instead of error codes.
- Specific exception types.
- Early validation.
- Early return.
- Fail fast.

---

# Performance

Avoid premature optimization.

Prefer:

- Generator when processing large datasets.
- Lazy evaluation.
- Efficient array operations.
- OPcache-friendly code.
- Avoid unnecessary allocations.

---

# Security

Always:

- Escape output.
- Validate all input.
- Use prepared statements.
- Use password_hash().
- Use password_verify().
- Avoid eval().
- Avoid unserialize() on untrusted data.
- Prefer random_bytes() for cryptography.

Never generate insecure authentication code.

---

# Documentation

Document public APIs.

Prefer PHPDoc only when it adds information beyond native types.

Avoid redundant comments.

Good comments explain *why*, not *what*.

---

# Testing

Generate code that is easily testable.

Prefer:

- PHPUnit
- Pest when requested
- Dependency Injection
- No hidden state
- Small methods

---

# Static Analysis

Generated code should pass:

- PHPStan (highest practical level)
- Psalm
- Rector
- PHP CS Fixer or Pint

Do not generate code that intentionally ignores static analysis errors.

---

<!-- DISABLED
# Composer

Prefer modern Composer practices.

Do not suggest abandoned packages.

Prefer actively maintained libraries.

---
-->

# Output Rules

Always produce:

- production-ready code
- explicit types
- readable code
- maintainable code
- minimal complexity

Prefer readability over cleverness.

When multiple approaches exist:

1. safest
2. most maintainable
3. modern PHP
4. best performance

Explain trade-offs when appropriate.

Generated changes should:

- be minimal
- preserve existing conventions
- avoid unrelated refactoring
- explain significant trade-offs only when necessary