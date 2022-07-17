<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    private $data = [
        ['nama_kategori' => 'Baju', 'jenis' => 0, 'icon' => '1.png'],
        ['nama_kategori' => 'Celana', 'jenis' => 0, 'icon' => '2.png'],
        ['nama_kategori' => 'Sepatu', 'jenis' => 0, 'icon' => '3.png'],
        ['nama_kategori' => 'Jam Tangan', 'jenis' => 0, 'icon' => '4.png'],
        ['nama_kategori' => 'Kacamata', 'jenis' => 0, 'icon' => '5.png'],
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
