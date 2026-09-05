# Laravel BFF

## Core Package Selection

- Use `laravel/framework` for the core application runtime, routing, ORM, validation, and service container.
- Use `laravel/sanctum` for lightweight API authentication so the SPA can sign in with tokens without bringing in a heavier JWT implementation.
- Keep the package set focused on the current scope: authentication, CRUD APIs, and a simple local development workflow.

## Controlling

In controller, `deferredPaginate()` instead of `paginate()`. E.g. `News::orderBy('id', 'desc')->deferredPaginate(20)`

## Modelling

(Database Portability, Events Triggers, Queue/Cache Invalidation, `$model->touch()`) In model, `public $timestamps = true;` instead of MySQL `ON UPDATE current_timestamp()`. It preserve database flexibility (PostgreSQL/SQLite switch), ensure cache/queue syncing, correctly trigger Eloquent Model Events and force refresing the `updated_at` column via `$model->touch()`.

## DDD (Domain-driven Design) architecture paradism

### Code Structure

- Domain/Task
   - Http/Controllers/TaskController
   - Models/Task
   - Repositories
      - TaskRepository
      - TaskRepositoryInterface
   - Resources/views
      - edit
      - index

## Dynamic route Definition

Routes should stay explicit and readable in the application layer. Dynamic route construction is acceptable only when it removes repetition for a known set of resources, but it should still preserve clear names and centralized route definitions rather than being scattered across components.

## Boost

- Tag: local post-migration-boost-index

(local only) Auto-run `boost:index` after Migration . Zero-latency context for Copilot/Cursor. The second your DB changes, the AI "knows" the new columns without a manual re-index.

# Database

For development, use SQLite instead of MariaDB (MySQL) or PostgreSQL. We try to config SQLite to behavior as close as MySQL.

## SQLite

### Init SQLs

- Tag: sqlite-init

To better align SQLite's behavior with a `production MySQL environment` on a local development machine, the following PRAGMAs are applied:

1. **Non-blocking Concurrency (`journal_mode=WAL`):** Slightly simulates MySQL's MVCC. Allows multiple readers and one writer to coexist. Without this (default `DELETE` mode), a write operation requires an exclusive lock, blocking all readers.
2. **Data Integrity (`foreign_keys=ON`):** Mimics MySQL's default foreign key enforcement. SQLite often has this disabled by default, which can lead to orphaned records in tests that would fail in production.
3. **Performance & Sync (`synchronous=NORMAL`):** Mimics MySQL's disk I/O buffering. It reduces the frequency of `fsync` operations (waiting for the physical disk platter to spin), significantly improving performance on low-end hardware without risking DB corruption. Default `FULL`.
4. **Lock Contention (`busy_timeout=5000`):** Equivalent to MySQL's `innodb_lock_wait_timeout`. Instead of failing instantly with a "Database is locked" error, SQLite will wait up to 5 seconds for a lock to be released. Default `0`.

# SQLite-MySQL gaps

- SQLite is acceptable for local development, but it still differs from MySQL in some edge cases around constraint handling, collation, and transactional behavior.
- Keep migrations and tests aware of those differences so local results do not accidentally imply production parity.
- Prefer MySQL-compatible SQL patterns and verify critical flows against the intended production database when behavior is ambiguous.

---

# Frontend (Vue 3)

## typescript + vue-tsc (strict mode)

Strict mode catches bugs at compile time, avoiding runtime type hell

## PNPM

Hard links save disk space, workspace monorepo natively supports it, clear dependency isolation

## Pinia

Simple API, TS-friendly, no need for Vuex complexity

---

# Git Ops

- Keep changes small and reviewable by separating refactors, feature work, and documentation updates.
- Use descriptive commit messages that explain the intent of the change, not just the file names touched.
- Prefer rebasing or squashing when the branch history would otherwise obscure the reasoning behind a change.

---

# Core Attributes of Robust System Architecture

## Reliability (retry, timeout)

- Prefer idempotent requests where possible and handle transient server failures with bounded retries.
- Keep request timeouts explicit so a stalled dependency does not block the user indefinitely.
- Surface actionable errors to the UI while preserving the original exception context for debugging.

## Performance (optimization, benchmarking)

- Optimize the hot path first: avoid unnecessary round-trips, large payloads, and repeated queries.
- Profile expensive UI flows and database access patterns before introducing premature micro-optimizations.
- Use caching and pagination selectively where they improve perceived responsiveness without increasing complexity.

## Security (implementation; CIA: confidentiality, integrity, availability)

- Treat authentication tokens as secrets and never expose them in logs or client-side debug output.
- Validate input on both the client and the server to reduce accidental misuse and protect the API boundary.
- Preserve least-privilege access and enforce authorization checks on the backend.

## Scalability (horizontal & vertical scaling)

- Keep services stateless where possible so they can scale horizontally without special session affinity.
- Design modules around clear boundaries so new features can be added without tightly coupling unrelated components.
- Favor queue-based and asynchronous processing for work that is not required to complete inline.

## Observability (metrics, logs, traces; goal: minimal MTTR)

- Emit logs with enough context to understand the request path and the user action that triggered it.
- Capture timing and error metrics for the most important API and UI flows.
- Keep local observability tools simple so regressions are easy to diagnose before they reach production.

---

# AI Engineer

Use AI assistance to accelerate implementation, but keep the human review loop explicit for correctness, architectural intent, and operational risk.

## GraphRAG

GraphRAG is useful when a project needs to connect code, documentation, and domain knowledge into a navigable knowledge graph. Use it when the problem requires cross-cutting context rather than a single file change.

## Agentic RAG

Agentic RAG is appropriate for iterative tasks that need multiple steps of retrieval, reasoning, and action. It should be used to reduce context loss and support multi-step implementation rather than as a substitute for clear requirements.

## RAG Evaluation

Evaluate retrieval quality, answer usefulness, and factual grounding regularly. The goal is not just to retrieve information, but to ensure the resulting output is relevant, correct, and safe to act on.

---

# Idempotency

TBD

# Reconciliation

TBD

# Snowflake ID + Sqids

TBD