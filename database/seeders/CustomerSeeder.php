<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customer')->insert([
            'name'          => 'budi',
            'phone'         => '08123456789',
            'email'         => 'budi@gmail.com',
            'city'          => 107,
            'address'       => 'Komp. Taman Bumi Prima, Jalan Bumi Prima Raya P-7, Cimahi Utara, Kota Cimahi, Jawa Barat 40135',
            'birthdate'     => '2000-01-01',
            'gender'        => 'Laki-Laki',
            'password'      => Hash::make('123'),
            'created_at'    => NOW(),
            'updated_at'    => NOW(),
        ]);
    }
}
