# Laporan Hasil Stress Testing – Medium Load (20 Virtual Users)

## Ringkasan Pengujian

Pengujian dilakukan menggunakan Artillery dengan skenario beban sedang (Medium Load) untuk mengevaluasi performa aplikasi crowdfunding setelah dilakukan optimasi pada konfigurasi infrastruktur dan aplikasi.

### Parameter Pengujian

| Parameter             | Nilai            |
| --------------------- | ---------------- |
| Virtual Users (VUs)   | 20               |
| Durasi Pengujian      | 120 detik        |
| Request Rate          | 20 request/detik |
| Total Requests        | 2.400            |
| HTTP 200 (Success)    | 2.400            |
| ERR_SOCKET_TIMEOUT    | 0                |
| Virtual Users Created | 2.400            |
| Virtual Users Failed  | 0                |

## Hasil Pengujian

Hasil pengujian menunjukkan bahwa seluruh request berhasil diproses dengan baik tanpa terjadi timeout maupun kegagalan koneksi. Dari total 2.400 request yang dikirim selama pengujian berlangsung, seluruhnya memperoleh respons HTTP 200 (OK) dengan tingkat keberhasilan 100%.

Selain itu, tidak ditemukan error berupa `ERR_SOCKET_TIMEOUT`, sehingga dapat disimpulkan bahwa aplikasi mampu mempertahankan stabilitas layanan selama menerima beban 20 Virtual Users secara bersamaan.

## Optimasi yang Diterapkan

Beberapa optimasi yang dilakukan sebelum pengujian meliputi:

1. **PHP-FPM**

   * Mengubah process manager menjadi `dynamic`.
   * Meningkatkan jumlah worker melalui konfigurasi `pm.max_children`.
   * Menyesuaikan parameter `start_servers`, `min_spare_servers`, dan `max_spare_servers`.
   * Menambah nilai `request_terminate_timeout`.

2. **Nginx**

   * Menyesuaikan konfigurasi `keepalive_timeout`.
   * Meningkatkan ukuran buffer FastCGI untuk menangani respons yang lebih besar.
   * Menyesuaikan timeout FastCGI agar lebih toleran terhadap beban tinggi.

3. **Docker Container**

   * Meningkatkan alokasi resource container aplikasi menjadi 8 GB RAM dan 8 CPU.
   * Menambahkan worker queue untuk memproses job secara asynchronous.

4. **MySQL**

   * Meningkatkan `max_connections`.
   * Menambah ukuran `innodb_buffer_pool_size` untuk meningkatkan performa akses data.

5. **Redis Cache**

   * Mengaktifkan caching pada beberapa endpoint untuk mengurangi beban query database.

## Analisis

Berdasarkan hasil pengujian, optimasi yang diterapkan berhasil menghilangkan bottleneck yang sebelumnya menyebabkan timeout pada saat load testing. Penggunaan Redis Cache membantu mengurangi beban database, sementara peningkatan kapasitas PHP-FPM dan resource container memungkinkan aplikasi menangani request secara lebih efisien.

Tidak ditemukan indikasi overload pada aplikasi selama pengujian berlangsung, sehingga sistem dapat dikategorikan stabil pada skenario beban sedang (20 Virtual Users).

## Kesimpulan

Berdasarkan hasil stress testing yang telah dilakukan, aplikasi crowdfunding berhasil menangani beban 20 Virtual Users selama 120 detik dengan tingkat keberhasilan 100%.

Seluruh 2.400 request berhasil diproses tanpa timeout maupun error, menunjukkan bahwa optimasi pada PHP-FPM, Nginx, Redis, MySQL, dan Docker Container memberikan peningkatan performa yang signifikan dibandingkan konfigurasi sebelumnya.

## Rekomendasi

Sebagai tahap lanjutan, disarankan untuk melakukan pengujian dengan beban yang lebih tinggi (misalnya 80 Virtual Users) guna mengetahui batas kapasitas sistem serta mengidentifikasi potensi bottleneck yang mungkin muncul pada skenario penggunaan yang lebih berat.
