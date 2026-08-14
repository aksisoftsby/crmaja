# PROMPT: Build "Aksisoft CRM" — Full CRM System (Setara PerfexCRM + Modul Tambahan)

## Cara Pakai Dokumen Ini
Copy seluruh isi file ini sebagai prompt awal ke AI coding agent (Claude Code, dll) atau jadikan spesifikasi untuk tim dev. Dokumen ini mendefinisikan: tech stack, seluruh modul & fitur, skema database + relasi, non-functional requirement, dan langkah deploy ke VPS yang sudah tersedia.

> **Catatan penting:** Sistem ini didesain **original** dengan fitur setara PerfexCRM (CodeCanyon) sebagai referensi fungsional — bukan menyalin source code proprietary mereka. Nama produk, struktur tabel, dan penamaan semuanya baru, supaya bebas isu lisensi/trademark dan bisa dikembangkan/dimodifikasi tanpa batasan.

---

## Daftar Isi
0. Environment & Target Deploy
1. Tech Stack
2. Arsitektur Pengguna & Hak Akses
3. Modul Inti
4. Modul Tambahan
5. Skema Database & Relasi
6. Ringkasan Relasi Utama
7. Non-Functional Requirements
8. Deployment ke VPS
9. Fase Pengerjaan
10. Instruksi Tambahan untuk AI Agent

---

## 0. Environment & Target Deploy
- VPS sudah tersedia, akses root
- MySQL sudah terpasang, akses root tersedia
- Cloudflare sudah terhubung (Tunnel/connector aktif ke VPS)
- Domain produksi: **crm.aksisoft.web.id**

---

## 1. Tech Stack

| Layer | Pilihan |
|---|---|
| Backend framework | Laravel 11.x (PHP 8.3+) |
| Database | MySQL 8, engine InnoDB, charset `utf8mb4` |
| Frontend | Blade + Livewire 3 + Alpine.js + Tailwind CSS |
| Auth | Laravel Breeze/Fortify |
| Roles & Permission | `spatie/laravel-permission` |
| Queue | Laravel Queue (driver `database`, upgrade ke Redis kalau traffic tinggi) |
| Scheduler | Laravel Task Scheduling (via cron VPS) |
| PDF generator | `barryvdh/laravel-dompdf` (invoice, estimate, proposal, kontrak) |
| Web server | Nginx + PHP-FPM |
| Process manager | Supervisor (queue worker) |
| Realtime (opsional) | Laravel Reverb / Pusher (notifikasi live, chat) |
| Storage | Local disk default, S3-compatible opsional |

> Kalau prefer stack lain (Node.js + NestJS, atau Python + Django) beri tahu — modul & skema database di bawah tetap berlaku, tinggal alih teknologi.

---

## 2. Arsitektur Pengguna & Hak Akses

Tiga jenis pengguna:

1. **Super Admin** — akses penuh ke semua modul & pengaturan sistem
2. **Staff** — role custom tak terbatas, permission granular per modul: `view_own`, `view_all`, `create`, `edit`, `delete`
3. **Client (Contact)** — login terpisah lewat **Client Portal**, hanya bisa akses data milik company/perusahaannya sendiri

Struktur project disarankan modular (misal pakai `nwidart/laravel-modules`) — tiap modul di bawah jadi folder terpisah (`app/Modules/Leads`, `app/Modules/Invoices`, dst) supaya gampang di-maintain dan gampang nambah modul baru ke depan.

---

## 3. Modul Inti (Setara PerfexCRM)

### 3.1 Dashboard
- Widget: total leads (per status), invoice unpaid & overdue, grafik income vs expense per bulan, task jatuh tempo, tiket terbuka, project aktif, calendar event mendatang, sales funnel/conversion rate
- Filter periode (bulan ini, kuartal, custom range)

### 3.2 Leads Management
- Data lead: nama, perusahaan, email, telepon, sumber (custom), status (custom, kanban stage), assigned staff, estimasi nilai, tags, custom fields
- Kanban board drag-and-drop antar status
- Web-to-lead form (bisa di-embed ke website luar, public endpoint)
- Import lead via CSV, deteksi duplikat
- Convert Lead → Customer (satu klik, migrasi semua data terkait)
- Log aktivitas & reminder follow-up per lead
- Kirim email langsung dari halaman lead (tercatat di activity log)

