<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdukSeeder extends Seeder
{
    private $data = [
        ['id_kategori' => 1, 'nama_produk' => 'Kaos Epidemic', 'berat' => 100, 'warna' => 'Hitam', 'ukuran' => 'S,M,L,XL', 'harga' => 40000, 'stok' => 100, 'deskripsi' => 'test', 'gambar' => '1.jpg'],
        ['id_kategori' => 1, 'nama_produk' => 'Kaos Polos', 'berat' => 100, 'warna' => 'Hitam', 'ukuran' => 'S,M,L,XL', 'harga' => 35000, 'stok' => 100, 'deskripsi' => 'test', 'gambar' => '2.jpg'],
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
            DB::table('produk')->insert($d);
        }
    }
}
