<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('campaigns')->delete();

        $now = now();
        $batchSize = 1000;
        $totalCampaigns = 20000;

        $titles = [
            'Bantu Korban Banjir',
            'Operasi Katarak Gratis',
            'Beasiswa Anak Yatim',
            'Renovasi Masjid',
            'Sumur Bersih untuk Desa',
            'Panti Asuhan Sejahtera',
            'Bantuan Pangan Darurat',
            'Sekolah Gratis Pedalaman',
            'Rumah Singgah Pasien',
            'Ambulans Desa Terpencil',
            'Bantuan Gempa Bumi',
            'Gizi Buruk Balita',
            'Bedah Rumah Tidak Layak Huni',
            'Perpustakaan Keliling',
            'Kaki Palsu untuk Difabel',
            'Air Bersih Pulau Terluar',
            'Bantuan Ibu Hamil Kurang Mampu',
            'Beasiswa SMA Berprestasi',
            'Dapur Umum Warga Miskin',
            'Kursi Roda untuk Lansia',
            'Tanggap Darurat Kebakaran',
            'Biaya Operasi Jantung',
            'Pondok Pesantren Mandiri',
            'Ladang Hidroponik Desa',
            'Pelatihan UMKM Ibu Rumah Tangga',
        ];

        $descriptions = [
            'Program ini bertujuan membantu masyarakat yang terdampak bencana dan membutuhkan pertolongan segera.',
            'Donasi Anda akan digunakan secara transparan dan dilaporkan secara berkala kepada para donatur.',
            'Setiap rupiah yang Anda sumbangkan akan memberikan dampak nyata bagi kehidupan mereka.',
            'Bersama kita bisa mewujudkan harapan dan masa depan yang lebih baik untuk sesama.',
            'Program telah terverifikasi dan diawasi langsung oleh relawan terlatih di lapangan.',
            'Bantuan Anda sangat berarti untuk keberlangsungan program kemanusiaan ini.',
            'Mari bersatu membantu mereka yang membutuhkan, sekecil apapun kontribusi Anda sangat berharga.',
            'Program ini telah berjalan sejak lama dan terbukti memberikan manfaat nyata bagi penerima.',
        ];

        $batch = [];

        for ($i = 1; $i <= $totalCampaigns; $i++) {
            $titleBase = $titles[array_rand($titles)];
            $title = $titleBase . ' #' . $i;
            $status = random_int(1, 100) <= 70 ? 'aktif' : 'selesai';

            $batch[] = [
                'title'         => $title,
                'description'   => $descriptions[array_rand($descriptions)],
                'target_amount' => random_int(5000000, 500000000),
                'status'        => $status,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];

            if (count($batch) >= $batchSize) {
                DB::table('campaigns')->insert($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            DB::table('campaigns')->insert($batch);
        }
    }
}