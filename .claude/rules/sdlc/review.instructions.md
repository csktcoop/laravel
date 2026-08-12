---
applyTo: "**/*.php"
description: Guidance for reviewing code changes, pull requests, and refactoring
---

# Code Review

Review code with the goal of preventing defects from entering the codebase.

Do not assume the proposed change is correct.

Review for correctness first, then maintainability, then style.

---

# Review Workflow

1. Understand the intent of the change.
2. Inspect surrounding code, not only the modified lines.
3. Compare with existing implementations.
4. Identify correctness issues.
5. Identify maintainability issues.
6. Identify performance and security concerns.
7. Recommend the smallest practical improvements.

---

# Review Priorities

Review in this order:

1. Correctness
2. Security
3. Backward compatibility
4. Data integrity
5. Maintainability
6. Performance
7. Readability
8. Style

Never request style-only changes while correctness issues remain.

---

# Repository Consistency

Verify that the change:

- follows existing architecture
- follows existing naming conventions
- follows existing dependency injection patterns
- follows existing folder structure
- reuses existing services, helpers, repositories, and utilities
- does not introduce a second implementation of an existing pattern

Prefer consistency over novelty.

---

# PHP Review

Verify:

- syntax is compatible with the project's PHP version
- strict types are respected
- types are explicit where appropriate
- nullable handling is explicit
- exceptions are appropriate
- deprecated PHP features are not introduced
- unnecessary boilerplate is avoided

When newer PHP language features improve readability without breaking compatibility, recommend them.

---

# Static Analysis

Review as though the code must pass the project's configured static analysis.

Verify:

- PHPStan
- Psalm (if present)
- Rector compatibility
- Pint or PHP-CS-Fixer conventions

Do not recommend suppressing static-analysis warnings unless there is a documented project exception.

---

# Architecture

Verify that the change:

- preserves existing architecture
- does not introduce unnecessary abstractions
- applies KISS for new code
- applies YAGNI for speculative features
- improves SOLID only where modifying existing code

Reject unnecessary rewrites.

---

# Testing

Verify that:

- existing tests remain valid
- new behavior is tested
- edge cases are covered
- failure paths are tested when appropriate

Recommend additional tests only when they reduce meaningful risk.

Avoid requesting tests for trivial implementation details.

---

# Performance

Look for:

- unnecessary database queries
- N+1 problems
- repeated expensive operations
- unnecessary allocations
- inefficient algorithms

Recommend optimizations only when they provide measurable value.

Avoid micro-optimizations.

---

# Security

Verify:

- input validation
- output escaping
- authorization
- authentication
- SQL injection protection
- XSS protection
- CSRF protection where applicable
- secure secret handling

Never recommend insecure shortcuts.

---

# Refactoring

Suggest refactoring only when it:

- reduces complexity
- removes duplication
- improves readability
- improves maintainability

Avoid "clean code" suggestions that increase the size or complexity of the change.

---

# Review Output

Group findings by severity.

## Critical

Issues that may introduce:

- bugs
- security vulnerabilities
- data loss
- incorrect behavior

## Major

Issues that significantly affect:

- maintainability
- scalability
- correctness under edge cases

## Minor

Small improvements that:

- improve readability
- simplify code
- improve consistency

## Suggestions

Optional improvements that are safe to defer.

---

# Review Principles

Do not request changes solely because they match personal preference.

Every recommendation should have a clear technical justification.

Prefer incremental improvements over idealized redesigns.

If no meaningful issues are found, explicitly state that the code appears consistent with the project's conventions and explain any remaining assumptions.