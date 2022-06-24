<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PembayaranSeeder extends Seeder
{
    private $data = [
        ['nama_pembayaran' => 'Bank BCA', 'nomor_rekening' => '291038109238', 'atas_nama' => 'Abraham'],
        ['nama_pembayaran' => 'Bank BNI', 'nomor_rekening' => '943594387192', 'atas_nama' => 'Abraham'],
        ['nama_pembayaran' => 'Bank Mandiri', 'nomor_rekening' => '120939833243', 'atas_nama' => 'Abraham'],
        ['nama_pembayaran' => 'OVO', 'nomor_rekening'   => '08123456789', 'atas_nama' => 'Abraham'],
        ['nama_pembayaran' => 'GOPAY', 'nomor_rekening' => '08123456789', 'atas_nama' => 'Abraham'],

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
            DB::table('pembayaran')->insert($d);
        }
    }
}
