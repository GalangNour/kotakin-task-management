# Rancangan — Task Management (Laravel Coding Test)

## Progress (update: 26 Juli 2026)

- [x] Project setup — Laravel 11 + Breeze (Inertia + Vue 3 + Tailwind), SQLite (zero-config), `QUEUE_CONNECTION=database`
- [x] Migration — roles, users (+role_id/is_active/last_login_at/preferences), project_categories, projects, tasks, task_attachments, audits — semua UUID PK + soft delete (kecuali project_categories)
- [x] Model + relasi (`Role`, `User`, `ProjectCategory`, `Project`, `Task`, `TaskAttachment`, `Audit`)
- [x] Audit manual — trait `HasAudit` (model events, snapshot old/new values), terpasang di Project/Task/TaskAttachment
- [x] Seeder — role Administrator+Member, admin default (`admin@example.com` / `password`)
- [x] Middleware `role:Administrator` custom (bukan Spatie)
- [x] CRUD controller — Role, User, ProjectCategory, Project, Task (nested di project), TaskAttachment (upload+verify+destroy)
- [x] Vue pages — semua Index/Create/Edit tiap entity, tab "Detail" + "History & Audit Trail" di Project/Task, tab Attachments (upload+verify+hapus) di Task
- [x] Tested end-to-end via Playwright (login → CRUD → upload file → audit trail) — nol console/HTTP error
- [x] Import/Export Excel + Queue (poin 4) — target entity **Task** (fit paling natural: bulk import backlog, export laporan). `TasksExport` (dynamic column checklist via `WithMapping`), `ExportTasksJob`/`ImportTasksJob` (queue), mapping kolom dinamis saat import (FK `project`/`assignee` di-resolve dari konteks project + email, bukan UUID mentah), tabel `task_exports`/`task_imports` buat tracking status + polling. Tested end-to-end lewat browser beneran (upload CSV 3 baris → semua ke-import benar, termasuk resolve assignee by email).

- [x] Landing page custom (hero + fitur, ganti boilerplate Breeze/Laracasts links)

**Belum:**
- [ ] Deploy/packaging buat presentasi

**Catatan implementasi (beda dari asumsi awal rancangan):**
- Dev server jalan di port **8001**, bukan 8000 (8000 kepakai project lain di mesin yang sama)
- DB dev pakai **SQLite** (bukan MySQL) — cukup buat demo, zero-config
- Route `show` di-exclude dari semua resource (gak ada halaman detail terpisah, langsung Edit)
- Import/Export scoped per-project (`/projects/{project}/tasks/export|import`), bukan global — konsisten sama struktur nested task yang udah ada
- **Perlu `php artisan queue:work` jalan** biar job export/import diproses (queue driver `database`, gak auto-jalan tanpa worker)

---

## 0. Update Ketentuan Penilaian (dari panitia)
- Backend **wajib Laravel**
- Skor frontend: **Vue.js > React.js > Blade**
- Deadline pengerjaan: **Rabu, 29 Juli 2026 sebelum 09.30**, presentasi jam yang sama via Zoom
- Belum selesai semua soal tetap boleh presentasi (penilaian tidak hanya dari kelengkapan)

→ Karena waktu efektif cuma ±3 hari dan Vue bernilai tertinggi, stack di bawah disesuaikan.

## 1. Tech Stack yang Disarankan
- **Backend**: Laravel 10/11
- **Frontend**: **Laravel Breeze starter kit — Inertia.js + Vue 3** (bukan Blade, bukan API+SPA terpisah)
  - Alasan: Inertia biarkan Laravel tetap pegang routing/auth/middleware (hemat waktu setup), tapi UI tetap full Vue 3 component → tetap dapat nilai kategori Vue tertinggi
  - Styling: Tailwind (bawaan Breeze)
- Role & Permission: buat tabel sendiri (custom), tidak wajib pakai Spatie — biar sesuai poin 1c (CRUD role management)
- Audit: **manual (tanpa library)** — model events / Observer + tabel `audits` custom
- Excel: `maatwebsite/excel`
- Queue: database driver (`QUEUE_CONNECTION=database`) cukup untuk demo
- Pengganti komponen (versi Vue, bukan jQuery plugin lama):
  - Select2 → `vue-select` atau Headless UI Combobox
  - DataTables → tabel custom dengan server-side pagination/search/sort via Inertia (`router.get` dengan query params), atau pakai `vue3-easy-data-table`

---

## 2. Struktur Database (ERD)

### a. Auth & Role
**roles**
| Field | Type |
|---|---|
| id | UUID (PK) |
| name | string (Administrator, Member, dst) |
| is_active | boolean |
| permissions | json *(daftar permission per role, fleksibel)* |
| timestamps, soft deletes |

