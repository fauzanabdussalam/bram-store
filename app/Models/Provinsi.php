<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Kota;

class Provinsi extends Model
{
    // use HasFactory;
    public $timestamps      = false;
    protected $table        = 'provinsi';
    protected $primaryKey   = 'id';
    protected $fillable     = ['nama_provinsi'];

    public function kota()
    {
        return $this->hasMany(Kota::class);
    }
}
