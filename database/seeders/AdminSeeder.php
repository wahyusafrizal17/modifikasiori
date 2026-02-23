<?php

namespace Database\Seeders;

use App\Models\Kota;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $jakarta = Kota::firstOrCreate(['nama' => 'JAKARTA']);

        User::updateOrCreate(
            ['email' => 'admin@modifikasiori.com'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'role' => 'Admin',
                'kota_id' => $jakarta->id,
                'warehouse_id' => null,
            ]
        );
    }
}
