<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;

class Ulasan extends Model
{
    // use HasFactory;
    public $timestamps      = false;
    protected $table        = 'ulasan';
    protected $primaryKey   = 'nomor_transaksi';
    protected $keyType      = 'string';
    public $incrementing    = false;

    protected $fillable = [
        'nomor_transaksi', 
        'nilai', 
        'ulasan',
        'gambar',
        'created_at'
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'nomor_transaksi');
    }

    public function getNextId() 
    {
        $statement = DB::select("show table status like 'ulasan'");

        return $statement[0]->Auto_increment;
    }
}
