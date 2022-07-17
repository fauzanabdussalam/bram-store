<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Kota;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $guard = 'customer';
    protected $table = 'customer';
    
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phone',
        'name',
        'email',
        'city',
        'address',
        'birthdate',
        'gender',
        'picture',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function kota()
    {
        return $this->belongsTo(Kota::class, 'city');
    }

    public function getJumlahTransaksi($id) 
    {
        $query = DB::table('transaksi')->where("id_customer", $id);

        return 0;
    }

    public function getNextId() 
    {
        $statement = DB::select("show table status like 'customer'");

        return $statement[0]->Auto_increment;
    }
}