**users**
| Field | Type |
|---|---|
| id | UUID (PK) |
| role_id | UUID (FK → roles) |
| name, email, password | string |
| is_active | boolean |
| last_login_at | datetime |
| preferences | json *(opsional, misal tema UI)* |
| timestamps, soft deletes |

> Relasi: `roles` 1—N `users`

### b. Domain: Task Management
**project_categories** *(lookup table, sumber data select2)*
| Field | Type |
|---|---|
| id | UUID (PK) |
| name | string |
| timestamps |

**projects**
| Field | Type |
|---|---|
| id | UUID (PK) |
| category_id | UUID (FK → project_categories) |
| created_by | UUID (FK → users) |
| name, description | string/text |
| start_date | datetime |
| end_date | datetime |
| is_active | boolean |
| metadata | json *(budget, client, tags, custom field bebas)* |
| timestamps, soft deletes |

**tasks**
| Field | Type |
|---|---|
| id | UUID (PK) |
| project_id | UUID (FK → projects) |
| assigned_to | UUID (FK → users, sumber select2) |
| title, description | string/text |
| status | enum (todo/in_progress/done) |
| priority | enum (low/medium/high) |
| due_date | datetime |
| is_completed | boolean |
| custom_fields | json |
| timestamps, soft deletes |

**task_attachments**
| Field | Type |
|---|---|
| id | UUID (PK) |
| task_id | UUID (FK → tasks) |
| uploaded_by | UUID (FK → users) |
| file_name, file_path | string |
| file_size | integer (KB, divalidasi 100–500) |
| uploaded_at | datetime |
| is_verified | boolean |
| meta | json *(mime type, checksum, dll)* |
| timestamps, soft deletes |

**Relasi keseluruhan:**
```
roles 1—N users
project_categories 1—N projects
users 1—N projects (created_by)
projects 1—N tasks
users 1—N tasks (assigned_to)
tasks 1—N task_attachments
```

Ini sudah lebih dari 3 table dengan relationship berlapis (project → task → attachment), plus semua field wajib (UUID, datetime, boolean, json) tersebar merata di tiap tabel.

---

## 3. Role & Akses (poin 1)
- **Landing page** — route publik, tanpa middleware auth
- **Dashboard** — middleware `auth`, semua role boleh masuk
- **CRUD Role Management** — middleware `auth`, semua role boleh masuk (sesuai soal "role apapun") — bisa dipertimbangkan untuk dibatasi ke Administrator saja secara best-practice, tapi soal bilang bebas
- **CRUD User Account** — middleware `auth` + middleware tambahan `role:Administrator`

---

## 4. Audit & Auditable (poin 3) — Manual, tanpa library
- Buat tabel `audits` sendiri (migration manual):
  | Field | Type |
  |---|---|
  | id | UUID (PK) |
  | auditable_type | string *(polymorphic, misal `App\Models\Task`)* |
  | auditable_id | UUID *(polymorphic)* |
  | event | string (created/updated/deleted/restored) |
  | old_values | json |
  | new_values | json |
  | user_id | UUID (FK → users, nullable) |
  | created_at | datetime |
- Buat trait `HasAudit`, hook ke `static::created`/`static::updating`/`static::deleted`/`static::restored` di method `boot()` trait
- Ambil `$model->getOriginal()` vs `$model->getChanges()` buat isi `old_values`/`new_values`, `auth()->id()` buat `user_id`
- Exclude field sensitif (password, token) dari log manual di trait
- Pasang trait `HasAudit` di model `Project`, `Task`, `TaskAttachment`
- Tampilkan di tiap halaman detail form sebagai tab/section "History & Audit Trail" (mirip contoh gambar kamu), query via relasi polymorphic `morphMany(Audit::class, 'auditable')`
- **Konsep Auditable (data historis tidak berubah walau master diupdate):**
  Contoh kasus: kalau `Task` sudah selesai dan project-nya kemudian diubah nama/kategori, riwayat task itu tidak boleh ikut berubah tampilannya. Solusinya: saat mencatat log/riwayat transaksi, simpan **snapshot** data terkait (misalnya nama project saat itu) langsung di kolom `json` milik record transaksi, bukan hanya foreign key. Jadi walau `projects.name` diupdate, snapshot lama tetap utuh.

---

## 5. Import/Export Excel + Queue (poin 4)
- Buat `ExportJob`/`ImportJob` (implements `ShouldQueue`)
- Endpoint export: generate file lewat `Maatwebsite\Excel`, jalankan di queue, kasih notifikasi/polling status selesai
- Endpoint import: upload file → dispatch job → job validasi & insert per baris
- **Dynamic field**: sediakan UI checklist kolom mana saja yang mau di-export, dan mapping kolom saat import (bukan hardcode kolom tetap)

---

Sisa kerjaan: Import/Export Excel + Queue (lihat checklist Progress di atas).
