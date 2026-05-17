# Windows Local Setup

## Target development stack

- Laragon
- PHP 8.3+
- PostgreSQL
- pgAdmin
- Node.js / npm
- Redis for queue/cache in real local integration runs

## Current machine note

During initial scaffolding, the shell resolved `php` to XAMPP PHP 8.2 while Laragon already contained PHP 8.3.30. The repository therefore includes a temporary CLI runtime file:

```text
.runtime/php-8.3.ini
```

Use the Laragon PHP 8.3 executable for project commands until your system PATH is normalized.

## Recommended `.env`

```dotenv
APP_NAME="Holding ERP"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://holding-erp.test

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=holding_erp
DB_USERNAME=postgres
DB_PASSWORD=

CACHE_STORE=redis
QUEUE_CONNECTION=redis
BROADCAST_CONNECTION=reverb
```

## First run

```powershell
composer install
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

## PostgreSQL setup checklist

1. Install PostgreSQL and pgAdmin.
2. Create database `holding_erp`.
3. Ensure the `pdo_pgsql` and `pgsql` PHP extensions are enabled.
4. Update `.env`.
5. Run:

```powershell
php artisan migrate:fresh --seed
```

## Windows queue note

Laravel Horizon belongs in the target architecture, but the Windows PHP runtime does not provide the Unix extensions Horizon expects. Use normal Laravel workers locally when needed, and run Horizon in a Linux-compatible environment for staging or production.

