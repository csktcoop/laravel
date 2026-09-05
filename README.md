# Team Task Manager

A monorepo for a simple task management app with a Laravel backend and a Vue frontend. The project is structured around a shared domain layer so business rules can evolve independently from the delivery surface.

## Workspace layout

- `laravel/` — Laravel API, authentication, persistence, queues, and artisan commands
- `vue3/` — Vue 3 single-page app, routing, Pinia stores, and UI components
- `src/`` — shared domain concepts and DDD-oriented abstractions
- [Makefile](Makefile) — convenience commands for install, dev, lint, and tests

## Quick start

### Prerequisites

See [technical_stacks.md](technical_stacks.md) for exact runtime and dependency requirements.

#### pre — Optional (fresh Laravel installations only)
`make be -- php artisan cache:table`
`make be -- php artisan queue:table`

#### pre — OS Setup (unverified)

`make init`

This installs Nix, PHP 8, Node 24 and pnpm

### Setup

From the project root:

```bash
make install
```

This installs backend and frontend dependencies, creates a local environment file when needed, and optionally runs the initial database setup.

### Run locally

```bash
make dev
```

The development workflow runs:
- Laravel backend on http://localhost:8000
- Vue frontend on http://localhost:5173

For a full local developer workflow with queue worker and log viewer, use:

```bash
make dev-full
```

### Demo accounts

- Admin: admin@test.com / password
- User: user@test.com / password

> Production deployments should rotate these credentials to stronger values.

## Core capabilities

### Backend

- Laravel 12 API with Sanctum-based authentication
- Role-based access control for admin and user accounts
- Task CRUD flow with status and assignment handling
- Queue and logging support for local development

### Frontend

- Vue 3 SPA with Pinia state management
- Vue Router with guarded navigation
- Element Plus-based UI components and responsive layout
- TypeScript support for safer component development

## Development workflow

Useful commands from the repository root:

```bash
make install
make dev
make lint
make be-test
```

The main quality checks are routed through the root package scripts and the Laravel/Vue toolchains.

## Architecture overview

See [ARCHITECTURE.md](ARCHITECTURE.md) for the current system boundaries, data flow, and component responsibilities.

## License

MIT
