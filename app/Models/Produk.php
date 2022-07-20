<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\Ulasan;

class Produk extends Model
{
    // use HasFactory;
    public $timestamps      = false;
    protected $table        = 'produk';
    protected $primaryKey   = 'id';

    protected $fillable = [
        'id_kategori', 
        'nama_produk', 
        'kondisi', 
        'jenis', 
        'berat',
        'warna',
        'ukuran',
        'harga',
        'deskripsi',
        'gambar',
        'stok'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function getNextId() 
    {
        $statement = DB::select("show table status like 'produk'");

        return $statement[0]->Auto_increment;
    }

    public function updateStok($id, $qty)
    {
        $produk = $this->find($id);
        $produk->stok = $produk->stok + $qty;
        $produk->save();
        
        return true;
    }

    public function getTransaksiProduk($id_produk)
    {
        $trx = Transaksi::whereRaw('json_contains(list_produk, \'{"id":"'.$id_produk.'"}\')')->get();

        $jumlah_pembelian   = 0;
        $jumlah_nilai       = 0;

        if($trx->count() > 0)
        {
            foreach ($trx as $data_trx) 
            {
                $data_ulasan   = Ulasan::find($data_trx->nomor_transaksi);
                $jumlah_nilai += empty($data_ulasan)?5:$data_ulasan->nilai;

                $list_produk = json_decode($data_trx->list_produk);
                foreach($list_produk as $produk)
                {
                    if($produk->id == $id_produk)
                    {
                        $jumlah_pembelian += $produk->quantity;
                        break;
                    }
                }
            }
        }

        $data = ["jumlah_pembelian" => $jumlah_pembelian, "rating" => round($jumlah_nilai/$trx->count(), 1)];

        return $data;
    }
}
