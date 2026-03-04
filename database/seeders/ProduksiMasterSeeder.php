<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\BahanSiapProduksi;
use App\Models\Kemasan;
use App\Models\Supplier;
use App\Models\TeamProduksi;
use Illuminate\Database\Seeder;

class ProduksiMasterSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            'Team A - Mixing',
            'Team B - Filling',
            'Team C - Packing',
            'Team D - QC',
            'Team E - Finishing',
        ];

        foreach ($teams as $nama) {
            TeamProduksi::firstOrCreate(['nama' => $nama]);
        }
        $this->command->info('Seeded ' . count($teams) . ' team produksi.');

        $supplier1 = Supplier::first();
        $supplier2 = Supplier::skip(1)->first();
        $supplier3 = Supplier::skip(2)->first();

        $bahanBakus = [
            ['kode' => 'BB-001', 'nama' => 'Oli Base SAE 10W-40', 'harga' => 45000, 'supplier_id' => $supplier1?->id],
            ['kode' => 'BB-002', 'nama' => 'Oli Base SAE 20W-50', 'harga' => 48000, 'supplier_id' => $supplier1?->id],
            ['kode' => 'BB-003', 'nama' => 'Additive EP (Extreme Pressure)', 'harga' => 120000, 'supplier_id' => $supplier2?->id],
            ['kode' => 'BB-004', 'nama' => 'Additive Anti-Wear', 'harga' => 95000, 'supplier_id' => $supplier2?->id],
            ['kode' => 'BB-005', 'nama' => 'Additive Viscosity Index Improver', 'harga' => 135000, 'supplier_id' => $supplier2?->id],
            ['kode' => 'BB-006', 'nama' => 'Pewarna Merah', 'harga' => 25000, 'supplier_id' => $supplier3?->id],
            ['kode' => 'BB-007', 'nama' => 'Pewarna Biru', 'harga' => 25000, 'supplier_id' => $supplier3?->id],
            ['kode' => 'BB-008', 'nama' => 'Coolant Concentrate', 'harga' => 78000, 'supplier_id' => $supplier1?->id],
            ['kode' => 'BB-009', 'nama' => 'Brake Fluid DOT 4 Bulk', 'harga' => 65000, 'supplier_id' => $supplier1?->id],
            ['kode' => 'BB-010', 'nama' => 'Chain Lube Base Oil', 'harga' => 55000, 'supplier_id' => $supplier3?->id],
        ];

        foreach ($bahanBakus as $bb) {
            BahanBaku::firstOrCreate(['kode' => $bb['kode']], array_merge($bb, ['stok' => 0]));
        }
        $this->command->info('Seeded ' . count($bahanBakus) . ' bahan baku.');

        $kemasans = [
            ['kode' => 'KM-001', 'nama' => 'Botol 800ml Oli Mesin', 'harga' => 3500, 'supplier_id' => $supplier3?->id],
            ['kode' => 'KM-002', 'nama' => 'Botol 1L Oli Mesin', 'harga' => 4200, 'supplier_id' => $supplier3?->id],
            ['kode' => 'KM-003', 'nama' => 'Botol 500ml Coolant', 'harga' => 2800, 'supplier_id' => $supplier3?->id],
            ['kode' => 'KM-004', 'nama' => 'Botol 300ml Brake Fluid', 'harga' => 2200, 'supplier_id' => $supplier3?->id],
            ['kode' => 'KM-005', 'nama' => 'Botol 200ml Chain Lube', 'harga' => 1800, 'supplier_id' => $supplier3?->id],
            ['kode' => 'KM-006', 'nama' => 'Tutup Botol Merah', 'harga' => 500, 'supplier_id' => $supplier3?->id],
            ['kode' => 'KM-007', 'nama' => 'Tutup Botol Biru', 'harga' => 500, 'supplier_id' => $supplier3?->id],
            ['kode' => 'KM-008', 'nama' => 'Label Oli Mesin 800ml', 'harga' => 350, 'supplier_id' => $supplier3?->id],
            ['kode' => 'KM-009', 'nama' => 'Label Oli Mesin 1L', 'harga' => 400, 'supplier_id' => $supplier3?->id],
            ['kode' => 'KM-010', 'nama' => 'Kardus Box isi 12', 'harga' => 5500, 'supplier_id' => $supplier3?->id],
        ];

        foreach ($kemasans as $km) {
            Kemasan::firstOrCreate(['kode' => $km['kode']], array_merge($km, ['stok' => 0]));
        }
        $this->command->info('Seeded ' . count($kemasans) . ' kemasan.');

        $bspList = [
            ['kode' => 'BSP-0001', 'nama' => 'Oli Mesin 10W-40 (Campuran)'],
            ['kode' => 'BSP-0002', 'nama' => 'Oli Mesin 20W-50 (Campuran)'],
            ['kode' => 'BSP-0003', 'nama' => 'Coolant Siap Pakai'],
            ['kode' => 'BSP-0004', 'nama' => 'Brake Fluid DOT 4 Siap Pakai'],
            ['kode' => 'BSP-0005', 'nama' => 'Chain Lube Siap Pakai'],
        ];

        foreach ($bspList as $bsp) {
            BahanSiapProduksi::firstOrCreate(['kode' => $bsp['kode']], array_merge($bsp, ['stok' => 0]));
        }
        $this->command->info('Seeded ' . count($bspList) . ' bahan siap produksi.');
    }
}