### 3.3 Customers & Contacts
- Customer = company/organisasi; Contact = PIC (bisa banyak per customer, satu ditandai primary)
- Customer group, custom fields per customer
- Tab detail lengkap: profile, contacts, proposals, estimates, invoices, payments, credit notes, contracts, projects, tasks, tickets, notes, files, statement of account
- Statement of account: rekap transaksi dalam PDF, per periode

### 3.4 Proposals
- Builder proposal: rich text editor + tabel item + harga
- Status: draft, sent, open, revised, declined, accepted
- Public link (token, tanpa login penuh) supaya client bisa buka & accept/decline
- E-signature ringan saat accept
- Convert proposal accepted → invoice otomatis

### 3.5 Estimates
- Sama struktur dengan invoice tapi belum final (quotation)
- Expiry date, status tracking
- Estimate request: client bisa request estimate dari portal
- Convert ke invoice sekali klik

### 3.6 Invoices
- Line item + tax per item/total + discount (fixed/percentage)
- Status: draft, unpaid, partial, paid, overdue, cancelled
- Recurring invoice: interval custom (harian/bulanan/tahunan), auto-generate via scheduler, jumlah siklus (unlimited/terbatas)
- Multi-currency
- Payment gateway: Midtrans/Xendit (lokal), Stripe/PayPal (opsional internasional)
- Reminder otomatis invoice jatuh tempo/overdue (email/WA terjadwal)
- Late fee otomatis (opsional, persentase/nominal)
- PDF invoice branded (logo, warna, template custom)

### 3.7 Payments
- Record manual payment (staff) atau otomatis via webhook payment gateway
- Master data payment mode (transfer, cash, gateway, dll)
- Partial payment: satu invoice bisa banyak payment record

### 3.8 Credit Notes
- Refund/adjustment terhadap invoice
- Bisa apply saldo credit note ke invoice lain

### 3.9 Expenses
- Kategori expense (custom), upload bukti/nota
- Recurring expense (sama pola dengan recurring invoice)
- Relasi opsional ke project → jadi billable expense ke client

### 3.10 Contracts
- Tipe kontrak custom, isi kontrak rich text/template
- Value, tanggal mulai-selesai, lampiran file
- E-signature dari client via portal
- Reminder otomatis sebelum kontrak expired

### 3.11 Projects
- Billing type: fixed price / hourly / non-billable
- Milestone + Kanban task board per project
- Time tracking per task (start-stop timer, manual entry)
- Diskusi/comment thread per project + file sharing
- Project members (staff, banyak-ke-banyak) + akses client (client bisa lihat progress di portal kalau diizinkan)
- Progress otomatis dari task selesai / manual override

### 3.12 Tasks
- Assign ke banyak staff sekaligus, checklist item, comment thread, lampiran
- Prioritas, status custom, tanggal mulai/jatuh tempo
- Relasi polymorphic: task bisa nempel ke project, lead, customer, ticket, atau berdiri sendiri
- Recurring task
- Reminder tugas

### 3.13 Tickets (Support/Helpdesk)
- Department (custom), priority, status
- Email-to-ticket (email masuk otomatis jadi ticket)
- Reply threaded + internal note (tidak terlihat client)
- Attachment per reply
- Assign otomatis / manual, SLA tracking opsional
- Client bisa submit & pantau ticket dari portal

### 3.14 Knowledge Base
- Kategori + subkategori artikel
- Search full-text, related articles, view counter
- Publish publik atau khusus client (login required)

### 3.15 Calendar
- Event + reminder, assign event ke staff lain
- Sync opsional ke Google Calendar
- Filter per staff/departemen

### 3.16 Surveys
- Builder survey (pertanyaan custom: text/pilihan ganda/rating)
- Kirim ke customer/contact via email/link
- Report hasil per survey

### 3.17 Goals
- Target custom (revenue, jumlah lead baru, dll) per periode
- Assign ke staff/tim
- Progress otomatis dari data invoice/leads aktual vs target

### 3.18 Reports
- Sales report, lead conversion report, income vs expense, staff performance, tax report, project profitability, aging report piutang (AR aging)

### 3.19 Staff Management, Roles & Permission
- Profile staff, departemen, jabatan
- Permission granular per modul
- Staff performance report (berbasis task selesai, ticket handled, dll)

