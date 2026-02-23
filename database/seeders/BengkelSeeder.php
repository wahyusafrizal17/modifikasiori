<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use App\Models\Kota;
use App\Models\Mekanik;
use App\Models\Pelanggan;
use Illuminate\Database\Seeder;

class BengkelSeeder extends Seeder
{
    public function run(): void
    {
        $jakarta = Kota::where('nama', 'JAKARTA')->first();
        $bandung = Kota::where('nama', 'BANDUNG')->first();
        $surabaya = Kota::where('nama', 'SURABAYA')->first();

        $pelanggans = [
            ['nama' => 'Budi Santoso', 'no_hp' => '081234567890', 'alamat' => 'Jl. Sudirman No. 10', 'kota_id' => $jakarta?->id],
            ['nama' => 'Siti Rahayu', 'no_hp' => '082345678901', 'alamat' => 'Jl. Asia Afrika No. 5', 'kota_id' => $bandung?->id],
            ['nama' => 'Ahmad Fauzi', 'no_hp' => '083456789012', 'alamat' => 'Jl. Tunjungan No. 15', 'kota_id' => $surabaya?->id],
            ['nama' => 'Dewi Lestari', 'no_hp' => '084567890123', 'alamat' => 'Jl. Gatot Subroto No. 20', 'kota_id' => $jakarta?->id],
            ['nama' => 'Rudi Hermawan', 'no_hp' => '085678901234', 'alamat' => 'Jl. Dago No. 8', 'kota_id' => $bandung?->id],
        ];

        foreach ($pelanggans as $p) {
            Pelanggan::firstOrCreate(['nama' => $p['nama']], $p);
        }

        $kendaraans = [
            ['pelanggan' => 'Budi Santoso', 'nomor_polisi' => 'B 1234 ABC', 'merk' => 'Honda', 'tipe' => 'Beat', 'tahun' => 2022],
            ['pelanggan' => 'Budi Santoso', 'nomor_polisi' => 'B 5678 DEF', 'merk' => 'Yamaha', 'tipe' => 'NMAX', 'tahun' => 2023],
            ['pelanggan' => 'Siti Rahayu', 'nomor_polisi' => 'D 9012 GHI', 'merk' => 'Honda', 'tipe' => 'Vario 160', 'tahun' => 2024],
            ['pelanggan' => 'Ahmad Fauzi', 'nomor_polisi' => 'L 3456 JKL', 'merk' => 'Yamaha', 'tipe' => 'Aerox 155', 'tahun' => 2021],
            ['pelanggan' => 'Dewi Lestari', 'nomor_polisi' => 'B 7890 MNO', 'merk' => 'Honda', 'tipe' => 'PCX 160', 'tahun' => 2023],
            ['pelanggan' => 'Rudi Hermawan', 'nomor_polisi' => 'D 2345 PQR', 'merk' => 'Kawasaki', 'tipe' => 'Ninja ZX-25R', 'tahun' => 2022],
        ];

        foreach ($kendaraans as $k) {
            $pel = Pelanggan::where('nama', $k['pelanggan'])->first();
            if ($pel) {
                Kendaraan::firstOrCreate(
                    ['nomor_polisi' => $k['nomor_polisi']],
                    ['pelanggan_id' => $pel->id, 'nomor_polisi' => $k['nomor_polisi'], 'merk' => $k['merk'], 'tipe' => $k['tipe'], 'tahun' => $k['tahun']]
                );
            }
        }

        $mekaniks = [
            ['nama' => 'Joko Widodo', 'no_hp' => '081111222333', 'spesialisasi' => 'Mesin', 'status' => 'aktif'],
            ['nama' => 'Agus Prasetyo', 'no_hp' => '082222333444', 'spesialisasi' => 'Kelistrikan', 'status' => 'aktif'],
            ['nama' => 'Bambang Surya', 'no_hp' => '083333444555', 'spesialisasi' => 'Body & Cat', 'status' => 'aktif'],
            ['nama' => 'Deni Irawan', 'no_hp' => '084444555666', 'spesialisasi' => 'AC & Cooling', 'status' => 'aktif'],
            ['nama' => 'Eko Saputra', 'no_hp' => '085555666777', 'spesialisasi' => 'Transmisi', 'status' => 'nonaktif'],
        ];

        foreach ($mekaniks as $m) {
            Mekanik::firstOrCreate(['nama' => $m['nama']], $m);
        }
    }
}
