---
applyTo: "**/*.php"
description: Security guidance
---

# Security

Security takes precedence over convenience.

## Input

Always:

- validate input
- sanitize where appropriate
- treat external input as untrusted

## Output

Escape output using the project's existing conventions.

## Database

Always use:

- prepared statements
- parameter binding

Never concatenate SQL.

## Authentication

Reuse the project's authentication mechanisms.

Do not invent custom authentication.

## Authorization

Reuse existing authorization policies and gates.

## Secrets

Never:

- hardcode credentials
- expose secrets
- log sensitive information

## PHP

Avoid:

- eval()
- unsafe unserialize()
- insecure random generators

Prefer:

- password_hash()
- password_verify()
- random_bytes()

## Changes

If a requested change introduces a security risk:

- explain the risk
- suggest the safer alternative