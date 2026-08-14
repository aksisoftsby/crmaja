# Aksisoft CRM — Status Implementasi dan Rilis

## Selesai dan tervalidasi

| Area | Status | Validasi |
|---|---|---|
| Laravel 12, MySQL 8, Breeze, Spatie Permission | Selesai | Upgrade terkontrol dari Laravel 11 ke 12, audit dependensi bersih, login dan database MySQL lokal tervalidasi |
| Customers & Contacts | Selesai | CRUD, policy akses, PIC utama, soft delete, 31 tes/75 asersi pada tahap validasi |
| Leads | Selesai | CRUD, master sumber/status, assignment, policy akses, 33 tes/79 asersi pada tahap validasi |
| Master Items | Selesai | Schema, policy, validasi, CRUD, rute, dan UI dasar tersedia; regresi lokal lulus |
| Proposals | Selesai | Dokumen, item, relasi customer/lead, total transaksi, policy, rute, dan UI dasar tersedia; 34 tes/82 asersi pada regresi penuh |
| Estimates | Selesai | Dokumen, item, relasi customer, total transaksi, policy, rute, dan UI dasar tersedia; 35 tes/85 asersi pada regresi penuh |
| Invoices & Payments | Selesai | Tagihan, item, pencatatan pembayaran, pembaruan saldo/status, unduhan PDF, policy, rute, dan UI dasar tersedia; pengujian PDF lulus secara lokal |
| Projects, Tasks & Time Tracking | Selesai | Project, anggota, milestone, Task polymorphic, multi-assignee, checklist, diskusi, timer/manual time log, policy, rute, dan UI dasar tersedia; regresi penuh 41 tes/112 asersi lulus |
| Tickets, Knowledge Base & Client Portal | Selesai | Ticket staff/client, balasan dan catatan internal, departemen, kategori/artikel KB, login Contact terpisah, serta isolasi data perusahaan tersedia; regresi penuh 45 tes/134 asersi lulus |
| Utilitas, Dashboard, Laporan, Dokumentasi & Data Demo | Selesai | Notes polymorphic pada pelanggan, dashboard operasional, laporan ringkas, README lokal/produksi, dan DemoDataSeeder tersedia; regresi penuh 46 tes/138 asersi lulus |
| Verifikasi integritas Laravel 12 | Selesai | Regresi penuh 49 tes/176 asersi lulus, audit dependensi bersih, cache konfigurasi/rute/view lulus, dan build Vite berhasil |
| GitHub | Selesai | Rilis Laravel 12 dipublikasikan ke `aksisoftsby/crmaja`, branch `main` |
| Produksi | Selesai | Aplikasi di-deploy ke VPS dengan Apache, MariaDB, Supervisor worker, scheduler cron, dan konfigurasi production non-debug |
| DNS & HTTPS | Selesai | Record A Cloudflare `crm.aksisoft.web.id` mengarah ke VPS dan sertifikat Let's Encrypt aktif dengan pengalihan HTTP ke HTTPS |
| Smoke test produksi | Selesai | Login Super Admin, Dashboard, dan 23 halaman navigasi staf tervalidasi melalui HTTPS setelah deployment |

## Status rilis saat ini

Aplikasi tersedia di **https://crm.aksisoft.web.id**. Deployment menggunakan Apache karena VPS telah menjalankan Apache untuk situs lain, sedangkan Nginx yang tersedia tidak aktif karena port 80 dan 443 telah digunakan Apache. Record Cloudflare saat ini memakai mode **DNS only** untuk menjaga jalur origin HTTPS langsung; record dapat diproksikan setelah mode SSL/TLS Cloudflare dipastikan `Full (Strict)`.

## Tindak lanjut operasional

1. Ambil kredensial Super Admin awal dari file root terlindungi di VPS, lalu ganti lagi setelah login pertama.
2. Konfigurasikan SMTP, gateway WhatsApp, penyimpanan berkas eksternal, dan integrasi pihak ketiga ketika kredensial operasional tersedia.
3. Aktifkan backup database terenkripsi dan pantau log, ruang disk, renewal sertifikat, queue worker, serta scheduler.
