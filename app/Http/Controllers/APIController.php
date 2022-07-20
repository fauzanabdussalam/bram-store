<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Auth;
use Validator;
use File;
use App\Models\Customer;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Pembayaran;
use App\Models\Provinsi;
use App\Models\Kota;
use App\Models\Transaksi;
use App\Models\Ulasan;

class APIController extends Controller
{
    function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->classProduk      = new Produk();
        $this->classTransaksi   = new Transaksi();

        $this->url_api = 'https://api.rajaongkir.com/starter/';
        $this->api_key = '19ddbb5173b42a658d9e8b5f48a2b2b4';

        $this->status_trx   = [
            '0' => 'Belum Dibayar', 
            '6' => 'Menunggu Verifikasi', 
            '1' => 'Lunas', 
            '2' => 'Batal', 
            '3' => 'Diproses', 
            '4' => 'Dikirim', 
            '5' => 'Selesai'
        ];
        
        $this->classTransaksi->batalkanTransaksiExpired();
    }

    function getProfileCustomer()
    {
        $customer = auth()->user();
        $customer->picture = ($customer->picture != "")?URL::asset('images/customer').'/'.$customer->picture:"";
        
        return $customer;
    }

    function changeProfileCustomer(Request $request) {

        $validator = Validator::make($request->all(),[
            'name'      => 'required|string',
            'phone'     => 'required',
            'email'     => 'required|string|email',
            'address'   => 'required|string'
        ]);

        if($validator->fails())
        {
            $response = [
                'status'    => 'error',
                'message'   => $validator->errors()->first()
            ];

            return response()->json($response, 400);       
        }

        $customer = [
            'name'      => $request->name,
            'phone'     => $request->phone,
            'email'     => $request->email,
            'address'   => $request->address,
            'birthdate' => date('Y-m-d', strtotime($request->birthdate)),
            'gender'    => $request->gender,
            'picture'   => $request->picture
        ];

        Customer::find(Auth::user()->id)->update($customer);

        $response = [
            'status'    => 'success',
            'message'   => 'Change Profile Success',
            'content'   => [
                'data' => $customer,
            ]
        ];

        return response()->json($response, 200);
    }

    function changePasswordCustomer(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'old_password'          => 'required|string',
            'new_password'          => 'required|string|min:6',
            'confirm_new_password'  => 'required|string|min:6'
        ]);
        
        if($validator->fails())
        {
            $response = [
                'status'    => 'error',
                'message'   => $validator->errors()->first()
            ];

            return response()->json($response, 400);       
        }

        if($request->new_password != $request->confirm_new_password)
        {
            $ret['status']  = "error";
            $ret['message'] = "Confirm Password didn't match!";

            return response()->json($ret);
        }

        if(!Hash::check($request->old_password, Auth::user()->password))
        {
            $ret['status']  = "error";
            $ret['message'] = "Old Password Wrong!";
        }
        else
        {   
            $data['password'] = Hash::make($request->new_password);

            Customer::find(Auth::user()->id)->update($data);     
    
            $ret['status']  = "success";
            $ret['message'] = "Password changed successfully!";
        }

        return response()->json($ret);
    }

    function getKategori()
    {
        $kategori = Kategori::orderBy('jenis')->orderBy('nama_kategori')->get();

        $data = [];
        foreach($kategori as $row)
        {
            $row->icon  = URL::asset('images/kategori').'/'.$row->icon;
            $data[]     = $row;
        }

        $ret['status']  = "success";
        $ret['data']    = $data;

        return response()->json($ret);
    }

    function getSortProduk()
    {
        $data = ["Pembelian", "Ulasan", "Terbaru", "Harga Tertinggi", "Harga Terendah"];

        $ret['status']  = "success";
        $ret['data']    = $data;

        return response()->json($ret);
    }

    function getProduk(Request $request)
    {
        $produk = Produk::with("kategori")->select('produk.*', 'nama_kategori', 'kategori.jenis AS jenis_produk')->leftJoin('kategori', 'produk.id_kategori', '=', 'kategori.id')->where('stok', '>', 0);
        $produk = ($request->exists('id_kategori') && $request->id_kategori!="")?$produk->where('produk.id_kategori', $request->id_kategori):$produk;
        $produk = ($request->exists('keyword') && $request->keyword!="")?$produk->where('nama_produk', 'LIKE', '%'.$request->keyword.'%')->orWhere('nama_kategori', 'LIKE', '%'.$request->keyword.'%'):$produk;
        $sort = ($request->exists('sort') && $request->sort!="")?$request->sort:0;

        if(in_array($sort, [2,3,4]))
        {
            $data_sort = [
                "2" => ["field" => "created_at", "order" => "desc"],
                "3" => ["field" => "harga", "order" => "desc"],
                "4" => ["field" => "harga", "order" => "asc"],
            ];
    
            $produk = $produk->orderBy($data_sort[$sort]['field'], $data_sort[$sort]['order']);
        }

        $produk = $produk->limit(20)->get();

        $data = [];
        foreach($produk as $row)
        {
            $data_trx = $this->classProduk->getTransaksiProduk($row->id);

            $data[] = [
                "id"                => $row->id,
                "nama_produk"       => $row->nama_produk,
                "kategori"          => $row->nama_kategori,
                "kondisi"           => (!$row->kondisi)?"Baru":"Bekas",
                "jenis_pembelian"   => (!$row->jenis)?"Langsung":"Pre-Order",
                "warna"             => $row->warna,
                "ukuran"            => empty($row->ukuran)?[]:explode(",", $row->ukuran),
                "harga"             => (int)$row->harga,
                "gambar"            => URL::asset('images/produk').'/'.$row->gambar,
                "stok"              => $row->stok,
                "jumlah_pembelian"  => $data_trx['jumlah_pembelian'],
                "rating"            => $data_trx['rating'],
            ];
        }

        if($sort == 0)
        {
            array_multisort(array_column($data, "jumlah_pembelian"), SORT_DESC, $data);
        }
        elseif($sort == 1)
        {
            array_multisort(array_column($data, "rating"), SORT_DESC, $data);
        }

        $ret['status']  = "success";
        $ret['data']    = $data;

        return response()->json($ret);
    }

    function getDetailProduk(Request $request)
    {
        $row = Produk::with('kategori')->find($request->id);

        $data_trx = $this->classProduk->getTransaksiProduk($request->id);
        
        $data[] = [
            "id"                => $row->id,
            "nama_produk"       => $row->nama_produk,
            "jenis_produk"      => (!$row->jenis_produk)?"Barang":"Jasa",
            "kategori"          => $row->kategori->nama_kategori,
            "kondisi"           => (!$row->kondisi)?"Baru":"Bekas",
            "jenis_pembelian"   => (!$row->jenis)?"Langsung":"Pre-Order",
            "berat"             => $row->berat . " gr",
            "warna"             => $row->warna,
            "ukuran"            => empty($row->ukuran)?[]:explode(",", $row->ukuran),
            "harga"             => (int)$row->harga,
            "deskripsi"         => $row->deskripsi,
            "gambar"            => URL::asset('images/produk').'/'.$row->gambar,
            "stok"              => $row->stok,
            "jumlah_pembelian"  => $data_trx['jumlah_pembelian'],
            "rating"            => $data_trx['rating'],
        ];

        $ret['status']  = "success";
        $ret['data']    = $data;

        return response()->json($ret);
    }

    function getPembayaran()
    {
        $data = Pembayaran::orderBy('nama_pembayaran')->get();

        $ret['status']  = "success";
        $ret['data']    = $data;

        return response()->json($ret);
    }

    function getProvinsi()
    {
        $ret['status']  = "success";
        $ret['data']    = Provinsi::all();

        return response()->json($ret);
    }

    function getKota(Request $request)
    {
        $ret['status']  = "success";
        $ret['data']    = Kota::where('id_provinsi', $request->id_provinsi)->orderBy('nama_kota')->get();;

        return response()->json($ret);
    }

    function getKurir()
    {
        $data = [
            ["kode" => "jne", "nama" => "JNE"],
            ["kode" => "pos", "nama" => "POS"],
            ["kode" => "tiki", "nama" => "TIKI"],
        ];
        
        $ret['status']  = "success";
        $ret['data']    = $data;

        return response()->json($ret);
    }

    function getLayananKurir(Request $request)
    {
        $response = Http::post($this->url_api."/cost", [
            'key'           => $this->api_key,
            'origin'        => 23,
            'destination'   => auth()->user()->city,
            'weight'        => 1000,
            'courier'       => $request->kurir
        ]);

        $ret['status']  = "success";
        $ret['data']    = $response['rajaongkir']['results'][0]['costs'];

        return response()->json($ret);
    }

    function hitungTotalTransaksi(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'list_produk'       => 'required|string',
            'qty_produk'        => 'required|string',
            'kurir'             => 'required|string',
            'layanan_kurir'     => 'required|string'
        ]);

        if($validator->fails())
        {
            $response = [
                'status'    => 'error',
                'message'   => $validator->errors()->first()
            ];

            return response()->json($response, 400);       
        }

        $parameter = [
            "id_customer"   => auth()->user()->id,
            "list_produk"   => $request->list_produk,
            "qty_produk"    => $request->qty_produk,
            "kurir"         => $request->kurir,
            "layanan_kurir" => $request->layanan_kurir
        ];

        $data = $this->classTransaksi->hitungTotal($parameter);

        $ret['status']  = "success";
        $ret['data']    = $data;

        return response()->json($ret);
    }

    function addTransaksi(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'list_produk'       => 'required|string',
            'qty_produk'        => 'required|string',
            'kurir'             => 'required|string',
            'layanan_kurir'     => 'required|string',
        ]);
        
        if($validator->fails())
        {
            $response = [
                'status'    => 'error',
                'message'   => $validator->errors()->first()
            ];

            return response()->json($response, 400);       
        }

        // CEK TRX YANG BELUM DIBAYAR
        $trx = Transaksi::where('id_customer', auth()->user()->id)->where('status', 0)->first();
        if($trx)
        {
            $response = [
                'status'    => 'error',
                'message'   => 'Anda memiliki transaksi yang belum dibayar, silahkan lakukan pembayaran terlebih dahulu'
            ];

            return response()->json($response, 400);       
        }

        $nomor_transaksi = $this->classTransaksi->generateNomorTransaksi();
        
        $arr_produk = explode(",", $request->list_produk);
        $arr_qty    = explode(",", $request->qty_produk);
        $i          = 0;
        foreach($arr_produk as $id_produk)
        {
            $data_produk = Produk::with('kategori')->find($id_produk);

            $list_produk[] = [
                "id"                => $id_produk,
                "name"              => $data_produk->nama_produk,
                "kategori"          => $data_produk->kategori->nama_kategori,
                "jenis_kategori"    => $data_produk->kategori->jenis,
                "price"             => (int)$data_produk->harga,
                "quantity"          => (int)$arr_qty[$i],
                "total"             => (int)$data_produk->harga * $arr_qty[$i]  
            ];

            $this->classProduk->updateStok($id_produk, -$arr_qty[$i]);

            $i++;
        }

        $parameter = [
            "id_customer"   => auth()->user()->id,
            "list_produk"   => $request->list_produk,
            "qty_produk"    => $request->qty_produk,
            "kurir"         => $request->kurir,
            "layanan_kurir" => $request->layanan_kurir
        ];

        $data_total         = $this->classTransaksi->hitungTotal($parameter);
        $customer           = auth()->user();
        $pembayaran_expired = date("Y-m-d H:i:s", strtotime("+1 day"));

        $data = array(
            'nomor_transaksi'   => $nomor_transaksi,
            'waktu_transaksi'   => date('Y-m-d H:i:s'),
            'id_customer'       => $customer->id,
            'telp'              => $customer->phone,
            'nama'              => $customer->name,
            'alamat'            => $customer->address,
            'list_produk'       => json_encode($list_produk),
            'sub_total'         => $data_total['subtotal'],
            'biaya_ongkir'      => $data_total['biaya_ongkir'],
            'diskon'            => 0,
            'total_bayar'       => $data_total['total'],
            'status'            => 0,
            'id_pembayaran'     => $request->pembayaran,
            'bukti_pembayaran'  => "",
            'kurir'             => $request->kurir,
            'layanan_kurir'     => $request->layanan_kurir,
            'nomor_resi'        => "",
            'tracking'          => "",
            'pembayaran_expired'=> $pembayaran_expired,
        );

        Transaksi::create($data); 

        $ret['status']  = "success";
        $ret['data']    = ["nomor_transaksi" => $nomor_transaksi, "pembayaran_expired" => $pembayaran_expired];

        return response()->json($ret);
    }

    function listTransaksi()
    {
        $list_trx = Transaksi::where('id_customer', auth()->user()->id)->orderBy('waktu_transaksi', 'desc')->get();

        $data = [];
        foreach($list_trx as $data_trx)
        {
            $data[] = [
                "nomor_transaksi"   => $data_trx->nomor_transaksi,
                "waktu_transaksi"   => $data_trx->waktu_transaksi,
                "status"            => $this->status_trx[$data_trx->status],
                "total_bayar"       => $data_trx->total_bayar,
                "pembayaran_expired"=> $data_trx->pembayaran_expired,
            ];
        }

        $ret['status']  = "success";
        $ret['data']    = $data;

        return response()->json($ret);
    }

    function detailTransaksi(Request $request)
    {
        $data = Transaksi::find($request->nomor_transaksi);
        
        $data['status']         = $this->status_trx[$data->status];
        $data['list_produk']    = json_decode($data->list_produk);
        $data['tracking']       = !empty($data->tracking)?array_reverse(json_decode($data->tracking)):[];
        
        $ret['status']  = "success";
        $ret['data']    = $data;

        return response()->json($ret);
    }

    function uploadPembayaranTransaksi(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nomor_transaksi'   => 'required|string',
            'pembayaran'        => 'required',
            'bukti_pembayaran'  => 'required'
        ]);
        
        if($validator->fails())
        {
            $response = [
                'status'    => 'error',
                'message'   => $validator->errors()->first()
            ];

            return response()->json($response, 400);       
        }
        
        $data_trx = Transaksi::find($request->nomor_transaksi);

        if($data_trx->status == 0)
        {
            $tracking   = !empty($data_trx->tracking)?json_decode($data_trx->tracking):[];
            $tracking[] = ["time" => date('Y-m-d H:i:s'), "text" => "Menunggu Pembayaran Diverifikasi"];

            $data = [
                'status'            => 6,
                'id_pembayaran'     => $request->pembayaran,
                'bukti_pembayaran'  => $request->bukti_pembayaran,
                'tracking'          => json_encode($tracking),
            ];

            Transaksi::find($request->nomor_transaksi)->update($data);
        }

        $ret['status']  = "success";
        $ret['data']    = $request->nomor_transaksi;

        return response()->json($ret);
    }

    function setSelesaiTransaksi(Request $request)
    {
        $data_trx = Transaksi::find($request->nomor_transaksi);

        if($data_trx->status == 3 || $data_trx->status == 4)
        {
            $tracking   = !empty($data_trx->tracking)?json_decode($data_trx->tracking):[];
            $tracking[] = ["time" => date('Y-m-d H:i:s'), "text" => "Transaksi selesai"];
    
            $data = [
                'status'        => 5,
                'nomor_resi'    => $request->nomorresi,
                'tracking'      => json_encode($tracking),
            ];
    
            Transaksi::find($request->nomor_transaksi)->update($data);
        }

        $ret['status']  = "success";
        $ret['data']    = $request->nomor_transaksi;

        return response()->json($ret);
    }

    function setBatalTransaksi(Request $request)
    {
        $data_trx = Transaksi::find($request->nomor_transaksi);

        if($data_trx->status != 2)
        {
            $arr_produk = json_decode($data_trx->list_produk);
            foreach($arr_produk as $produk)
            {
                $this->classProduk->updateStok($produk->id, $produk->quantity);
            }

            $tracking   = !empty($data_trx->tracking)?json_decode($data_trx->tracking):[];
            $tracking[] = ["time" => date('Y-m-d H:i:s'), "text" => "Transaksi dibatalkan"];

            $data = [
                'status'    => 2,
                'tracking'  => json_encode($tracking),
            ];
        
            Transaksi::find($data_trx->nomor_transaksi)->update($data);
        }

        $ret['status']  = "success";
        $ret['data']    = $request->nomor_transaksi;

        return response()->json($ret);
    }

    function setUlasanTransaksi(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nomor_transaksi'   => 'required|string',
            'rating'            => 'required',
            'ulasan'            => 'required'
        ]);
        
        if($validator->fails())
        {
            $response = [
                'status'    => 'error',
                'message'   => $validator->errors()->first()
            ];

            return response()->json($response, 400);       
        }

        $ulasan = Ulasan::find($request->nomor_transaksi);

        $data = [
            "nomor_transaksi"   => $request->nomor_transaksi,
            "nilai"             => $request->rating,
            "ulasan"            => $request->ulasan,
            "gambar"            => $request->gambar,
            "created_at"        => empty($ulasan)?date("Y-m-d H:i:s"):$ulasan->created_at,
        ];
        
        if(empty($ulasan))
        {
            Ulasan::create($data);
        }
        else
        {
            $ulasan->update($data);
        }

        $ret['status']  = "success";
        $ret['data']    = $request->nomor_transaksi;

        return response()->json($ret);
    }
}
