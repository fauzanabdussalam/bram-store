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
}