### 3.20 Client Portal
- Login terpisah untuk contact
- Lihat & bayar invoice online, accept/decline proposal & estimate
- Submit & pantau ticket, lihat project & task yang di-share
- Download & tanda tangan kontrak
- Akses knowledge base

### 3.21 Utilities & Settings
- Custom Fields builder — bisa ditempel ke hampir semua entity (leads, customers, invoices, dll)
- Tags system (polymorphic, lintas modul)
- Notes (polymorphic, catatan bebas lintas modul)
- Activity log / audit trail sistem
- Notification center (in-app + email)
- Announcement ke staff/client
- Media library terpusat
- Import/export CSV
- Pengaturan: profil company, template email, tax rate, currency, template PDF, multi-bahasa (i18n)

---

## 4. Modul Tambahan (Di Luar Standar PerfexCRM)

- **HRM ringan** — attendance/absensi, leave request (cuti) + approval, payroll sederhana + payslip
- **Inventory/Stock** — produk fisik, gudang, stock movement in/out, purchase order & supplier
- **Recruitment/ATS ringan** — lowongan kerja, database pelamar, jadwal interview
- **WhatsApp/SMS Gateway** — notifikasi invoice/reminder otomatis via WA (provider lokal: Wablas/Fonnte) atau SMS
- **Live Chat widget** — chat langsung di client portal / website publik
- **Email Marketing/Campaign** — broadcast ke leads/customers, tracking open/click sederhana
- **Workflow Automation** — trigger → action (contoh: "invoice overdue 3 hari → kirim WA reminder", "lead baru masuk → assign otomatis ke staff tertentu")
- **Accounting ringan** — chart of accounts, jurnal umum, laporan laba rugi & neraca sederhana (CRM makin ke arah ERP ringan)
- **REST API penuh** — buat integrasi eksternal/mobile app ke depan (Laravel Sanctum + dokumentasi OpenAPI)
- **Two-Factor Authentication** — untuk staff & client portal
- **Multi-tenant/SaaS mode** (opsional) — kalau CRM ini mau dijual sebagai layanan ke banyak klien lain (shared database dengan `tenant_id`, atau database per-tenant)
- **Affiliate/referral tracking** — kalau ada program referral customer

---

## 5. Skema Database & Relasi

Format: `nama_tabel` — kolom penting (`kolom→tabel_relasi` = foreign key; nullable ditandai eksplisit)

### 5.1 Identity & Access
```
users            — id, name, email, password, phone, avatar, role_id→roles,
                    department_id→departments (nullable), is_active, last_login_at
roles            — id, name, is_admin (bool)
permissions      — id, module, action (view_own/view_all/create/edit/delete)
role_permissions — role_id→roles, permission_id→permissions (pivot)
departments      — id, name
```

### 5.2 Leads
```
leads         — id, name, company_name, email, phone, source_id→lead_sources,
                 status_id→lead_statuses, assigned_to→users, lead_value,
                 is_converted (bool), converted_client_id→clients (nullable),
                 converted_at, created_by→users
lead_sources  — id, name
lead_statuses — id, name, color, order, is_default
```

### 5.3 Customers & Contacts
```
clients          — id, company_name, vat_number, phone, website, address, city,
                    state, zip, country, currency_id→currencies,
                    customer_group_id→customer_groups, assigned_staff_id→users,
                    is_active, created_by→users
contacts         — id, client_id→clients, first_name, last_name, email, phone,
                    title, is_primary (bool), password (nullable, portal login),
                    is_active, last_login_at
customer_groups  — id, name
```

### 5.4 Master Data Transaksi
```
currencies    — id, name, symbol, exchange_rate, is_default
taxes         — id, name, rate
items         — id, title, description, rate, unit, tax_id→taxes (nullable)
payment_modes — id, name
```

### 5.5 Proposals & Estimates
```
proposals      — id, client_id→clients (nullable), lead_id→leads (nullable), subject,
                  content, total, status, date, open_till,
                  converted_invoice_id→invoices (nullable), created_by→users
proposal_items — id, proposal_id→proposals, item_id→items (nullable),
                  description, qty, rate, tax_id→taxes, amount

estimates      — id, client_id→clients, number, date, expiry_date, status,
                  subtotal, discount, total, notes,
                  converted_invoice_id→invoices (nullable), created_by→users
estimate_items — id, estimate_id→estimates, item_id→items (nullable),
                  description, qty, rate, tax_id→taxes, amount
```

