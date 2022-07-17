<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Kategori;

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
}
