<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProduksiUserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@modifikasiori.com'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'role' => 'Admin',
                'section' => null,
                'kota_id' => null,
                'warehouse_id' => null,
            ]
        );

        // Produksi - Manager
        User::updateOrCreate(
            ['email' => 'manager.produksi@modifikasiori.com'],
            [
                'name' => 'Manager Produksi',
                'username' => 'manager.produksi',
                'password' => Hash::make('password'),
                'role' => 'Manager',
                'section' => 'produksi',
                'kota_id' => null,
                'warehouse_id' => null,
            ]
        );

        // Produksi - Staf
        User::updateOrCreate(
            ['email' => 'staf.produksi@modifikasiori.com'],
            [
                'name' => 'Staf Produksi',
                'username' => 'staf.produksi',
                'password' => Hash::make('password'),
                'role' => 'Staf',
                'section' => 'produksi',
                'kota_id' => null,
                'warehouse_id' => null,
            ]
        );

        // Warehouse - Manager
        User::updateOrCreate(
            ['email' => 'manager.warehouse@modifikasiori.com'],
            [
                'name' => 'Manager Warehouse',
                'username' => 'manager.warehouse',
                'password' => Hash::make('password'),
                'role' => 'Manager',
                'section' => 'warehouse',
                'kota_id' => null,
                'warehouse_id' => null,
            ]
        );

        // Warehouse - Staf
        User::updateOrCreate(
            ['email' => 'staf.warehouse@modifikasiori.com'],
            [
                'name' => 'Staf Warehouse',
                'username' => 'staf.warehouse',
                'password' => Hash::make('password'),
                'role' => 'Staf',
                'section' => 'warehouse',
                'kota_id' => null,
                'warehouse_id' => null,
            ]
        );

        // Speedshop - Manager
        User::updateOrCreate(
            ['email' => 'manager.speedshop@modifikasiori.com'],
            [
                'name' => 'Manager Speedshop',
                'username' => 'manager.speedshop',
                'password' => Hash::make('password'),
                'role' => 'Manager',
                'section' => 'speedshop',
                'kota_id' => null,
                'warehouse_id' => null,
            ]
        );

        // Speedshop - Staf
        User::updateOrCreate(
            ['email' => 'staf.speedshop@modifikasiori.com'],
            [
                'name' => 'Staf Speedshop',
                'username' => 'staf.speedshop',
                'password' => Hash::make('password'),
                'role' => 'Staf',
                'section' => 'speedshop',
                'kota_id' => null,
                'warehouse_id' => null,
            ]
        );
    }
}
