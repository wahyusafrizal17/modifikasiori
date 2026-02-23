<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\JasaServis;
use App\Models\Kota;
use App\Models\Product;
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

        $warehouses = [];
        foreach ($warehouseData as $w) {
            $kotaModel = Kota::where('nama', $w['kota'])->first();
            $warehouses[$w['nama']] = Warehouse::firstOrCreate(['nama' => $w['nama']], [
                'nama' => $w['nama'],
                'no_hp' => $w['no_hp'],
                'alamat' => $w['alamat'],
                'kota_id' => $kotaModel?->id,
            ]);
        }

        $whBahanBaku = $warehouses['01-BAHAN BAKU-KTP'];

        $categories = collect([
            '4.1 CH-MANGKOK GANDA CUSTOM',
            '4.2 FC-RUMAH ROLLER',
            '4.3 FD-KIPAS PULLEY',
            '4.4 FC-FD (SET PULLEY)',
            '4.5 RMP- RAMPLATE',
            '4.6 DAYTONA',
            '4.7 BRT',
            '4.8 TDR',
            '4.9 GATES',
            '4.10 DR PULLEY',
            '4.11 PROPER',
            '4.12 RX7',
            '4.13 ARUMI',
            '4.14 RCB',
            '4.15 UMA RACING',
            '4.16 HGP',
            '4.17 YGP',
            '4.18 PLATINUM',
        ])->map(fn ($nama) => Category::updateOrCreate(
            ['nama' => $nama],
            ['warehouse_id' => $whBahanBaku->id]
        ));

        $catCustom = $categories->first();

        $products = [
            ['kode_produk' => 'CH-K16-M1', 'nama_produk' => 'CUSTOM LUBANG-MANGKOK GANDA CUSTOM', 'category_id' => $catCustom->id, 'jumlah' => 0, 'harga_pembelian' => 93375, 'harga_jual' => 210000, 'warehouse_id' => $whBahanBaku->id],
            ['kode_produk' => 'CH-KVV-M1', 'nama_produk' => 'CUSTOM LUBANG-MANGKOK GANDA CUSTOM', 'category_id' => $catCustom->id, 'jumlah' => 0, 'harga_pembelian' => 90250, 'harga_jual' => 210000, 'warehouse_id' => $whBahanBaku->id],
            ['kode_produk' => 'CH-KVB-M1', 'nama_produk' => 'CUSTOM LUBANG-MANGKOK GANDA CUSTOM', 'category_id' => $catCustom->id, 'jumlah' => 0, 'harga_pembelian' => 119000, 'harga_jual' => 240000, 'warehouse_id' => $whBahanBaku->id],
            ['kode_produk' => 'CH-K16-M2', 'nama_produk' => 'CUSTOM NEW-MANGKOK GANDA', 'category_id' => $catCustom->id, 'jumlah' => 0, 'harga_pembelian' => 93375, 'harga_jual' => 190000, 'warehouse_id' => $whBahanBaku->id],
            ['kode_produk' => 'CH-KVV-M2', 'nama_produk' => 'CUSTOM NEW-MANGKOK GANDA', 'category_id' => $catCustom->id, 'jumlah' => 0, 'harga_pembelian' => 90250, 'harga_jual' => 190000, 'warehouse_id' => $whBahanBaku->id],
            ['kode_produk' => 'CH-KVB-M2', 'nama_produk' => 'CUSTOM NEW-MANGKOK GANDA', 'category_id' => $catCustom->id, 'jumlah' => 0, 'harga_pembelian' => 100250, 'harga_jual' => 200000, 'warehouse_id' => $whBahanBaku->id],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['kode_produk' => $p['kode_produk']],
                $p
            );
        }

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

        foreach ($jasaServisList as $js) {
            JasaServis::updateOrCreate(
                ['nama' => $js['nama']],
                array_merge($js, ['warehouse_id' => $whBahanBaku->id])
            );
        }
    }
}