### 5.6 Invoices, Payments, Credit Notes
```
invoices                — id, client_id→clients, number, date, due_date, status,
                           subtotal, discount, total, currency_id→currencies,
                           recurring_id→invoice_recurring_rules (nullable),
                           project_id→projects (nullable), notes, created_by→users
invoice_items            — id, invoice_id→invoices, item_id→items (nullable),
                           description, qty, rate, tax_id→taxes, amount
invoice_recurring_rules — id, interval_type (day/week/month/year), interval_value,
                           next_run_date, total_cycles (nullable), cycles_run
payments                 — id, invoice_id→invoices, amount, date,
                           payment_mode_id→payment_modes, transaction_id, note
credit_notes             — id, client_id→clients, invoice_id→invoices (nullable),
                           number, date, total, status
credit_note_items       — id, credit_note_id→credit_notes, item_id→items (nullable),
                           description, qty, rate, tax_id→taxes, amount
```

### 5.7 Expenses
```
expenses           — id, category_id→expense_categories, amount, date, vendor,
                      reference_no, tax_id→taxes (nullable),
                      project_id→projects (nullable), client_id→clients (nullable, billable),
                      receipt_path, is_recurring, recurring_rule (json), created_by→users
expense_categories — id, name
```

### 5.8 Contracts
```
contracts      — id, client_id→clients, contract_type_id→contract_types, subject,
                  content, value, start_date, end_date,
                  signed_by_contact_id→contacts (nullable), signature_path (nullable),
                  status, created_by→users
contract_types — id, name
```

### 5.9 Projects & Tasks
```
projects             — id, name, client_id→clients (nullable), description,
                        start_date, deadline, status, billing_type (fixed/hourly/not_billed),
                        budget, progress, created_by→users
project_members       — project_id→projects, user_id→users (pivot)
milestones            — id, project_id→projects, title, due_date, description, order
tasks                 — id, name, description, priority, status, start_date, due_date,
                        milestone_id→milestones (nullable), related_type, related_id
                        (polymorphic: project/lead/customer/ticket), is_recurring, created_by→users
task_assignees        — task_id→tasks, user_id→users (pivot)
task_checklist_items — id, task_id→tasks, description, is_finished, order
task_comments         — id, task_id→tasks, user_id→users, comment
task_time_logs        — id, task_id→tasks, user_id→users, start_time, end_time, note
```

### 5.10 Support
```
tickets            — id, subject, client_id→clients (nullable), contact_id→contacts (nullable),
                      department_id→ticket_departments, priority, status,
                      assigned_to→users (nullable), source (email/portal/manual)
ticket_departments — id, name, email_piping_address (nullable)
ticket_replies     — id, ticket_id→tickets, user_type (staff/contact), user_id,
                      message, is_internal_note (bool)

kb_categories — id, name, parent_id→kb_categories (nullable)
kb_articles   — id, category_id→kb_categories, title, content, is_published, views_count
```

### 5.11 Collaboration Lintas Modul (Polymorphic)
```
calendar_events     — id, title, description, start, end, all_day, created_by→users,
                       related_type, related_id (nullable)
notes               — id, content, related_type, related_id, created_by→users
attachments         — id, file_path, file_name, related_type, related_id, uploaded_by→users
tags                — id, name
taggables           — tag_id→tags, taggable_type, taggable_id (pivot polymorphic)
custom_fields       — id, module, label, field_type (text/textarea/select/date/checkbox),
                       options (json), is_required
custom_field_values — id, custom_field_id→custom_fields, related_type, related_id, value
reminders           — id, related_type, related_id, remind_at, description,
                       is_notified (bool), created_by→users
notifications       — id, type, notifiable_type, notifiable_id, data (json), read_at
activity_logs       — id, user_id→users (nullable), action, subject_type, subject_id,
                       description, ip_address
announcements       — id, title, content, show_to (staff/client/both), start_date, end_date
```

