<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomerAddress extends Model
{
    // use HasFactory;
    public $timestamps      = false;
    protected $table        = 'customer_address';
    protected $primaryKey   = 'id';
    protected $fillable     = ['id_customer', 'phone', 'name', 'address', 'label'];
}
