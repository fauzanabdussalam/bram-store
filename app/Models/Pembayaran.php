<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    public $timestamps      = false;
    protected $table        = 'pembayaran';
    protected $primaryKey   = 'id';
    protected $fillable     = ['nama_pembayaran', 'nomor_rekening', 'atas_nama'];
}