### 5.12 Surveys & Goals
```
surveys           — id, title, description, status
survey_questions  — id, survey_id→surveys, question, type
survey_recipients — id, survey_id→surveys, client_id→clients (nullable),
                     contact_id→contacts (nullable), sent_at, completed_at
survey_answers    — id, survey_question_id→survey_questions,
                     survey_recipient_id→survey_recipients, answer

goals          — id, title, target_type, target_value, period_start, period_end
goal_assignees — goal_id→goals, user_id→users (pivot)
```

### 5.13 Modul Tambahan (Skema Ringkas)
```
# HRM
attendances    — id, user_id→users, date, check_in, check_out
leave_types    — id, name, quota_per_year
leave_requests — id, user_id→users, leave_type_id→leave_types, start_date, end_date,
                  status, approved_by→users (nullable)
payroll_runs   — id, period_start, period_end, status
payslips       — id, payroll_run_id→payroll_runs, user_id→users, gross, deductions, net

# Inventory
warehouses           — id, name, address
products             — id, name, sku, item_id→items (nullable), unit
stock_movements       — id, product_id→products, warehouse_id→warehouses, qty,
                        type (in/out), reference_type, reference_id
suppliers             — id, name, phone, email
purchase_orders       — id, supplier_id→suppliers, warehouse_id→warehouses, status, total
purchase_order_items — id, purchase_order_id→purchase_orders, product_id→products, qty, price

# Recruitment
job_postings        — id, title, description, status
applicants           — id, job_posting_id→job_postings, name, email, resume_path, status
interview_schedules — id, applicant_id→applicants, scheduled_at, interviewer_id→users

# Automation
workflows        — id, name, trigger_event, is_active
workflow_actions — id, workflow_id→workflows, action_type, config (json)

# Messaging
message_logs — id, channel (whatsapp/sms/email), recipient, message, status,
                related_type, related_id

# Accounting
chart_of_accounts   — id, code, name, type (asset/liability/equity/income/expense)
journal_entries     — id, date, description, created_by→users
journal_entry_lines — id, journal_entry_id→journal_entries, account_id→chart_of_accounts,
                       debit, credit
```

---

## 6. Ringkasan Relasi Utama (Quick Reference)

- 1 `clients` → N `contacts`, N `invoices`, N `estimates`, N `proposals`, N `contracts`, N `projects`, N `tickets`
- 1 `leads` → 1 `clients` (setelah convert)
- 1 `invoices` → N `invoice_items`, N `payments`; opsional N `credit_notes`
- 1 `estimates` → N `estimate_items`; convert 1:1 jadi 1 `invoices`
- 1 `proposals` → N `proposal_items`; convert 1:1 jadi 1 `invoices`
- 1 `projects` → N `tasks`, N `milestones`, N↔N `users` (lewat `project_members`)
- 1 `tasks` → N↔N `users` (lewat `task_assignees`), N `task_checklist_items`, N `task_comments`, N `task_time_logs`
- 1 `tickets` → N `ticket_replies`
- 1 `kb_categories` → N `kb_articles`, self-referencing untuk subkategori
- Polymorphic (`related_type` + `related_id`) dipakai bersama oleh: `notes`, `attachments`, `custom_field_values`, `taggables`, `reminders`, `calendar_events`, `tasks` — supaya satu struktur bisa nempel ke banyak jenis entity (leads, clients, projects, invoices, tickets, dll) tanpa perlu tabel pivot terpisah tiap kombinasi

---

## 7. Non-Functional Requirements

- **Security** — bcrypt password hashing, CSRF protection, mass-assignment protection, prepared statement via Eloquent ORM, rate limiting di login, 2FA opsional untuk staff & client
- **Performance** — eager loading relasi (hindari N+1 query), index di semua foreign key & kolom filter umum (`status`, `date`, `client_id`), pagination di semua listing
- **Responsive** — mobile-friendly di semua halaman (staff area & client portal)
- **Backup** — scheduled `mysqldump` harian, simpan di storage terpisah dari VPS utama (S3/offsite)
- **Logging** — Laravel log + tabel `activity_logs` untuk audit trail
- **Queue & Scheduler** — queue worker via Supervisor (recurring invoice generation, kirim email/WA/notifikasi), Laravel Scheduler jalan tiap menit via cron VPS
- **Localization** — minimal Bahasa Indonesia + English, format currency & tanggal ikut locale
- **Soft delete** — aktifkan di tabel transaksional penting (`clients`, `invoices`, `estimates`, `proposals`, `contracts`, dll) supaya data tidak hilang permanen saat dihapus

