---
applyTo: "**/*.php"
description: Guidance for refactoring existing PHP code
---

# Refactoring

Refactor existing code with the smallest practical change set.

## Goals

- Preserve behavior.
- Improve readability.
- Reduce complexity.
- Increase maintainability.

## Process

Before refactoring:

1. Understand the current implementation.
2. Identify similar implementations.
3. Preserve public behavior.
4. Prefer incremental improvements.

## Design

Prefer:

- Extract methods before extracting classes.
- Improve naming before introducing abstractions.
- Remove duplication only when it has become a maintenance burden.
- Simplify conditionals.
- Reduce nesting with early returns.

Avoid:

- Large rewrites.
- Introducing new architectural patterns.
- Mixing refactoring with feature development.
- Changing unrelated files.

## SOLID

When modifying existing code:

- Improve adherence to SOLID only where it reduces complexity.
- Do not force SOLID if it makes the code harder to understand.

## KISS

Always prefer the simplest implementation that solves the current problem.

## YAGNI

Do not introduce extension points, interfaces, or abstractions without an existing need.

## Output

Changes should be:

- reviewable
- minimal
- behavior-preserving