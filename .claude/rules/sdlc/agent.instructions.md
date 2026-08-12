---
applyTo: "**"
description: General workflow for AI agents
---

# Agent Workflow

1. Understand the request.
2. Inspect the repository.
3. Discover project constraints.
4. Lock those constraints.
5. Search for existing implementations.
6. Reuse existing code.
7. Make the smallest possible change.
8. Verify against project rules before finishing.

# Repository First

Before generating code:

- Search for existing implementations.
- Reuse services.
- Reuse repositories.
- Reuse helpers.
- Follow naming conventions.
- Follow folder structure.
- Follow dependency injection patterns.

Never introduce a second architecture into an existing project.

# Existing Pattern Analysis

Before introducing a new implementation:

- Identify similar code in the repository.
- Follow the dominant pattern unless there is a clear reason to improve it.
- Prefer consistency over novelty.

# Project Discovery

Inspect the repository.

Discover:

- PHP version
- Laravel version
- Static analysis configuration
- Coding standard
- Testing framework
- Existing architecture
- Dependency Injection style

Search:

- composer.json
- phpstan.neon
- rector.php
- phpunit.xml
- pest.php
- pint.json
- php-cs-fixer.php
- README.md
- docs/

# Lock Constraints

Treat discovered configuration files as the project's source of truth.

Examples:

- composer.json determines supported PHP features.
- phpstan.neon determines the required PHPStan level.
- pint.json or php-cs-fixer.php determines formatting.
- phpunit.xml or pest.php determines the testing framework.

Do not ask again unless the configuration is ambiguous.

Do not override repository conventions with generic best practices.

# Conflict Resolution

When multiple sources define project conventions, use them in this order:

1. Explicit user instructions in the current conversation.
2. Repository configuration files (composer.json, phpstan.neon, pint.json, etc.).
3. Project documentation (README.md, docs/).
4. Existing code patterns in the affected area.
5. Framework conventions (e.g., Laravel Boost knowledge).
6. General language or industry best practices.

If a conflict cannot be resolved confidently, ask a clarifying question before making changes.