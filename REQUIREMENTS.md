## Goal

Build a small "Team Task Manager" app

### Tech stack

- Backend: Laravel 11, PHP 8.3, MySQL 8
- Frontend: Vue 3 SPA with Vuetify 3 and TypeScript
- API: JSON REST
- Auth: Simple login with role-based access (use laravel-permissions package):
  - admin — can manage all tasks
  - user — default, can manage only own tasks

Focus on clean, maintainable code, sensible arch and clear doc rather than feature completeness.

## Deliverables

1. A Github link containing:
  - The Laravel backend project (in laravel11/ subdir)
  - The Vue 3 frontend project (in a vue3/ subdir)
2. A short README that includes:
  - How to setup and run the backend and frontend (`composer run setup` and `dev`)
  - Any assumptions or simplifications made
  - How to login (seeding, password, test users instructions)

## Scope

Expectation to cost expectation is around 2–3 hours. It's OK if not everything is perfect or fully polished.

Priorities:
- Clear structure and naming
- Good use of framework features
- Reasonable handling of errors and edge cases

### Min Backend Scope

#### Data Model & Migrations

Create the following DB structure and Eloquent models:

##### Users (conceptual, use Laravel native)

Fields:
- id
- name
- email
- password
- role (user, admin)
- Timestamps (created_at, updated_at)

Relationships:
- A Task belongs to a User (assignee)

##### Tasks

- id
- title
- description
- status (todo, in_progress, done)
- assigned_to (nullable, FK → users.id)
- due_date (nullable)
- Timestamps

Relationships:
- A Task belongs to a User (assignee)

#### Requirements

- users and tasks migrations
- User and Task Eloquent models with correct relationships
- Proper $fillables / $guarded usage
- Sensible constraints (e.g. enum or validation for `status`, FK for `assigned_to`)

##### REST API Endpoints (Laravel resource)

- /api/tasks
- GET
  - Returns a task list
  - Supports optional filters via query params:
    - `status`
    - `assigned_to`
- POST
  - Creates a new task
- PUT
  - Updates title, description, status, due_date, assigned_to
- DELETE
  - Deletes a task (either soft or hard delete, note in the README)

Requirements:
- Use FormRequest for validation (e.g. StoreTaskRequest, UpdateTaskRequest)
- Return JSON with appropriate HTTP status codes
- Handle validation and not-found errors cleanly (optional use API Resources)

##### Authentication & Authorization

Implement a simple Laravel auth mechanism (e.g. Sanctum or session-based):
- A way to login and obtain session/token
- role-based access rules:
  - admin:
    - list, create, update, and delete any task
  - user:
    - list own tasks
    - create, update, delete own tasks
- use guards, middleware, policies or gates but keep it clear and easy to follow.

### Min Frontend Scope

#### Project Setup

- Create a Vue 3 SPA using TypeScript
- Use Vuetify 3 for layout and components
- Use a router with at least these routes:
  - /login
  - /tasks

#### Task List page (/tasks)

- Fetches tasks from the /api/tasks
- Displays them in a Vuetify table (v-table or v-data-table) with columns:
  - Title
  - Assignee
  - Status
  - Due date
  - Actions (e.g. edit/delete or at least a placeholder)
- Supports:
  - Pagination (client or server side)
  - Filter by status (dropdown)
  - Search by title (text)
- Nice-to-have:
  - Colored chips for status:
  - todo in grey
  - in_progress in blue
  - done in green
- Loading and error states using Vuetify components
- Task Form (Create/Update)
  - Implement a reusable Task Form component (e.g. FormTask.vue) that:
    - Fields:
      - Title
      - Description (optional, textarea)
      - Status (dropdown)
      - Assignee (dropdown, list of users from the API)
      - Due date (Vuetify date picker)
    - Behaviors:
      - Create: POST /api/tasks
      - Update: load task (from API or passed-in data) and calls PUT /api/tasks/{id}
    - It's up to you whether this form is a separate page or a dialog/modal in the task list
### Login page (/login)

- Email + password fields
  - A login button that calls POST /api/login (or similar)
- On success:
  - Store the auth token/session info (cookie, store or localStorage)
- Protect routes:
  - Accessible only to auth users for /tasks
- Show current user's name and role somewhere at the top or header

If you simplify the backend auth logic for time reason, just explain your assumption in the README

## Short Written Questions

Answer these in a Answer section of the README

### Performance & Security

1. Performance:
The GET /api/tasks endpoint may one day need to handle 1M+ tasks.<br>
Bulleted list 5–10 points describing how you would improve its performance<br>
(e.g. pagination strategy, indexing, eager loading, caching, query optimization, etc.)
2. Security:
In a few bullet points, describe how you would secure this Laravel API + Vue SPA setup. <br>
Mention typical threats and countermesures (e.g. XSS, token handling, HTTPS, etc.)

### TypeScript / Vue

1. What are the main benefits of using TypeScript in a Vue 3 SPA?
2. Show a small sample code of a Vue 3 component using `<script setup lang="ts">` with strongly typed props.

### Timebox & Expectations

- Suggested time within 2–3 hours for this round.
- It's acceptable if you leave out some non-critical aspects (e.g. polished styling, full validation everywhere) as long as:
  - The main flow works
  - The code is reasonably structured and readable
  - Your assumptions are documented
- If you  run out of time, briefly explain in the README what you would do next or how would improve the solution.

### How to submit (not for agents)

Send employer the Git URL to the README.