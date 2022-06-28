<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
// use App\Models\News;

class Kategori extends Model
{
    // use HasFactory;
    public $timestamps      = false;
    protected $table        = 'kategori';
    protected $primaryKey   = 'id';
    protected $fillable     = ['nama_kategori', 'jenis', 'icon'];

    public function produk()
    {
        // return $this->hasMany(News::class);
    }

    public function getNextId() 
    {
        $statement = DB::select("show table status like 'kategori'");

        return $statement[0]->Auto_increment;
    }
}
