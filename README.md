# Team Task Manager

A simple yet powerful task management application with role-based access control. Built with Laravel 12 backend and Vue 3 frontend.

> **Note on Laravel Version:** The project uses Laravel 12, which is compatible with the requirements. Laravel 12 builds on the stable Laravel 11 foundation with additional improvements and features. Both versions share the same core architecture and patterns.

## Project Structure

```
.
├── laravel12/          # Laravel 12 backend API
├── vue3/               # Vue 3 SPA frontend
├── src/                # Shared DDD architecture (Domain-driven design)
├── ARCHITECTURE.md     # Architecture decisions and design patterns
└── README.md           # This file
```

## Quick Start

### Prerequisites

- PHP 8.3+
- Node.js 18+ and npm/pnpm
- MySQL 8+ (or SQLite for development)
- Composer

### Setup (Backend + Frontend)

From the project root directory:

```bash
cd laravel12
composer run setup
```

This single command will:
1. Install Laravel dependencies
2. Create `.env` file
3. Generate application key
4. Run database migrations with seeds
5. Install Vue 3 frontend dependencies

The backend API will be available at `http://localhost:8000`
The frontend will be available at `http://localhost:5173`

**Test Accounts:**
- Admin: `admin@test.com` / `password`
- User: `user@test.com` / `password`

> **Production TODO:** Change the passwords to stronger ones

### Development

Start the development servers (from `laravel12/` directory):

```bash
composer run dev
```

This runs concurrently:
- Laravel backend server (`http://localhost:8000`)
- Queue listener
- Pail log viewer
- Vue 3 frontend dev server (`http://localhost:5173`)

## Features

### Backend (Laravel 11)

#### Data Model
- **Users**: Email authentication with role-based access (admin/user)
- **Tasks**: Full task management with status tracking and due dates

#### API Endpoints

**Authentication:**
- `POST /api/login` - Login and receive auth token
- `POST /api/register` - Register new user
- `POST /api/logout` - Logout
- `GET /api/me` - Get current user info
- `GET /api/users` - Get list of users (admin only)

**Tasks:**
- `GET /api/tasks?status={status}&assigned_to={id}` - List tasks with optional filters
- `POST /api/tasks` - Create new task
- `PUT /api/tasks/{id}` - Update task
- `DELETE /api/tasks/{id}` - Delete task

#### Authorization Rules
- **Admin**: Can create, view, update, and delete any task
- **User**: Can only manage their own tasks

### Frontend (Vue 3 + Vuetify 3)

#### Pages
- `/login` - Login page
- `/register` - Registration page
- `/tasks` - Task management dashboard

#### Features
- **Task List**: Paginated table with search and filter capabilities
- **Task Form**: Create and edit tasks with modal dialog
- **Status Badges**: Color-coded task status indicators
- **User Info**: Display current user and role in app bar
- **Responsive Design**: Mobile-friendly Vuetify components

## Key Decisions & Simplifications

### Architecture
- Used **DDD (Domain-Driven Design)** pattern for backend code organization
- Task domain isolated in `src/Domain/Task/` for easy maintenance
- Separation of concerns with repositories pattern

### Authentication
- Laravel Sanctum for API token authentication
- JWT tokens stored in localStorage (frontend)
- Token sent in Authorization header for all requests

### Database
- Enum fields for status and role (MySQL 8+ native support)
- Soft deletes could be added if needed
- Foreign key constraints for data integrity

### Frontend
- Pinia for state management (auth + tasks)
- Vue Router for navigation with auth guards
- Vuetify for consistent UI components

## Assumptions & Trade-offs

1. **Simple Auth**: No 2FA or social login (can be added via Laravel Fortify)
2. **Client-side Search**: Title search is done client-side for simplicity. Server-side can be added.
3. **No Real-time**: WebSockets not implemented (could use Laravel Reverb for live updates)
4. **Basic Validation**: Standard HTTP validation (can be enhanced with custom rules)
5. **SQLite Friendly**: Migrations work with both MySQL and SQLite for development

## Performance Considerations

### For 1M+ Tasks Endpoint

1. **Pagination**: Implemented with 20 items per page (adjustable)
2. **Database Indexing**: Add indexes on `assigned_to`, `status`, `created_at` columns
3. **Eager Loading**: Use `with()` to prevent N+1 queries
4. **Query Optimization**: 
   - Select only needed columns: `select('id', 'title', 'status', ...)`
   - Use `whereIn()` for multiple conditions
5. **Caching**: Implement Redis caching for:
   - User list (for dropdown)
   - Frequently filtered queries
