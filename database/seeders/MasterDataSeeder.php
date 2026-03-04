<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\JasaServis;
use App\Models\Kota;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $kotas = collect([
            'JAKARTA', 'BANDUNG', 'DEPOK', 'BOGOR',
            'Bandung-KTP-NC', 'Bandung-KTP-OP', 'Bandung-KTP-SS',
            'Bandung-TKK-X1',
        ])->map(fn ($nama) => Kota::firstOrCreate(['nama' => $nama]));

        $warehouseData = [
            ['nama' => '01-BAHAN BAKU-KTP', 'no_hp' => '344234234234', 'alamat' => 'dfdsfsdfsdfdsf', 'kota' => 'JAKARTA'],
            ['nama' => '02-ONLINE PACKING UTAMA-KTP', 'no_hp' => '082368946001', 'alamat' => 'Jl. Pasir Panjang RT 02 RW 16 Desa Cilampeni Kec.', 'kota' => 'Bandung-KTP-OP'],
            ['nama' => '03-SPEED SHOP BANDUNG-KTP', 'no_hp' => '085353300103', 'alamat' => 'Jl. Pasir Panjang RT 02 RW 16 Desa Cilampeni Kec.', 'kota' => 'Bandung-KTP-SS'],
            ['nama' => '04-SPEED SHOP DEPOK-DPK', 'no_hp' => '085353300102', 'alamat' => 'Jl. Raya Cisalak, KM 31, NO 9A, RT 006 / RW 005 Ke', 'kota' => 'DEPOK'],
            ['nama' => '05-SPEED SHOP BOGOR-BGR', 'no_hp' => '081328773881', 'alamat' => 'Jl. Brigjen Saptadji Hadiprawira No.9, RT.01/RW.09', 'kota' => 'BOGOR'],
        ];

        foreach ($warehouseData as $w) {
            $kotaModel = Kota::where('nama', $w['kota'])->first();
            Warehouse::firstOrCreate(['nama' => $w['nama']], [
                'nama' => $w['nama'],
                'no_hp' => $w['no_hp'],
                'alamat' => $w['alamat'],
                'kota_id' => $kotaModel?->id,
            ]);
        }

        $categoryNames = ['Sparepart', 'Oli'];
        $brandNames = ['Modifikasi Ori', 'Speed Shop', 'Honda', 'Yamaha', 'Kawasaki'];

        $supplierData = [
            ['nama' => 'PT Sumber Jaya Motor', 'no_hp' => '08111222333', 'alamat' => 'Jl. Industri No. 10, Bandung', 'kota' => 'BANDUNG'],
            ['nama' => 'CV Mitra Sparepart', 'no_hp' => '08222333444', 'alamat' => 'Jl. Otista No. 25, Jakarta', 'kota' => 'JAKARTA'],
            ['nama' => 'UD Berkah Oli', 'no_hp' => '08333444555', 'alamat' => 'Jl. Raya Depok No. 5', 'kota' => 'DEPOK'],
            ['nama' => 'Toko Bearing Jaya', 'no_hp' => '08444555666', 'alamat' => 'Jl. Soekarno Hatta No. 88, Bandung', 'kota' => 'BANDUNG'],
            ['nama' => 'CV Abadi Motor Parts', 'no_hp' => '08555666777', 'alamat' => 'Jl. Pajajaran No. 12, Bogor', 'kota' => 'BOGOR'],
        ];

        $jasaServisList = [
            ['nama' => 'BENERIN TALI GAS', 'biaya' => 30000],
            ['nama' => 'BENSIN 1L', 'biaya' => 12000],
            ['nama' => 'BLEADING', 'biaya' => 35000],
            ['nama' => 'BONGKAR PASANG FUEL PUMP', 'biaya' => 30000],
            ['nama' => 'BONGKAR PASANG TIANG SOKAR C.L', 'biaya' => 70000],
            ['nama' => 'CABUT BAUT KNALPOT', 'biaya' => 40000],
            ['nama' => 'CASS AKI', 'biaya' => 20000],
            ['nama' => 'CEK KELISTRIKAN', 'biaya' => 30000],
            ['nama' => 'CEK KLAKSON', 'biaya' => 50000],
            ['nama' => 'CEK REM', 'biaya' => 20000],
            ['nama' => 'CEK RPM', 'biaya' => 10000],
            ['nama' => 'CEK TEKANAN BAHAN BAKAR', 'biaya' => 40000],
            ['nama' => 'COAK PISTON', 'biaya' => 35000],
            ['nama' => 'COAK SPAKBOR', 'biaya' => 30000],
            ['nama' => 'FULL KOMPLIT SERVICE 110CC', 'biaya' => 260000],
            ['nama' => 'FULL KOMPLIT SERVICE 125CC +', 'biaya' => 270000],
            ['nama' => 'FULL KOMPLIT SERVICE 250CC +', 'biaya' => 350000],
            ['nama' => 'FULL SERVICE', 'biaya' => 175000],
        ];

        foreach ($categoryNames as $nama) {
            Category::firstOrCreate(['nama' => $nama]);
        }

        foreach ($brandNames as $nama) {
            Brand::firstOrCreate(['nama' => $nama]);
        }

        foreach ($supplierData as $s) {
            $kotaModel = Kota::where('nama', $s['kota'])->first();
            Supplier::firstOrCreate(
                ['nama' => $s['nama']],
                [
                    'no_hp' => $s['no_hp'],
                    'alamat' => $s['alamat'],
                    'kota_id' => $kotaModel?->id,
                ]
            );
        }

        foreach ($jasaServisList as $js) {
            JasaServis::firstOrCreate(
                ['nama' => $js['nama']],
                $js
            );
        }

        $this->command->info("Seeded: " . count($warehouseData) . " warehouses, " . count($categoryNames) . " categories, " . count($brandNames) . " brands, " . count($supplierData) . " suppliers, " . count($jasaServisList) . " jasa servis (global)");
    }
}
