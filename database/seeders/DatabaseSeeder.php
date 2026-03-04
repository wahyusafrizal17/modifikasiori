<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MasterDataSeeder::class,
            SpeedshopSeeder::class,
            AdminSeeder::class,
            ProduksiUserSeeder::class,
            BengkelSeeder::class,
            ProductSeeder::class,
            ProduksiMasterSeeder::class,
        ]);
    }
}
