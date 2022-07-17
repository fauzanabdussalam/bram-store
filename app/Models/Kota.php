<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Provinsi;
use App\Models\Customer;

class Kota extends Model
{
    public $timestamps      = false;
    protected $table        = 'kota';
    protected $primaryKey   = 'id';
    protected $fillable     = ['id_provinsi', 'nama_kota'];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi');
    }

    public function customer()
    {
        return $this->hasMany(Customer::class);
    }
}
