# team-13-crowdfunding-platform
Crowdfunding platform backend (Team 13) for managing campaigns, processing donations in real-time, and handling user authentication using Laravel.

## Overview

Project ini menggabungkan modul Campaign Management, Donation Processing, User & Auth dalam satu backend Laravel.

Fokus integrasi:
- Pengelolaan kampanye donasi
- Input donasi dan akumulasi total secara otomatis
- Dukungan donasi anonim
- Pencegahan double-processing dengan idempotency key
- Tampilan web sederhana untuk modul Donation Processing

## Requirements

- Laravel 13
- PHP 8.4
- Docker dan Docker Compose

## Quick Start (Baru Install Docker)

Panduan ini untuk teman tim yang baru pertama kali setup project di laptopnya.

1. Clone repo lalu masuk ke folder project

```bash
git clone <URL_REPOSITORY_KALIAN>
cd team-13-crowdfunding-platform
```

2. Build dan jalankan container

```bash
docker compose up -d --build
```

3. Install dependency Laravel di container

```bash
docker compose exec app composer install
```

4. Siapkan environment (jika file `.env` belum ada)

```bash
cp .env.example .env
```

5. Generate app key

```bash
docker compose exec app php artisan key:generate
```

6. Migrasi dan seed database

```bash
docker compose exec app php artisan migrate --seed
```

7. Jalankan server Laravel

```bash
docker compose exec app php artisan serve --host=0.0.0.0 --port=8000
```

8. Buka aplikasi

```text
http://localhost:8000
```

Jika perintah `cp` tidak tersedia di Windows PowerShell, gunakan:

```powershell
Copy-Item .env.example .env
```

## Run With Docker

Kalau ingin memastikan image terbaru dipakai di mesin lokal, jalankan ini dulu:

```bash
docker compose down
docker compose build --no-cache app
docker compose up -d
```

Setelah container aktif, lanjutkan migrasi dan buka aplikasi:

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan serve --host=0.0.0.0 --port=8000
```

Jika ingin reset cache Laravel saat development:

```bash
docker compose exec app php artisan optimize:clear
```

## Database Seeding

Seeder utama menyiapkan data dummy skala besar secara konsisten:

- 20.000 donatur aktif terverifikasi
- 60.000 data donasi sukses
- Rekap agregasi ke tabel `donation_totals`

```bash
docker compose exec app php artisan db:seed
```

## Main Features

### Campaign Management

- List campaign
- Create campaign
- Update campaign
- Delete campaign
- Update status campaign
- Filter campaign by status

### Donation Processing

- Create donation via API
- Pilih campaign aktif otomatis (sinkron dari Campaign Management)
- Anonymous donation
- Idempotency protection
- Auto update donation total per campaign
- Total campaign endpoint

## API Endpoints

### Campaign

- `GET /api/campaigns`
- `GET /api/campaigns/status/{status}`
- `POST /api/campaigns`
- `GET /api/campaigns/{campaign}`
- `PUT /api/campaigns/{campaign}`
- `DELETE /api/campaigns/{campaign}`
- `PATCH /api/campaigns/{campaign}/status`

### Donation

- `POST /api/donations`
- `GET /api/donations/stats`
- `GET /api/campaigns/{campaignId}/donations/total`
- `GET /api/donations/history` (JWT required)
- `GET /api/donations/history/{id}` (JWT required)
- `DELETE /api/donations/history/{id}` (JWT required)

### Authentication

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/refresh` (JWT required)
- `POST /api/auth/logout` (JWT required)
- `POST /api/auth/verify/{id}` (JWT + admin required)

### Donation Category

- `GET /api/donation-categories`
- `POST /api/donation-categories`
- `GET /api/donation-categories/{category}`
- `PUT /api/donation-categories/{category}`
- `DELETE /api/donation-categories/{category}`

## Web UI

Halaman utama sekarang memakai dashboard Campaign Management sebagai pusat navigasi, dan Donation Processing tetap tersedia sebagai modul terpisah.

- Root page: `/`
- Campaign dashboard alias: `/campaigns`
- Donation processing page: `/donation-processing`
- File view dashboard: [resources/views/dashboard.blade.php](resources/views/dashboard.blade.php)
- File view: [resources/views/donation-processing.blade.php](resources/views/donation-processing.blade.php)

## Testing

Feature test yang tersedia:

- `tests/Feature/DonationProcessingTest.php`

Jalankan test:

```bash
docker compose run --rm --no-TTY app php artisan test --filter=DonationProcessingTest
```

## Postman Quick Test

Base URL (local):

```text
http://localhost:8000
```

### 1) List campaign

- Method: `GET`
- URL: `/api/campaigns`
- Headers: `Accept: application/json`

### 2) Create donation (public, tanpa JWT)

- Method: `POST`
- URL: `/api/donations`
- Headers:
	- `Content-Type: application/json`
	- `Accept: application/json`
	- `X-Idempotency-Key: test-donation-001`
- Body (raw JSON):

```json
{
	"campaign_id": 1,
	"amount": 150000,
	"is_anonymous": false,
	"donor_name": "Postman User",
	"note": "Donasi dari Postman"
}
```

### 3) Cek total donasi per campaign

- Method: `GET`
- URL: `/api/campaigns/1/donations/total`
- Headers: `Accept: application/json`

### 4) Cek statistik donor aktif

- Method: `GET`
- URL: `/api/donations/stats`
- Headers: `Accept: application/json`

### 5) Login untuk endpoint history (JWT)

- Method: `POST`
- URL: `/api/auth/login`
- Headers:
	- `Content-Type: application/json`
	- `Accept: application/json`
- Body (raw JSON, sesuaikan akun):

```json
{
	"email": "personal@test.local",
	"password": "Test12345!"
}
```

Gunakan token hasil login di header berikut untuk endpoint protected:

```text
Authorization: Bearer <token>
```

### 6) Ambil history donasi user (JWT)

- Method: `GET`
- URL: `/api/donations/history?per_page=10&sort_by=created_at&sort_dir=desc`
- Headers:
	- `Accept: application/json`
	- `Authorization: Bearer <token>`

## Notes

- Repo ini dijalankan melalui Docker karena environment lokal tidak menyediakan PHP native.
- Untuk rebuild image setelah perubahan Dockerfile:

```bash
docker compose down
docker compose build --no-cache app
docker compose up -d
```
