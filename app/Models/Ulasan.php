<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ulasan extends Model
{
    // use HasFactory;
    public $timestamps      = false;
    protected $table        = 'ulasan';
    protected $primaryKey   = 'nomor_transaksi';

    protected $fillable = [
        'nilai', 
        'ulasan',
        'gambar',
        'created_at'
    ];

    public function getNextId() 
    {
        $statement = DB::select("show table status like 'ulasan'");

        return $statement[0]->Auto_increment;
    }
}
