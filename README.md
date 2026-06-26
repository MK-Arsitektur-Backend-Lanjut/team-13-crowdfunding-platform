# Team 13 Crowdfunding Platform

Backend crowdfunding platform berbasis Laravel 13 untuk mengelola campaign, memproses donasi secara real-time, dan menangani autentikasi pengguna.

## Tech Stack

- Laravel 13
- PHP 8.4
- MySQL
- Redis
- Nginx + PHP-FPM
- Docker & Docker Compose
- Artillery (Stress Testing)

## Features

### Campaign Management
- CRUD Campaign
- Update Status Campaign
- Filter Campaign

### Donation Processing
- Create Donation
- Anonymous Donation
- Idempotency Protection
- Auto Update Donation Total
- Donation Statistics

### Authentication
- Register
- Login
- Refresh Token
- Logout
- Email Verification

## Quick Start

```bash
git clone <repository-url>
cd team-13-crowdfunding-platform

docker compose up -d --build
docker compose exec app composer install

cp .env.example .env
# Windows:
# Copy-Item .env.example .env

docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret
docker compose exec app php artisan migrate --seed
```

Akses aplikasi:

```
http://localhost:8000
```

## API

### Campaign
- GET `/api/campaigns`
- POST `/api/campaigns`
- PUT `/api/campaigns/{id}`
- DELETE `/api/campaigns/{id}`

### Donation
- POST `/api/donations`
- GET `/api/donations/stats`
- GET `/api/campaigns/{id}/donations/total`

### Authentication
- POST `/api/auth/register`
- POST `/api/auth/login`
- POST `/api/auth/refresh`
- POST `/api/auth/logout`

## Testing

Menjalankan feature test:

```bash
docker compose run --rm app php artisan test
```

## Stress Testing

Install dependency:

```bash
npm install
```

Jalankan benchmark:

```bash
npm run stress:stats
```

Stress test dilakukan menggunakan Artillery dengan skenario bertahap hingga **200 requests/second** namun juga disesuaikan dengan kebutuhan test masing-masing.

## Project Structure

```
app/
database/
docker/
routes/
stress-test/
tests/
```

## Notes

- Menggunakan Docker (Nginx + PHP-FPM + MySQL + Redis).
- Redis digunakan untuk caching.
- Endpoint statistik donasi telah dioptimalkan menggunakan caching dan query optimization.
- Backend telah diuji menggunakan Artillery untuk memastikan performa pada beban tinggi.
