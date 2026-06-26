# Team 13 Crowdfunding Platform

Crowdfunding platform backend (Team 13) built with Laravel 13 for managing campaigns, processing donations in real-time, handling authentication, and optimizing backend performance using Redis caching and Docker.

---

## Overview

Project ini menggabungkan modul:

- Campaign Management
- Donation Processing
- User Authentication

Fokus utama backend:

- Pengelolaan campaign donasi
- Pemrosesan donasi secara realtime
- Anonymous donation
- Idempotency key untuk mencegah duplicate request
- Auto update total donasi per campaign
- Dashboard sederhana untuk Donation Processing
- Optimasi performa backend untuk traffic tinggi

---

## Backend Optimization

Backend telah dioptimalkan agar mampu menangani request dalam jumlah besar menggunakan beberapa teknik berikut:

- Redis Cache (application-level, TTL 60 detik untuk stats)
- Nginx FastCGI Cache (edge cache 30 detik untuk `/api/donations/stats`)
- Materialized Statistics Counter (`platform_stats`, `active_donor_markers`)
- Query Optimization & database indexing
- Repository Pattern
- Idempotency Protection
- PHP-FPM tuning + OPcache
- Dockerized Environment (Nginx + PHP-FPM + MySQL + Redis)
- Progressive Stress Testing menggunakan Artillery

---

## Technology Stack

- Laravel 13
- PHP 8.4
- MySQL 8
- Redis 7
- Nginx
- PHP-FPM
- Docker & Docker Compose
- Artillery (Stress Testing)

---

## Requirements

- Docker Desktop
- Docker Compose
- Node.js (untuk menjalankan Artillery)
- PHP 8.4 (opsional jika tidak menggunakan Docker)

---

## Quick Start

Clone repository:

```bash
git clone <URL_REPOSITORY>
cd team-13-crowdfunding-platform
```

Build dan jalankan container:

```bash
docker compose up -d --build
```

Install dependency:

```bash
docker compose exec app composer install
```

Copy environment:

Linux / Mac:

```bash
cp .env.example .env
```

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate key dan JWT secret:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret
```

Migration dan seeder:

```bash
docker compose exec app php artisan migrate --seed
```

Cache config & route (disarankan sebelum load test):

```bash
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
```

Akses aplikasi (via Nginx, **tanpa** `php artisan serve`):

```
http://localhost:8000
```

Verifikasi endpoint stats:

```bash
curl -s -D - http://localhost:8000/api/donations/stats
```

Request kedua ke atas seharusnya menampilkan header `X-Fastcgi-Cache: HIT`.

---

## Docker

Build ulang image:

```bash
docker compose down
docker compose build --no-cache app
docker compose up -d
```

Clear cache Laravel:

```bash
docker compose exec app php artisan optimize:clear
```

Rebuild cache setelah perubahan config/route:

```bash
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
```

Restart Nginx setelah perubahan config Nginx:

```bash
docker compose restart nginx
```

---

## Database Seeding

Seeder menyediakan data dummy untuk kebutuhan benchmarking.

Data yang dihasilkan:

- ±20.000 campaign
- ±20.000 donor aktif terverifikasi
- ±60.000 transaksi donasi sukses
- Rekap agregasi ke tabel `donation_totals`
- Counter platform ke tabel `platform_stats` (via migration)

Menjalankan seeder:

```bash
docker compose exec app php artisan db:seed
```

---

## Main Features

### Campaign Management

- List Campaign
- Create Campaign
- Update Campaign
- Delete Campaign
- Update Campaign Status
- Filter Campaign by Status

### Donation Processing

- Create Donation
- Anonymous Donation
- Idempotency Protection
- Auto Update Donation Total per Campaign
- Donation Statistics (`GET /api/donations/stats`)
- Campaign Total Endpoint

### Authentication

- Register
- Login
- Refresh Token
- Logout
- Email Verification (admin only)

### Performance

- Redis Cache
- Nginx FastCGI Cache
- Materialized Counter
- Optimized Query
- Repository Pattern
- Progressive Stress Testing

---

## API Endpoints

### Campaign

```
GET    /api/campaigns
GET    /api/campaigns/status/{status}
POST   /api/campaigns
GET    /api/campaigns/{campaign}
PUT    /api/campaigns/{campaign}
DELETE /api/campaigns/{campaign}
PATCH  /api/campaigns/{campaign}/status
```

### Donation

```
POST   /api/donations
GET    /api/donations/stats
GET    /api/campaigns/{campaignId}/donations/total

