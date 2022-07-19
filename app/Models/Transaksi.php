<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Produk;
use App\Models\Ulasan;

class Transaksi extends Model
{
    // use HasFactory;
    public $timestamps      = false;
    protected $table        = 'transaksi';
    protected $primaryKey   = 'nomor_transaksi';
    protected $keyType      = 'string';
    public $incrementing    = false;

    protected $fillable = [
        'nomor_transaksi', 
        'waktu_transaksi',
        'id_customer',
        'telp',
        'nama',
        'alamat',
        'list_produk',
        'sub_total',
        'biaya_ongkir',
        'diskon',
        'total_bayar',
        'status',
        'id_pembayaran',
        'bukti_pembayaran',
        'kurir',
        'nomor_resi',
        'tracking'
    ];

    public function ulasan()
    {
        return $this->hasOne(Ulasan::class);
    }

    public function generateNomorTransaksi()
    {
        $tgl    = date('Y-m-d');
        $data   = DB::select("SELECT * FROM transaksi WHERE date(waktu_transaksi)='$tgl' ORDER BY nomor_transaksi DESC LIMIT 1");
        
        if(isset($data[0]))
        {
            $last_code      = $data[0]->nomor_transaksi;
            $lastIncreament = substr($last_code, -3);
            $nomor_transaksi = 'T' . date('ymd') . str_pad($lastIncreament + 1, 3, 0, STR_PAD_LEFT);
        }
        else
        {
            $nomor_transaksi = "T" . date('ymd') . "001";
        }

        return $nomor_transaksi;
    }

    public function hitungOngkir($parameter)
    {
        extract($parameter);

        $url_api = 'https://api.rajaongkir.com/starter/';
        $api_key = '19ddbb5173b42a658d9e8b5f48a2b2b4';

        $response = Http::post($url_api."/cost", [
            'key'           => $api_key,
            'origin'        => 23,
            'destination'   => $kota_tujuan,
            'weight'        => $berat,
            'courier'       => $kurir
        ]);

        foreach($response['rajaongkir']['results'][0]['costs'] as $value)
        {
            $data[$value['service']] = $value['cost'][0]['value'];
        }

        $ongkir = !empty($data[$layanan_kurir])?$data[$layanan_kurir]:0;

        return $ongkir;
    }

    public function hitungTotal($parameter)
    {
        extract($parameter);

        $arr_produk = explode(",", $list_produk);
        $arr_qty    = explode(",", $qty_produk);

        $total_harga    = 0;
        $total_berat    = 0;
        $i              = 0;
        foreach($arr_produk as $id_produk)
        {
            $data_produk = Produk::find($id_produk);
            $total_harga += $data_produk->harga * $arr_qty[$i];
            $total_berat += $data_produk->berat * $arr_qty[$i];

            $i++;
        }
        
        $customer = auth()->user();

        $parameter = [
            "kota_tujuan"   => $customer->city,
            "berat"         => $total_berat,
            "kurir"         => $kurir,
            "layanan_kurir" => $layanan_kurir
        ];

        $biaya_ongkir = $this->hitungOngkir($parameter);

        $data = [
            "subtotal"      => (int)$total_harga,
            "biaya_ongkir"  => (int)$biaya_ongkir,
            "total"         => (int)($total_harga + $biaya_ongkir),
        ];

        return $data;
    }
}