---

## 8. Deployment ke VPS (crm.aksisoft.web.id)

### 8.1 Setup Database (pakai user khusus, bukan root langsung di app)
```bash
mysql -u root -p
CREATE DATABASE aksisoft_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'aksisoft_crm'@'localhost' IDENTIFIED BY 'GANTI_PASSWORD_KUAT';
GRANT ALL PRIVILEGES ON aksisoft_crm.* TO 'aksisoft_crm'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 8.2 Setup Server
```bash
apt update && apt install -y nginx php8.3-fpm php8.3-mysql php8.3-mbstring \
  php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath nodejs npm supervisor

# Composer (kalau versi apt sudah lama, pakai installer resmi):
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

### 8.3 Deploy Aplikasi
```bash
cd /var/www
git clone <repo-project> crm   # atau upload manual hasil build
cd crm
composer install --optimize-autoloader --no-dev
cp .env.example .env
php artisan key:generate
# edit .env: DB_DATABASE=aksisoft_crm, DB_USERNAME=aksisoft_crm, DB_PASSWORD=...,
#            APP_URL=https://crm.aksisoft.web.id, QUEUE_CONNECTION=database
php artisan migrate --seed
npm install && npm run build
chown -R www-data:www-data /var/www/crm
chmod -R 775 storage bootstrap/cache
```

### 8.4 Nginx Server Block
```nginx
server {
    listen 80;
    server_name crm.aksisoft.web.id;
    root /var/www/crm/public;

    index index.php;
    add_header X-Frame-Options "SAMEORIGIN";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 8.5 Konfigurasi Cloudflare (Tunnel/Connector sudah aktif)
1. Buka **Zero Trust Dashboard → Networks → Tunnels**, pilih tunnel yang jalan di VPS ini
2. Tambah **Public Hostname**: subdomain `crm`, domain `aksisoft.web.id`, service `HTTP → localhost:80`
3. Simpan — DNS otomatis dibuat, tidak perlu buka port 80/443 langsung ke internet

> Kalau ternyata setupnya DNS proxy biasa (bukan Tunnel): tambahkan **A record** `crm` → IP VPS, proxy status ON (orange cloud), SSL/TLS mode **Full** atau **Full (Strict)** dengan origin certificate terpasang di Nginx.

### 8.6 Queue Worker (Supervisor)
```ini
[program:aksisoft-crm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/crm/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=2
user=www-data
```

### 8.7 Scheduler (Cron)
```bash
* * * * * cd /var/www/crm && php artisan schedule:run >> /dev/null 2>&1
```

---

## 9. Fase Pengerjaan (Build Order untuk AI Agent)

1. **Fase 1** — Setup project Laravel, auth, roles & permission, migration tabel identity + clients + contacts
2. **Fase 2** — Modul Leads, Customers, Contacts (CRUD + kanban leads + convert to customer)
3. **Fase 3** — Modul sales: Items, Proposals, Estimates, Invoices, Payments, Credit Notes, Recurring Invoice, PDF generator
4. **Fase 4** — Modul Projects, Tasks, Milestones, Time Tracking
5. **Fase 5** — Modul Tickets, Knowledge Base, Client Portal (login contact + akses terbatas)
6. **Fase 6** — Calendar, Surveys, Goals, Announcements, Notifications, Custom Fields, Tags, Notes, Activity Log
7. **Fase 7** — Reports & Dashboard widget
8. **Fase 8** — Modul tambahan sesuai prioritas bisnis (HRM/Inventory/Automation/WA integration/Accounting)
9. **Fase 9** — Testing, seeder demo data, deployment ke VPS `crm.aksisoft.web.id`

---

## 10. Instruksi Tambahan untuk AI Agent

- Ikuti Laravel best practice: Form Request untuk validasi, Policy untuk otorisasi per role, Resource Controller
- Semua tabel transaksional pakai soft delete
- Sertakan seeder: role default (Super Admin, Staff, Sales, Support) + permission + demo data secukupnya untuk testing
- Sertakan `README.md` berisi cara install lokal & deploy production
- Struktur folder modular supaya gampang menambah modul baru tanpa bentrok modul lain
- Semua fitur kirim-email (invoice, reminder, ticket) pakai queue, jangan synchronous
