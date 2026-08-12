# Vue 3 frontend

This folder contains the Vue 3 client for the task manager app. It is a small Vite + Vue + Pinia + Vue Router project that talks to the Laravel backend through Axios. The UI is built with Element Plus and uses @element-plus/icons-vue for icons.

## Project entry points

- [index.html](index.html) — the HTML shell loaded by Vite.
- [main.ts](src/main.ts) — creates the Vue app, installs the router, and mounts the root component.
- [App.vue](src/App.vue) — the root layout that hosts the router view.
- [styles.css](src/styles.css) — global styling helpers and resets.

## Main folders

- [pages](src/pages) — route-level pages such as Login, Register, and Tasks.
- [router](src/router) — route definitions and auth redirect logic.
- [domains](src/domains) — feature-based modules for tasks and users.
- [shared](src/shared) — shared infrastructure such as the API client, utilities, and types.
- [components](src/components) — reusable UI pieces.

## What the important files do

- [pages/Login.vue](src/pages/Login.vue) — login form and submit flow.
- [pages/Register.vue](src/pages/Register.vue) — registration form and submit flow.
- [pages/Tasks.vue](src/pages/Tasks.vue) — main task list UI, create/update/delete flow, and filtering.
- [domains/task/store.ts](src/domains/task/store.ts) — Pinia store for loading, filtering, and mutating tasks.
- [domains/user/store.ts](src/domains/user/store.ts) — Pinia store for auth state, token persistence, and current-user fetches.
- [shared/api/client.ts](src/shared/api/client.ts) — Axios instance, request helpers, auth header handling, and shared error extraction.
- [shared/api/index.ts](src/shared/api/index.ts) — consolidates shared API helpers for easy imports.
- [shared/util/auth-storage.ts](src/shared/util/auth-storage.ts) — small wrapper around browser localStorage for the auth token.
- [shims-vue.d.ts](src/shims-vue.d.ts) — a TypeScript shim that tells the compiler how to treat .vue files.

## Why shims-vue.d.ts exists

TypeScript does not understand Vue single-file components by default. The file [src/shims-vue.d.ts](src/shims-vue.d.ts) declares a module for any .vue import so TypeScript can type-check Vue files without errors.

## How to run the frontend

From this folder:

- Install dependencies: pnpm install
- Start the dev server: pnpm dev
- Build for production: pnpm build

## How to fix Biome issues

Biome is the formatter/linter used by this project. Ref: https://biomejs.dev/reference/configuration/

### Fix issues automatically

Run:

pnpm exec biome check --write .

This will format files and apply safe fixes in place.

### Check only

Run:

pnpm exec biome check .

### Common cases

- For import/order and formatting problems, Biome can usually rewrite them automatically.
- For accessibility warnings like unused button roles, update the component markup to use the semantic element directly.
- For style issues involving Vue scoped selectors, keep the rule-specific ignore comment only when the syntax is required.

## Notes

- The app uses the Laravel API at /api.
- Authentication state is stored in localStorage through the auth storage helper.
- Pinia stores keep feature logic separate from the UI pages, which makes the pages easier to read.