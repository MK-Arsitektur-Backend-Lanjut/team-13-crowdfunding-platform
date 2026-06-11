# Laporan Integrasi Modul Team 13

## Judul
Integrasi Modul Campaign Management dan Donation Processing pada Platform Crowdfunding Team 13.

## Anggota dan Pembagian Modul
- Modul Campaign Management: branch `modul-campaign-management-elsa`
- Modul Donation Processing: branch `modul-donation-processing-rendizar`
- Branch integrasi: `integration-modules`

## Latar Belakang
Platform crowdfunding memerlukan backend yang mampu mengelola kampanye, menerima donasi dalam jumlah besar, dan menghindari double-processing saat request donasi datang bersamaan. Karena itu, dua modul utama digabungkan dalam satu branch integrasi.

## Tujuan
- Menggabungkan fitur campaign dan donation dalam satu backend Laravel.
- Menyediakan API campaign management dan donation processing.
- Menyediakan tampilan web sederhana untuk Donation Processing.
- Menambahkan optimasi data, repository pattern, dan seeder dummy skala besar.

## Fitur yang Diintegrasikan
### Campaign Management
- Daftar campaign
- Tambah campaign
- Ubah campaign
- Hapus campaign
- Ubah status campaign aktif/selesai
- Filter campaign berdasarkan status
- Manajemen kategori donasi

### Donation Processing
- Input donasi lewat API
- Donasi anonim
- Pencegahan double-processing dengan idempotency key
- Akumulasi total donasi per campaign
- Endpoint total donasi campaign
- Seeder 20.000 data donasi dummy

## Arsitektur Teknis
- Framework: Laravel 13
- Pola akses data: Repository Pattern
- Deployment lokal: Docker Compose
- Database: MySQL untuk development, SQLite in-memory untuk test
- Frontend sederhana: Blade + JavaScript fetch API

## File Penting
- [routes/api.php](routes/api.php)
- [routes/web.php](routes/web.php)
- [resources/views/donation-processing.blade.php](resources/views/donation-processing.blade.php)
- [app/Services/DonationService.php](app/Services/DonationService.php)
- [app/Repositories/EloquentDonationRepository.php](app/Repositories/EloquentDonationRepository.php)
- [app/Providers/AppServiceProvider.php](app/Providers/AppServiceProvider.php)
- [database/seeders/DonationSeeder.php](database/seeders/DonationSeeder.php)
- [tests/Feature/DonationProcessingTest.php](tests/Feature/DonationProcessingTest.php)

## Endpoint API
### Campaign Management
- `GET /api/campaigns`
- `GET /api/campaigns/status/{status}`
- `POST /api/campaigns`
- `GET /api/campaigns/{campaign}`
- `PUT /api/campaigns/{campaign}`
- `DELETE /api/campaigns/{campaign}`
- `PATCH /api/campaigns/{campaign}/status`

### Donation Processing
- `POST /api/donations`
- `GET /api/campaigns/{campaignId}/donations/total`

### Donation Category
- `GET /api/donation-categories`
- `POST /api/donation-categories`
- `GET /api/donation-categories/{category}`
- `PUT /api/donation-categories/{category}`
- `DELETE /api/donation-categories/{category}`

## Tampilan Web
Halaman utama menampilkan form donation processing dengan fitur:
- Campaign ID
- Nominal donasi
- Nama donatur
- User ID opsional
- Pesan donasi
- Donasi anonim
- Total campaign live
- Snapshot campaign management

## Hasil Pengujian
Feature test untuk Donation Processing berhasil dijalankan:
- 3 test passed
- 15 assertions

Perintah yang digunakan:
```bash
docker compose run --rm --no-TTY app php artisan test --filter=DonationProcessingTest
```

## Catatan Integrasi
- Branch integrasi berhasil dibuat dan digabungkan.
- Konflik merge pada file utama sudah diselesaikan.
- Docker image diperbarui agar mendukung `pdo_mysql`.
- View Donation Processing disesuaikan agar bisa menampilkan data dari modul campaign juga.

## Kesimpulan
Integrasi modul campaign dan donation berhasil disatukan ke dalam satu branch backend Laravel yang siap dipresentasikan dan diuji.
