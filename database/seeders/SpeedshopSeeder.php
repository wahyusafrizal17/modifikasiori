<?php

namespace Database\Seeders;

use App\Models\Kota;
use App\Models\Speedshop;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class SpeedshopSeeder extends Seeder
{
    public function run(): void
    {
        $supplierWarehouse = Warehouse::where('nama', '02-ONLINE PACKING UTAMA-KTP')->first();

        $data = [
            ['nama' => '03-SPEED SHOP BANDUNG-KTP', 'no_hp' => '085353300103', 'alamat' => 'Jl. Pasir Panjang RT 02 RW 16 Desa Cilampeni Kec.', 'kota' => 'Bandung-KTP-SS'],
            ['nama' => '04-SPEED SHOP DEPOK-DPK', 'no_hp' => '085353300102', 'alamat' => 'Jl. Raya Cisalak, KM 31, NO 9A, RT 006 / RW 005 Ke', 'kota' => 'DEPOK'],
            ['nama' => '05-SPEED SHOP BOGOR-BGR', 'no_hp' => '081328773881', 'alamat' => 'Jl. Brigjen Saptadji Hadiprawira No.9, RT.01/RW.09', 'kota' => 'BOGOR'],
        ];

        foreach ($data as $row) {
            $kota = Kota::where('nama', $row['kota'])->first();
            $mutasiWarehouse = Warehouse::where('nama', $row['nama'])->first();
            Speedshop::firstOrCreate(
                ['nama' => $row['nama']],
                [
                    'nama' => $row['nama'],
                    'no_hp' => $row['no_hp'],
                    'alamat' => $row['alamat'],
                    'kota_id' => $kota?->id,
                    'warehouse_id' => $supplierWarehouse?->id,
                    'mutasi_warehouse_id' => $mutasiWarehouse?->id,
                ]
            );
        }

        $this->command->info('Seeded: ' . count($data) . ' speedshops.');
    }
}
