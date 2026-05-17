# Holding ERP Ecosystem

Enterprise modular-monolith ERP for a centralized multi-brand holding ecosystem:

- ICONMART
- VINZ Ice Cream
- SATE MERAH
- SHALIMAR Catering
- future brands

## Current foundation

- Laravel 13 core
- modular provider registry
- shared service/repository base layer
- operational + holding scoping primitives
- hierarchy, RBAC, audit, and notification migrations
- authentication, session, login log, and activity log foundation
- Holding dashboard with dynamic role-based sidebar
- Approval Inbox for pending enterprise decisions
- Notification Center with read/unread lifecycle
- Audit Log Viewer with subject activity timelines
- IT User Management with role and enterprise scope assignment
- Role & Permission Management UI with owner-role safety lock
- Purchasing vertical slice: supplier master, purchase order draft, approval inbox integration, receiving
- Inventory posting slice: stock movements, warehouse stocks, low-stock notification job
- PWA manifest + service worker shell
- architecture documentation under `docs/architecture`
- seeded local owner account for development only

## Local runtime note

This project targets Laravel 13 and PHP 8.3+. On this Windows machine, the active shell originally pointed to XAMPP PHP 8.2, while Laragon already provides PHP 8.3. A local runtime ini is stored in `.runtime/php-8.3.ini` for CLI work during development.

Laravel Horizon is part of the production architecture, but Windows PHP lacks the `pcntl` / `posix` extensions it requires. Use standard queue workers locally and run Horizon in a Linux-compatible runtime for staging/production.

## Architecture docs

- `docs/architecture/MASTER_ARCHITECTURE.md`
- `docs/architecture/DATABASE_AND_ERD.md`
- `docs/architecture/RBAC_AND_WORKFLOWS.md`
- `docs/architecture/IMPLEMENTATION_ROADMAP.md`
- `docs/development/WINDOWS_LOCAL_SETUP.md`

## Local login

For local development only:

- email: `owner@holding.test`
- password: `password123456`

## Verified status

Current automated checks pass:

- `php artisan migrate:fresh --seed --force`
- `php artisan test`
- `vendor/bin/pint --dirty`
- `npm run build`





