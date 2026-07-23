# Team Task Manager

A monorepo for a task management application with a Laravel 12 backend and a Vue 3 frontend. The project is structured around a shared domain layer so business rules can evolve independently from the delivery surface.

## Documentation structure

This repository keeps documentation intentionally small and focused:

- [README.md](README.md) — project overview, setup, and entry points
- [ARCHITECTURE.md](ARCHITECTURE.md) — system design, boundaries, and request flow
- [TECH_STACKS.md](TECH_STACKS.md) — runtime versions, frameworks, and tooling
- [PRACTICES.md](PRACTICES.md) — coding and architecture conventions
- [ISSUES.md](ISSUES.md) — tracked issues and follow-up work
- [DOCUMENTATION_PRACTICES.md](DOCUMENTATION_PRACTICES.md) — guidance for keeping docs useful for humans and AI agents

## Workspace layout

- [laravel12/](laravel12/) — Laravel API, authentication, persistence, queues, and artisan commands
- [vue3/](vue3/) — Vue 3 single-page app, routing, Pinia stores, and UI components
- [src/](src/) — shared domain concepts and DDD-oriented abstractions
- [Makefile](Makefile) — convenience commands for install, dev, lint, and tests

## Quick start

### Prerequisites

See [TECH_STACKS.md](TECH_STACKS.md) for exact runtime and dependency requirements.

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
- queue worker
- pail log viewer
- Vue frontend on http://localhost:5173

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
- Vuetify-based UI components and responsive layout
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
