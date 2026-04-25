# 🚀 Otomasi Postman - Panduan

Selamat datang di suite otomasi Postman untuk Platform Crowdfunding Team 13!

---

## 📖 Dari Mana Mulai?

### 🔰 **Baru Pertama Kali Pakai Postman?**
Mulai dari sini: **[QUICKSTART.md](QUICKSTART.md)** (baca 2 menit)
- Langkah instalasi
- Cara menjalankan tests (4 metode)
- Troubleshooting dasar

### 📚 **Mau Detail Lengkap?**
Baca ini: **[POSTMAN-RUNNER-GUIDE.md](POSTMAN-RUNNER-GUIDE.md)** (baca 15 menit)
- Dokumentasi fitur lengkap
- Konfigurasi advanced
- Integrasi CI/CD
- Troubleshooting detail

### 📋 **Butuh Overview?**
Lihat ini: **[IMPLEMENTATION-SUMMARY.md](IMPLEMENTATION-SUMMARY.md)** (baca 5 menit)
- Yang sudah dibuat
- Alur eksekusi test
- Tabel referensi cepat

---

## 🏃 **Quick Start (TL;DR)**

```bash
# 1. Install Newman (sekali saja)
npm install -g newman

# 2. Pastikan Laravel berjalan
docker compose up
# atau
php artisan serve

# 3. Jalankan tests
node postman/runner.js
```

Selesai! ✅

---

## 📦 **File yang Disertakan**

### **Runner** (Pilih satu)
| File | Cocok Untuk | Perintah |
|------|----------|---------|
| `runner.js` | 🌟 Recommended | `node postman/runner.js` |
| `runner.bat` | Windows | `postman\runner.bat` |
| `runner.ps1` | PowerShell | `.\runner.ps1` |

### **Dokumentasi**
| File | Tujuan |
|------|---------|
| `QUICKSTART.md` | ⚡ Referensi cepat |
| `POSTMAN-RUNNER-GUIDE.md` | 📖 Panduan lengkap |
| `IMPLEMENTATION-SUMMARY.md` | 📋 Ringkasan teknis |
| `README.md` | ℹ️ File ini |

### **Asset Testing**
| File | Tujuan |
|------|---------|
| `Team-13-Crowdfunding.postman_collection.json` | 15 API requests |
| `Team-13-Local.postman_environment.json` | Variabel & konfigurasi |

---

## 🎯 **Apa yang Akan Ditest?**

```
✓ 15 API Endpoints
✓ 4 Modul: Auth, Campaign, Donation (Public), Donation (Protected)
✓ 25+ Automatic Assertions
✓ Cakupan Lengkap: CRUD, Status, Filtering, Stats
✓ Sequential Execution dengan Variable Passing
✓ Waktu Eksekusi ~5-7 Detik
```

---

## 🔧 **Perintah Umum**

### Jalankan Normal
```bash
node postman/runner.js
```

### Dengan Opsi Custom
```bash
# Lebih lambat, lebih aman (untuk sistem yang loaded)
node postman/runner.js --delay 500 --timeout 10000

# Lebih cepat (untuk testing lokal)
node postman/runner.js --delay 50 --timeout 3000

# Stop di error pertama
node postman/runner.js --stop-on-error

# Izinkan self-signed certificates
node postman/runner.js --insecure
```

---

## ✅ **Prasyarat**

- ✓ Node.js v14+
- ✓ npm terinstall
- ✓ Laravel berjalan di `http://localhost:8000`
- ✓ Database dengan user test (`personal@test.local`)

---

## 📊 **Apa yang Terjadi Saat Menjalankan Tests?**

1. **Login** - Dapatkan JWT token
2. **Campaign CRUD** - Membuat, membaca, update, menghapus campaigns
3. **Donation Processing** - Membuat donations, dapatkan stats
4. **Protected Endpoints** - Akses data user-specific dengan JWT
5. **Cleanup** - Logout dan verifikasi token revocation

**Hasil:** JSON report di `test-results.json`

---

## 🐛 **Troubleshooting**

### "Newman not found"
```bash
npm install -g newman
```

### "Server not responding"
```bash
# Cek apakah running
curl http://localhost:8000/api/campaigns

# Mulai server
docker compose up
# atau
php artisan serve
```

### "401 Unauthorized"
- Verifikasi user test: `personal@test.local`
- Password: `Test12345!`
- Jalankan: `php artisan db:seed`

**Butuh bantuan lebih?** Lihat [POSTMAN-RUNNER-GUIDE.md](POSTMAN-RUNNER-GUIDE.md#troubleshooting)

---

## 🌐 **Alternatif: Gunakan Postman GUI**

Import di Postman Desktop:
1. Klik **Import**
2. Pilih:
   - `Team-13-Crowdfunding.postman_collection.json`
   - `Team-13-Local.postman_environment.json`
3. Pilih environment: dropdown → **Team-13-Local**
4. Jalankan: **Runner** → Pilih collection → **Run**

---

## 🎓 **Peta Dokumentasi**

```
README.md (Anda di sini)
├── QUICKSTART.md ────────────→ Panduan start cepat
├── POSTMAN-RUNNER-GUIDE.md ──→ Dokumentasi lengkap
└── IMPLEMENTATION-SUMMARY.md ─→ Detail teknis

runner.js ────────────────────→ Gunakan ini (recommended)
runner.bat, runner.ps1 ──────→ Atau alternatif lain

Team-13-Crowdfunding.postman_collection.json
Team-13-Local.postman_environment.json
└─────────────────────────────→ Asset testing
```

---

## 🎯 **Langkah Selanjutnya**

### Opsi A: Jalankan Tests Sekarang
```bash
node postman/runner.js
```

### Opsi B: Belajar Dulu
Baca → [QUICKSTART.md](QUICKSTART.md)

### Opsi C: Pendalaman
Explore → [POSTMAN-RUNNER-GUIDE.md](POSTMAN-RUNNER-GUIDE.md)

---

## 📞 **Support**

**Ada masalah?** Cek:
1. Apakah Laravel berjalan? → `curl http://localhost:8000/api/campaigns`
2. Database sudah di-seed? → `php artisan db:seed`
3. Newman terinstall? → `npm install -g newman`

**Masih stuck?** Lihat [Troubleshooting](POSTMAN-RUNNER-GUIDE.md#troubleshooting) di panduan lengkap.

---

## ✨ **Fitur**

- ✅ Cross-platform (Windows, macOS, Linux)
- ✅ Automated test execution
- ✅ Pre-configured variables
- ✅ JSON report generation
- ✅ CI/CD ready
- ✅ Zero manual setup
- ✅ Configurable delays & timeouts
- ✅ Detailed error reporting

---

## 🎉 **Ringkasan**

Anda punya semua yang diperlukan untuk automation API testing! 

**Pilih runner Anda:**
- 🌟 **Node.js** (recommended) → `node postman/runner.js`
- 🪟 **Windows** → `postman\runner.bat`
- ⚡ **PowerShell** → `.\runner.ps1`

**Kemudian jalankan:** Tests akan eksekusi otomatis dengan 25+ assertions! ✅

---

**Version:** 1.0  
**Last Updated:** April 25, 2026  
**Status:** ✅ Siap untuk production use
