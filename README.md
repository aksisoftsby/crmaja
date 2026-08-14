# Aksisoft CRM

**Aksisoft CRM** adalah aplikasi CRM berbasis Laravel untuk pengelolaan pelanggan, leads, dokumen penjualan, pembayaran, proyek, dukungan pelanggan, Knowledge Base, dan Client Portal. Aplikasi ini dibangun dengan Laravel 11, MySQL 8, Blade, Tailwind CSS, Laravel Breeze, serta Spatie Permission.

> Status rilis: aplikasi sedang divalidasi secara lokal. Perubahan aplikasi **belum** dipublikasikan ke GitHub atau dideploy ke lingkungan produksi.

## Modul yang tersedia

| Area | Cakupan utama |
|---|---|
| Akses staf | Login Breeze, role Super Admin/Staff/Sales/Support, permission granular |
| CRM sales | Customers, Contacts, Leads, Items, Proposals, Estimates, Invoices, Payments, PDF Invoice |
| Delivery | Projects, anggota project, milestones, Tasks polymorphic, checklist, komentar, dan time tracking |
| Support | Ticket, departemen, balasan staff, catatan internal, Knowledge Base |
| Client Portal | Login Contact terpisah serta akses terbatas ke invoice, proposal, estimate, project, task, ticket, dan Knowledge Base |
| Monitoring | Dashboard operasional dan laporan ringkas invoice, pelanggan, lead, project, serta ticket |

## Kebutuhan sistem

| Komponen | Versi minimum |
|---|---:|
| PHP | 8.3 |
| Composer | 2.x |
| Node.js | 20+ |
| MySQL | 8.0 |
| NPM | 10+ |

## Instalasi lokal

Buat database MySQL lokal, lalu berikan akses ke pengguna aplikasi.

```sql
CREATE DATABASE aksisoft_crm_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'aksisoft_crm_local'@'localhost' IDENTIFIED BY 'GANTI_DENGAN_PASSWORD_AMAN';
GRANT ALL PRIVILEGES ON aksisoft_crm_local.* TO 'aksisoft_crm_local'@'localhost';
FLUSH PRIVILEGES;
```

Salin konfigurasi lingkungan dan sesuaikan koneksi database.

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
```

Konfigurasi minimum di `.env` adalah sebagai berikut.

```dotenv
APP_NAME="Aksisoft CRM"
APP_ENV=local
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aksisoft_crm_local
DB_USERNAME=aksisoft_crm_local
DB_PASSWORD=GANTI_DENGAN_PASSWORD_AMAN
```

Jalankan skema awal, buat aset frontend, lalu mulai aplikasi.

```bash
php artisan migrate --seed
npm run dev
php artisan serve
```

Akses area staf pada `http://127.0.0.1:8000/login`.

| Akun | Email | Kata sandi |
|---|---|---|
| Super Admin lokal | `admin@aksisoft.test` | `ChangeMe123!` |

> Ganti kredensial default sebelum digunakan oleh pengguna lain. Jangan gunakan kredensial contoh di lingkungan produksi.

## Data demo opsional

Seeder dasar (`php artisan migrate --seed`) hanya membuat role, permission, Super Admin, sumber/status lead, dan departemen ticket. Jalankan seeder demo secara eksplisit untuk mengisi contoh pelanggan, contact portal, item, lead, project, task, invoice, ticket, dan artikel Knowledge Base.

```bash
php artisan db:seed --class=DemoDataSeeder
```

| Akun demo | Email | Kata sandi |
|---|---|---|
| Client Portal | `client@aksisoft.test` | `ClientPass123!` |

Client Portal tersedia di `/portal/login`. Staff juga dapat mengatur kata sandi portal saat menambahkan Contact pada halaman detail pelanggan.

## Pengujian dan build

Aplikasi memakai PHPUnit untuk pengujian fitur. Jalankan perintah berikut sebelum mengirim perubahan untuk ditinjau atau dipublikasikan.

```bash
php artisan test
npm run build
php artisan view:cache
```

## Deployment produksi

Target produksi adalah `https://crm.aksisoft.web.id`. Jalankan deployment hanya setelah kode lokal sudah tervalidasi, perubahan telah direview, dan repository sudah dipublikasikan.

### 1. Siapkan server

Install Nginx, PHP-FPM 8.3, ekstensi PHP Laravel, MySQL client, Node.js, NPM, dan Supervisor. Gunakan pengguna database khusus aplikasi, bukan root.

```bash
sudo apt update
sudo apt install -y nginx php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath nodejs npm supervisor
```

### 2. Buat database produksi

```sql
CREATE DATABASE aksisoft_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'aksisoft_crm'@'localhost' IDENTIFIED BY 'GUNAKAN_PASSWORD_UNIK_YANG_KUAT';
GRANT ALL PRIVILEGES ON aksisoft_crm.* TO 'aksisoft_crm'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Instal aplikasi

```bash
cd /var/www
git clone https://github.com/aksisoftsby/crmaja.git crm
cd crm
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
npm ci
npm run build
php artisan migrate --seed --force
sudo chown -R www-data:www-data /var/www/crm
sudo chmod -R 775 storage bootstrap/cache
```

Konfigurasi `.env` produksi setidaknya harus berisi `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://crm.aksisoft.web.id`, koneksi database produksi, serta `QUEUE_CONNECTION=database`. Gunakan secret yang unik dan tidak pernah disimpan dalam Git.

### 4. Nginx, queue, dan scheduler

Arahkan `root` virtual host Nginx ke `/var/www/crm/public`, lalu jalankan queue worker dengan Supervisor.

```ini
[program:aksisoft-crm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/crm/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=2
user=www-data
```

Tambahkan Laravel scheduler ke crontab pengguna server.

```cron
* * * * * cd /var/www/crm && php artisan schedule:run >> /dev/null 2>&1
```

### 5. DNS Cloudflare

Jika menggunakan Cloudflare Tunnel, buat Public Hostname dengan subdomain `crm`, domain `aksisoft.web.id`, dan service `http://localhost:80`. Jika menggunakan DNS proxy biasa, buat record `A` `crm` yang menunjuk ke IP server, aktifkan proxy Cloudflare, dan gunakan SSL/TLS `Full (Strict)` dengan sertifikat origin yang valid.

## Keamanan operasional

Jangan menyimpan `.env`, kata sandi database, token API, private key, atau kredensial VPS di repository. Aktifkan backup database terjadwal dan simpan salinan terenkripsi di lokasi terpisah dari server produksi. Gunakan `php artisan optimize` hanya setelah konfigurasi produksi sudah benar.

## Lisensi

Kode proyek ini menggunakan lisensi MIT, kecuali dependensi pihak ketiga yang mengikuti lisensi masing-masing.
