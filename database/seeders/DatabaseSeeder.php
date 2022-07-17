<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            KotaSeeder::class,
            UserSeeder::class,
            CustomerSeeder::class,
            KategoriSeeder::class,
            ProdukSeeder::class,
            PembayaranSeeder::class,
        ]);
    }
}