GET    /api/donations/history          (JWT required)
GET    /api/donations/history/{id}     (JWT required)
DELETE /api/donations/history/{id}     (JWT required)
```

### Authentication

```
POST /api/auth/register
POST /api/auth/login
POST /api/auth/refresh                 (JWT required)
POST /api/auth/logout                  (JWT required)
POST /api/auth/verify/{id}             (JWT + admin required)
```

### Donation Category

```
GET    /api/donation-categories
POST   /api/donation-categories
GET    /api/donation-categories/{category}
PUT    /api/donation-categories/{category}
DELETE /api/donation-categories/{category}
```

---

## Web UI

Dashboard tersedia pada:

```
/
```

Campaign Dashboard:

```
/campaigns
```

Donation Processing:

```
/donation-processing
```

View:

```
resources/views/dashboard.blade.php
resources/views/donation-processing.blade.php
```

---

## Testing

Menjalankan Feature Test:

```bash
docker compose run --rm --no-TTY app php artisan test --filter=DonationProcessingTest
```

---

## Stress Testing

Seluruh skenario berada pada folder:

```
stress-test/artillery
```

Install dependency Artillery (sekali):

```bash
npm install
```

Warmup cache stats (opsional):

```bash
bash stress-test/warmup-stats.sh
```

Menjalankan Progressive Stress Test:

```bash
npm run stress:stats
```

Quick Benchmark:

```bash
npm run stress:quick
```

Skenario lain:

```bash
npm run stress:read
npm run stress:write
npm run stress:mixed
```

### Tahapan pengujian (`stats-progressive.yml`)

| Phase   | Request Rate | Duration |
| ------- | -----------: | -------: |
| Warm Up |      5 req/s |      30s |
| Stage 1 |     20 req/s |      60s |
| Stage 2 |     50 req/s |      60s |
| Stage 3 |    100 req/s |      60s |
| Stage 4 |    200 req/s |      60s |

### Metrik yang perlu diperhatikan

| Metrik | Arti | Target |
| ------ | ---- | ------ |
| **p95** | 95% request selesai di bawah nilai ini | < 500 ms |
| **p99** | Tail latency | < 1000 ms |
| **Error rate** | `(failed + 5xx) / total` | < 1%, tanpa 502/504 |
| **Throughput** | Request/s yang diproses | Mendekati `arrivalRate` |
| **Median** | Waktu respons tipikal | < 50 ms (cache HIT) |
| **X-Fastcgi-Cache** | `HIT` = dari Nginx cache | Mayoritas HIT saat load tinggi |

Monitor Redis saat load test:

```bash
docker exec crowdfunding_redis redis-cli INFO stats
docker exec crowdfunding_redis redis-cli INFO memory
```

---

## Stress Test Result

Hasil pengujian endpoint:

```
GET /api/donations/stats
```

Menggunakan Artillery progressive test (`npm run stress:stats`).

| Metric | Result |
| ------ | ------ |
| Total Request | 22.350 |
| HTTP 200 | 20.105 (~90%) |
| Server Error (5xx) | **0** |
| Client Error (EADDRINUSE) | 2.245 (~10%) |
| Median Response | 6 ms |
| p95 | 89 ms |
| p99 | 633 ms |
| Max Response | 929 ms |

**Catatan:**

- Endpoint berhasil melayani hingga **200 requests/second** tanpa error **5xx** maupun timeout dari server.
- Error `EADDRINUSE` berasal dari **limit port ephemeral Windows** pada mesin yang menjalankan Artillery, bukan dari backend Laravel.
- Single request dengan Nginx cache HIT dapat mencapai **~5–9 ms** (`X-Fastcgi-Cache: HIT`).
- Untuk hasil load test yang lebih konsisten di Windows, jalankan Artillery dari WSL/Linux atau gunakan `http.pool` (sudah dikonfigurasi di `stats-progressive.yml`).

---

## Postman Quick Test

Base URL:

```
http://localhost:8000
```

### List Campaign

```
GET /api/campaigns
```

### Create Donation

```
POST /api/donations
```

Header:

```
Content-Type: application/json
Accept: application/json
X-Idempotency-Key: test-donation-001
```

Body:

```json
{
  "campaign_id": 1,
  "amount": 150000,
  "is_anonymous": false,
  "donor_name": "Postman User",
  "note": "Donasi dari Postman"
}
```

### Campaign Total

```
GET /api/campaigns/1/donations/total
```

### Donation Statistics

```
GET /api/donations/stats
```

### Login

```
POST /api/auth/login
```

Body:

```json
{
  "email": "personal@test.local",
  "password": "Test12345!"
}
```

> **Catatan:** Akun `personal@test.local` tersedia jika `TestAccountSeeder` dijalankan. Secara default, `migrate --seed` belum memanggil seeder ini — register dulu via `POST /api/auth/register` atau jalankan:
>
> ```bash
> docker compose exec app php artisan db:seed --class=TestAccountSeeder
> ```

Gunakan token:

```
Authorization: Bearer <token>
```

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Repositories/
└── Services/

database/
├── migrations/
└── seeders/

docker/
├── nginx/
└── php/

routes/
├── api.php
└── web.php

stress-test/
└── artillery/

tests/
└── Feature/
```

---

## Notes

- Project dijalankan menggunakan Docker (Nginx + PHP-FPM + MySQL + Redis).
- Akses API/UI melalui **Nginx port 8000**, bukan `php artisan serve`.
- Backend menggunakan Redis untuk cache aplikasi dan Nginx FastCGI cache untuk endpoint stats.
- Seluruh pengujian performa dilakukan menggunakan Artillery.
- Endpoint `/api/donations/stats` telah lolos stress test hingga **200 requests/second** dengan **0 error 5xx** dari server.