6. **Async Processing**: Use queues for task creation/updates
7. **API Rate Limiting**: Add throttle middleware to prevent abuse
8. **Database Connection Pooling**: Use persistent connections
9. **Compression**: Enable gzip for API responses
10. **CDN**: Cache static assets (frontend build files)

### Migration Strategy
```sql
CREATE INDEX idx_assigned_to ON tasks(assigned_to);
CREATE INDEX idx_status ON tasks(status);
CREATE INDEX idx_created_at ON tasks(created_at DESC);
CREATE INDEX idx_user_id ON tasks(assigned_to, status);
```

## Security Measures

### Threats & Countermeasures

1. **XSS (Cross-Site Scripting)**
   - Vue automatically escapes template expressions
   - Use `v-text` instead of `v-html` when possible

2. **CSRF (Cross-Site Request Forgery)**
   - Sanctum automatically includes CSRF protection
   - Token validated on state-changing requests

3. **SQL Injection**
   - Eloquent ORM prevents SQL injection via parameterized queries
   - Never use raw SQL with user input

4. **Authentication Token Security**
   - Tokens sent via HTTPS only in production
   - Stored securely in httpOnly cookies (can be improved)
   - Short expiration time recommended

5. **Authorization**
   - Gate checks on API endpoints
   - User can only access their own tasks unless admin
   - Validated on backend

6. **Rate Limiting**
   - Can add throttle middleware for login attempts
   - API endpoints should have rate limits

7. **HTTPS**
   - Must be enabled in production
   - Strict-Transport-Security header recommended

## TypeScript Benefits in Vue 3 SPA

### Type Safety
1. **Compile-time Checking**: Catch errors before runtime
2. **Autocompletion**: IDE provides intelligent suggestions
3. **Self-documenting Code**: Types serve as inline documentation

### Example Component with Strong Typing

```ts
<script setup lang="ts">
import { ref, computed } from 'vue'

interface Task {
  id: number
  title: string
  status: 'todo' | 'in_progress' | 'done'
  due_date?: string
}

const tasks = ref<Task[]>([])
const filter = ref<'todo' | 'in_progress' | 'done' | 'all'>('all')

const filteredTasks = computed<Task[]>(() => {
  if (filter.value === 'all') return tasks.value
  return tasks.value.filter(t => t.status === filter.value)
})

const addTask = (task: Task): void => {
  tasks.value.push(task)
}

const updateTask = (id: number, updates: Partial<Task>): void => {
  const task = tasks.value.find(t => t.id === id)
  if (task) {
    Object.assign(task, updates)
  }
}
</script>
```

### Benefits
- **Prevents runtime errors**: Type mismatches caught at compile time
- **Refactoring confidence**: Changing types shows all affected locations
- **Better DX**: IDE autocomplete for component props and emits
- **Code clarity**: Intent is explicit through types

## Running Tests

From `laravel12/` directory:

```bash
# Run all tests with linting
composer run test

# Run tests only (skip linting)
composer run test:lint

# Run linting only
composer run lint
```

## Future Enhancements

1. **Real-time Updates**: WebSocket support via Laravel Reverb
2. **Advanced Filtering**: Server-side search with Elasticsearch
3. **File Attachments**: Upload files to tasks
4. **Comments**: Task comments for collaboration
5. **Notifications**: Email/webhook notifications for task changes
6. **Export**: Export tasks to CSV/PDF
7. **Dark Mode**: Theme toggle in frontend
8. **Mobile App**: React Native or Flutter mobile client

## Deployment

### Full Production Build

From project root (one-step build for both backend and frontend):

```bash
cd laravel12

# Build both backend and frontend
composer run build

# Clean config and optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Backend Deployment

```bash
cd laravel12

# Install production dependencies only
composer install --no-dev --optimize-autoloader

# Run pending migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Frontend Deployment

The `composer run build` command builds the Vue 3 frontend to the `vue3/dist/` directory. Serve this directory with a web server (nginx, Apache, etc.) and configure it to serve `index.html` for all non-file routes.

Nginx example:
```nginx
location / {
    try_files $uri $uri/ /index.html;
}
```

## Troubleshooting

### CORS Issues
Add backend URL to `.env`:
```
VITE_API_URL=http://localhost:8000
```

### Migrations Fail
```bash
php artisan migrate:reset
php artisan migrate --seed
```

### Frontend Can't Connect to API
- Ensure backend is running on `http://localhost:8000`
- Check browser console for CORS errors
- Verify proxy config in `vite.config.ts`

## Architecture Overview

See [ARCHITECTURE.md](./ARCHITECTURE.md) for detailed architecture decisions, DDD patterns, and design principles.

## License

MIT
