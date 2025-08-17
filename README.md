# Laravel
Laravel Project Starter

# 📝 TODO backlog
- Add CRUD to Product, Category, Warehouse, Price models
- Theming UI (pallete, dark mode, bootstrap-like component gallery, etc)
- Add Role-Based Access Control (RBAC) using `spatie/laravel-permission`
- Package Approach (app/Modules)
- Implement Task Manager project (future)
- Implement Mini Blog API project (future)
- Add Dockerized setup (future)
- Add CI/CD pipeline (future)

# Organization
- Domain within defaults such as `app/Models/Inventory/Product.php`
- For multi-team projects, use [Package Development](https://laravel.com/docs/12.x/packages) approach

# Inventory System
A clean, modular inventory management app, demonstrating SOLID principles, clean code practices, and scalable design.  
This project is designed for quick setup, easy extension, and interview showcase value.

## 🚀 Features
- User authentication (Laravel Breeze)
- CRUD operations for Products and Categories
- Stock quantity management
- Search & filter for quick lookups
- REST API endpoints (JSON)
- Basic responsive frontend (Blade + optional Vue.js)
- Form request validation & Eloquent relationships
- Unit & feature tests

## 🛠 Tech Stack
- Laravel 10+
- PHP 8.2+
- MySQL
- Tailwind CSS
- Vue.js with Vite

## 📂 Project Structure
```
web/
 ├── Actions/       # Single, reusable business actions
 │ ├── Fortify/     # Related to authentication features
 │ └── Jetstream/   # Only delete-user action available
 ├── Console/       # Artisan console commands for CLI usage
 ├── Exceptions/    # Handling exception for web, API, and CLI contexts
 ├── Http/
 │ ├── Controllers/ # Handling requests and responses
 │ ├── Middleware/  # Before/After request Filters (auth checks, rate limiting, etc.)
 │ ├── Requests/    # FormRequest classes for input validation and authorization
 │ └── Resources/   # API Resource classes for transforming models to JSON responses
 ├── Models/        # Representing database tables
 ├── Providers/     # For binding services, events, and configuration at boot
 └── Services/      # Pmplementing domain or business logic
routes/
 ├── web.php
 └── api.php
database/
 ├── factories/
 ├── migrations/
 └── seeders/
```

## 📦 Installation
```bash
# Clone repository
git clone git@github.com:csktcoop/laravel.git
cd laravel/web

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database (first time only)
php artisan migrate --seed
# Database (before serving the app)
php artisan migrate

# Serve application
php artisan serve

# Serve Vite watcher
npm run dev
```

## 😵 Where to look at 1st
- The `artisan serve` output
- `run dev` output
- routes/web.php
- routes/api.php

## 🧪 Testing
```bash
php artisan test
```

## 📜 License
MIT License