<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kategori;
use App\Models\Produk;

class CartController extends Controller
{
    function __construct()
    {
    }
    
    function cart(Request $request)  
    {
        $cartCollection = \Cart::getContent();
        $arr_cart       = array();

        foreach($cartCollection as $data)
        {
            $data_produk = $data->associatedModel;
            
            $arr_cart[] = array(
                "id"        => $data->id,
                "name"      => $data->name,
                "kategori"  => $data->attributes->kategori,
                "price"     => "Rp " . number_format($data->price, 0, ",", "."),
                "quantity"  => ($data_produk->kategori->jenis==0)?str_replace(".", ",", $data->quantity):"-",
                "total"     => "Rp " . number_format(\Cart::get($data->id)->getPriceSum(), 0, ",", "."),
            );
        }

        $data['cart']               = $arr_cart;
        $data['total_bayar']        = "Rp " . number_format(\Cart::getTotal(), 0, ",", ".");
        $data['total_bayar_int']    = \Cart::getTotal();
        
        return response()->json($data);
    }

    function clear()
    {
        \Cart::clear();
        
        return response()->json(true);
    }
    
    function add(Request $request)
    {
        $data_produk    = Produk::with('kategori')->find($request->id);
        $data_cart      = \Cart::get($request->id);

        if(empty($data_cart) || $data_produk->kategori->jenis == 0)
        {
            \Cart::add([
                [
                    'id'                => $request->id,
                    'name'              => $data_produk->nama_produk,
                    'price'             => $data_produk->harga,
                    'quantity'          => $request->qty,
                    'attributes'        => array('kategori' => $data_produk->kategori->nama_kategori),
                    'associatedModel'   => $data_produk,
                ] 
            ]);
        }
        
        return response()->json(true);
    }

    function remove(Request $request)
    {
        \Cart::remove($request->id);
        
        return response()->json(true);
    }
}
