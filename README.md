# Task Management

Laravel 11 + Inertia.js + Vue 3 (Breeze) — technical test project. Role & user management,
project/task tracking dengan file attachment, audit trail manual, dan import/export Excel via queue.

Rancangan lengkap ada di [`rancangan-task-management (1).md`](./rancangan-task-management%20%281%29.md).

## Tech Stack

- Backend: Laravel 11
- Frontend: Inertia.js + Vue 3 + Tailwind (Breeze starter kit)
- DB: SQLite (dev)
- Excel: `maatwebsite/excel`
- Queue: database driver

## Setup

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed

npm run build
php artisan storage:link

php artisan serve
```

Di terminal terpisah, jalankan queue worker (wajib untuk fitur export/import task):

```bash
php artisan queue:work
```

Buka `http://127.0.0.1:8000`.

**Login default** (dari seeder): `admin@example.com` / `password`

## Development

```bash
npm run dev        # Vite dev server (hot reload)
php artisan serve  # Laravel dev server
php artisan queue:work  # queue worker
```

## Fitur

- CRUD Role & User Account (User Account khusus role Administrator)
- CRUD Project (dengan kategori) & Task (nested per project), dengan attachment upload (validasi 100–500 KB)
- Audit trail manual (tanpa package pihak ketiga) — tab "History & Audit Trail" di halaman edit Project/Task
- Export task ke Excel dengan kolom pilihan sendiri, import task dari Excel/CSV dengan mapping kolom dinamis — keduanya diproses via queue dengan halaman status/polling
