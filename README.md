# HelpDesk Pro

![Tests](https://github.com/Stevens2606/helpdesk-pro/actions/workflows/tests.yml/badge.svg)

Sistem tiket dukungan (helpdesk) dengan tiga peran (pelanggan, staf, admin), SLA otomatis, notifikasi email via queue, dan REST API.

<!-- Tambahkan 2-3 screenshot di sini setelah aplikasi berjalan -->
<!-- ![Daftar tiket](docs/screenshots/tickets.png) -->

## Fitur

- Alur tiket lengkap: open → in progress → resolved → closed
- Role-based access control dengan Policy (pelanggan hanya melihat tiketnya sendiri)
- Komentar tiket, catatan internal (tidak terlihat pelanggan), dan lampiran file
- Riwayat aktivitas otomatis lewat Observer
- Notifikasi email via queue (database queue)
- Scheduler per jam yang menandai tiket melewati batas SLA
- REST API versi 1 dengan Laravel Sanctum, rate limiting, dan dokumentasi Scribe
- 39 tes otomatis (Pest) dengan CI di GitHub Actions

## Tech Stack

Laravel 12, PHP 8.2, MySQL, Livewire (Breeze), Tailwind CSS, Laravel Sanctum, Pest

## Cara Menjalankan

```bash
git clone https://github.com/Stevens2606/helpdesk-pro.git
cd helpdesk-pro
composer install
cp .env.example .env
php artisan key:generate
```

Buat database `helpdesk_pro`, atur kredensial di `.env`, lalu:

```bash
php artisan migrate --seed
php artisan storage:link
npm install && npm run build
php artisan serve
```

Jalankan worker queue di terminal lain:

```bash
php artisan queue:work
```

## Akun Demo (hanya untuk pengembangan)

| Peran | Email | Password |
|---|---|---|
| Admin | admin@example.com | password |
| Staff | staff@example.com | password |
| Customer | customer@example.com | password |

## Testing

```bash
php artisan test
```

## API

Dokumentasi interaktif tersedia di `/docs` setelah menjalankan `php artisan scribe:generate`.

Alur singkat: `POST /api/v1/login` untuk mendapat token, lalu kirim header `Authorization: Bearer <token>` ke `/api/v1/tickets`.

## Keputusan Teknis

- **Policy** untuk otorisasi, sehingga aturan akses terpusat dan mudah diuji
- **Observer** untuk mencatat riwayat aktivitas tanpa mengotori controller
- **Eager loading** di seluruh daftar untuk mencegah N+1 query
- **Catatan internal disaring di query**, bukan di tampilan, sehingga data tidak pernah sampai ke pelanggan
- **Scheduler idempotent**: kolom `sla_breached_at` memastikan tiap tiket hanya diproses sekali
- **Tes menemukan bug nyata** (default status tiket), yang kemudian diperbaiki

## Lisensi

MIT