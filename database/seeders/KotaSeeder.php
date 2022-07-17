<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class KotaSeeder extends Seeder
{
    private $url_api    = 'https://api.rajaongkir.com/starter/';
    private $api_key    = '19ddbb5173b42a658d9e8b5f48a2b2b4';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arr_provinsi = $this->getProvinsi();

        if(!empty($arr_provinsi))
        {
            foreach ($arr_provinsi as $d) 
            {
                DB::table('provinsi')->insert($d);
            }
        }

        $arr_kota = $this->getKota();

        if(!empty($arr_kota))
        {
            foreach ($arr_kota as $d) 
            {
                DB::table('kota')->insert($d);
            }
        }
    }

    function getProvinsi()
    {
        $response = Http::get($this->url_api."/province", ['key' => $this->api_key]);

        $data = [];
        foreach($response['rajaongkir']['results'] as $value)
        {
            $data[] = ['id' => $value['province_id'], 'nama_provinsi' => $value['province']];
        }

        return $data;
    }

    function getKota()
    {
        $response = Http::get($this->url_api."/city", ['key' => $this->api_key]);

        $data = [];
        foreach($response['rajaongkir']['results'] as $value)
        {
            $data[] = ['id' => $value['city_id'], 'id_provinsi' => $value['province_id'], 'nama_kota' => $value['type'] . " " . $value['city_name']];
        }

        return $data;
    }
}
