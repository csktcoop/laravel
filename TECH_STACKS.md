# Tech stacks

This document is the authoritative stack reference for the current repository state.

## Monorepo structure

- Root workspace: /root/php/laravel
- Backend app: laravel12
- Frontend app: vue3
- Shared domain layer: src/
- Root tooling: Makefile, package.json, and pnpm workspace configuration

## Backend stack

- PHP 8.3
- Laravel 12
- Laravel Fortify
- Laravel Sanctum
- Livewire
- Livewire Flux
- Laravel Boost
- Laravel Pail
- Laravel Pint
- PHPUnit 11.5.3
- Mockery
- Larastan 3.10
- Spatie Laravel Ignition

## Frontend stack

- Vue 3
- Vue Router
- Pinia
- Vuetify
- Axios
- TypeScript
- Vite 5
- Vitest
- Vue Test Utils
- Material Design Icons JS

## Tooling and workflow

- pnpm for JavaScript dependency management across the monorepo
- Composer for PHP package management inside laravel12
- Husky 9.1.7 for Git hooks
- Biome 2.5.3 for frontend linting and formatting
- Root package.json orchestrates combined quality checks for frontend and backend
- Makefile provides the main local workflow: install, dev, lint, and test commands

## Documentation and AI workflow

The repository now includes documentation guidance in [DOCUMENTATION_PRACTICES.md](DOCUMENTATION_PRACTICES.md). When a dependency, workflow, or runtime requirement changes, update this file and the relevant feature documentation in the same change set.
