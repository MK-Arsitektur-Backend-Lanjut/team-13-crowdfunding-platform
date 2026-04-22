# team-13-crowdfunding-platform
Crowdfunding platform backend (Team 13) for managing campaigns, processing donations in real-time, and handling user authentication using Laravel.

## Overview

Project ini menggabungkan modul Campaign Management dan Donation Processing dalam satu backend Laravel.

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

Seeder utama sudah menyiapkan data donasi dummy dalam jumlah besar.

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
- `GET /api/campaigns/{campaignId}/donations/total`

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

## Notes

- Repo ini dijalankan melalui Docker karena environment lokal tidak menyediakan PHP native.
- Untuk rebuild image setelah perubahan Dockerfile:

```bash
docker compose down
docker compose build --no-cache app
docker compose up -d
```
