# laravel

## Practices

1. In model, use `public $timestamps = true;` instead of MySQL `ON UPDATE current_timestamp()`
2. DDD (Domain-driven Design) architecture paradism

## DDD (Task)

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


# Boost

- Tag: local post-migration-boost-index

(local only) Auto-run `boost:index` after Migration . Zero-latency context for Copilot/Cursor. The second your DB changes, the AI "knows" the new columns without a manual re-index.


# SQLite

## Init SQLs

- Tag: sqlite-init

To better align SQLite's behavior with a `production MySQL environment` on a local development machine, the following PRAGMAs are applied:

1. **Non-blocking Concurrency (`journal_mode=WAL`):** Slightly simulates MySQL's MVCC. Allows multiple readers and one writer to coexist. Without this (default `DELETE` mode), a write operation requires an exclusive lock, blocking all readers.
2. **Data Integrity (`foreign_keys=ON`):** Mimics MySQL's default foreign key enforcement. SQLite often has this disabled by default, which can lead to orphaned records in tests that would fail in production.
3. **Performance & Sync (`synchronous=NORMAL`):** Mimics MySQL's disk I/O buffering. It reduces the frequency of `fsync` operations (waiting for the physical disk platter to spin), significantly improving performance on low-end hardware without risking DB corruption. Default `FULL`.
4. **Lock Contention (`busy_timeout=5000`):** Equivalent to MySQL's `innodb_lock_wait_timeout`. Instead of failing instantly with a "Database is locked" error, SQLite will wait up to 5 seconds for a lock to be released. Default `0`.

