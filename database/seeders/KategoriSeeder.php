<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    private $data = [
        ['nama_kategori' => 'Makanan', 'jenis' => 0, 'icon' => ''],
        ['nama_kategori' => 'Pakaian', 'jenis' => 0, 'icon' => ''],
        ['nama_kategori' => 'Jasa', 'jenis' => 1, 'icon' => ''],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data as $d) 
        {
            DB::table('kategori')->insert($d);
        }
    }
}
