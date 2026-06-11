# DB Optimization, Redis Exploration, and Stress Test Guide

Dokumen ini fokus ke kebutuhan tugas: optimasi teknis database, eksplorasi Redis, dan stress testing yang bisa langsung dijalankan di project ini.

## 1. Temuan Bottleneck Aktual di Kode

1. Endpoint `GET /api/donations/stats` menjalankan query agregasi berat berulang.
2. Endpoint list campaign (`GET /api/campaigns` dan `GET /api/campaigns/status/{status}`) query langsung ke DB pada setiap hit.
3. Endpoint `GET /api/donations/history` melakukan pagination + sorting/filter per user, perlu index komposit yang tepat.
4. Path write donasi (`POST /api/donations`) sudah aman transaksi + idempotency, tetapi read-after-write untuk total campaign tetap berpotensi membebani DB di trafik tinggi.

## 2. Optimasi yang Sudah Diimplementasikan

1. Caching read endpoint untuk campaign list dan campaign by status (TTL 20 detik).
2. Caching donation stats (TTL 15 detik).
3. Caching campaign donation total (TTL 10 detik) + invalidation setelah donasi baru masuk.
4. Penambahan index performa melalui migration baru:
   - donations: `(user_id, status)`, `(user_id, created_at)`, `(user_id, campaign_id, created_at)`
   - users: `(role, is_verified)`, `(role, is_verified, email)`
   - campaigns: `(status, created_at)`, `(created_at)`

## 3. Aktivasi Redis (Disarankan untuk Caching)

Secara default project masih menggunakan cache database.

Jika menjalankan lewat Docker Compose project ini, naikkan service Redis:

```bash
docker compose up -d redis
```

Ubah `.env`:

```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
REDIS_DB=0
REDIS_CACHE_DB=1
```

Lalu clear config/cache:

```bash
php artisan optimize:clear
php artisan config:cache
```

## 4. Checklist Eksplorasi Redis

Gunakan perintah berikut saat load test berjalan:

```bash
redis-cli INFO stats
redis-cli INFO memory
redis-cli INFO commandstats
redis-cli --scan --pattern "*campaign*"
redis-cli --scan --pattern "*donation*"
```

Metrik yang dicatat:

1. `keyspace_hits`, `keyspace_misses`, hit ratio.
2. memory peak (`used_memory_peak_human`).
3. command latency dominan (`cmdstat_get`, `cmdstat_set`, `cmdstat_eval` jika ada).
4. jumlah key cache aktif untuk endpoint campaign/stats/total.

## 5. Menjalankan Stress Test

Pastikan API sudah up di `http://localhost:8000`.

### A. Read-heavy load

```bash
npm run stress:read
```

### B. Write-heavy load (donation)

```bash
npm run stress:write
```

### C. Quick smoke benchmark

```bash
npm run stress:quick
```

### D. Mixed workload 70% read / 30% write

```bash
npm run stress:mixed
```

## 6. Override Parameter Load Test

Bisa pakai environment variable:

```bash
BASE_URL=http://localhost:8000 \
STRESS_CAMPAIGN_ID=1 \
STRESS_AMOUNT_MIN=10000 \
STRESS_AMOUNT_MAX=1000000 \
STRESS_USER_EMAIL=personal@test.local \
STRESS_USER_PASSWORD=Test12345! \
npm run stress:write
```

Untuk PowerShell:

```powershell
$env:BASE_URL='http://localhost:8000'
$env:STRESS_CAMPAIGN_ID='1'
$env:STRESS_USER_EMAIL='personal@test.local'
$env:STRESS_USER_PASSWORD='Test12345!'
npm run stress:read
```

## 7. Cara Baca Hasil

Bandingkan minimal 2 kondisi:

1. Sebelum Redis aktif (`CACHE_STORE=database`).
2. Sesudah Redis aktif (`CACHE_STORE=redis`).

Target hasil yang diharapkan:

1. p95/p99 latency read endpoint turun signifikan.
2. Throughput request read meningkat.
3. Query DB untuk endpoint stats/list berkurang.
4. Error rate tetap rendah saat write-heavy.

## 8. Langkah Lanjut (Opsional)

1. Tambah dashboard metrik (Grafana + Prometheus/Exporter) untuk MySQL dan Redis.
2. Tambah skenario mixed workload (70% read, 30% write).
3. Jalankan load test di data volume besar setelah `php artisan db:seed`.
