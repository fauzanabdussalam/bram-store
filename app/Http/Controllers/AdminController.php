<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use File;

use App\Models\Pembayaran;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Ulasan;
use App\Models\Customer;
use App\Models\User;
use App\Models\Provinsi;
use App\Models\Kota;
use App\Models\Transaksi;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        date_default_timezone_set('Asia/Jakarta');

        $this->classKategori    = new Kategori();
        $this->classProduk      = new Produk();
        $this->classCustomer    = new Customer();
        $this->classUser        = new User();
        $this->classTransaksi   = new Transaksi();

        $this->url_api      = 'https://api.rajaongkir.com/starter/';
        $this->api_key      = '19ddbb5173b42a658d9e8b5f48a2b2b4';
        $this->status_trx   = [
            '0' => 'Belum Dibayar', 
            '6' => 'Menunggu Verifikasi', 
            '1' => 'Lunas', 
            '2' => 'Batal', 
            '3' => 'Diproses', 
            '4' => 'Dikirim', 
            '5' => 'Selesai'
        ];

        $this->kurir        = [
            ["kode" => "jne", "nama" => "JNE"],
            // ["kode" => "pos", "nama" => "POS"],
            ["kode" => "tiki", "nama" => "TIKI"],
        ];

        $this->classTransaksi->batalkanTransaksiExpired();
    }

    function index()
    {
        $data['total_kategori']     = Kategori::all()->count();
        $data['total_produk']       = Produk::all()->count();
        $data['total_pelanggan']    = Customer::all()->count();
        $data['total_pengguna']     = User::all()->count();        
        $data['transaksi']          = Transaksi::whereBetween('waktu_transaksi', [date("Y-m-d")." 00:00:00", date("Y-m-d")." 23:59:59"])->get();
        $data['status_trx']         = $this->status_trx;

        return view('admin/pages/dashboard', $data);
    }

    function swal($pages, $process)
    {
        $message = "$pages $process successfully!";
        Session::flash('alert_swal','swal("Success!", "'.$message.'", "success");');
    }

    function pembayaran()
    {   
        $data = Pembayaran::all();

        return view('admin/pages/pembayaran', ['pembayaran' => $data]);
    }

    function getDataPembayaran(Request $request)
    {
        $data = Pembayaran::find($request->id);

        return response()->json($data);
    }
    
    function savePembayaran(Request $request)
    {
        $id     = array('id' => $request->id);
        $data   = array(
            'nama_pembayaran'   => $request->nama,
            'nomor_rekening'    => $request->nomorrekening,
            'atas_nama'         => $request->an,
        );

        Pembayaran::updateOrCreate($id, $data);        
        
        $process = ($request->id == "")?"created":"updated";
        $this->swal("pembayaran", $process);

        return redirect('admin/pembayaran');
    }

    function deletePembayaran(Request $request)
    {
        $data = Pembayaran::find($request->id)->delete();

        return response()->json($data);
    }

    function kategori()
    {   
        $data = Kategori::all();

        return view('admin/pages/kategori', ['kategori' => $data]);
    }

    function getDataKategori(Request $request)
    {
        $data = Kategori::find($request->id);

        return response()->json($data);
    }
    
    function saveKategori(Request $request)
    {
        $id = ($request->id != "")?$request->id:$this->classKategori->getNextId();

        if ($request->hasFile('icon'))
        {
            $destinationPath    = "images/kategori";
            $file               = $request->icon;
            $fileName           = $id.".".$file->getClientOriginalExtension();
            $pathfile           = $destinationPath.'/'.$fileName;

            if($request->old_icon != "")
            {
                File::delete($destinationPath."/".$request->old_icon);
            }

            $file->move($destinationPath, $fileName); 

            $icon = $fileName;
        }
        else
        {
            $icon = $request->old_icon;
        }

        $id     = array('id'   => $request->id);
        $data   = array(
            'nama_kategori' => $request->name,
            'jenis'         => $request->jenis,
            'icon'          => $icon
        );

        Kategori::updateOrCreate($id, $data);        
        
        $process = ($request->id == "")?"created":"updated";
        $this->swal("category", $process);

        return redirect('admin/kategori');
    }

    function deleteKategori(Request $request)
    {
        $data = Kategori::find($request->id);

        File::delete("images/kategori/".$data->icon);

        $delete = $data->delete();
        
        return response()->json($delete);
    }
    
    function produk(Request $request)
    {   
        $filter     = ($request->filter!='')?$request->filter:0;
        $produk     = Produk::with("kategori");
        $produk     = ($filter)?$produk->where('id_kategori', $filter)->get():$produk->get();

        $data['filter']     = $filter;
        $data['kategori']   = Kategori::orderBy('nama_kategori')->get();
        $data['produk']     = $produk;

        return view('admin/pages/produk', $data);
    }

    function getDataProduk(Request $request)
    {
        $data = Produk::find($request->id);
        $data->arr_ukuran = explode(",", $data->ukuran);

        return response()->json($data);
    }

    function getListProdukByKategori(Request $request)
    {
        $data = Produk::with('kategori')->where('id_kategori', $request->id)->where('stok', '>', 0)->orderBy('nama_produk')->get();
        
        return response()->json($data);
    }
    
    function saveProduk(Request $request)
    {
        $id     = ($request->id != "")?$request->id:$this->classProduk->getNextId();
        $ukuran = ($request->ukuran != "")?implode(",", $request->ukuran):"";
        
        if ($request->hasFile('icon'))
        {
            $destinationPath    = "images/produk";
            $file               = $request->icon;
            $fileName           = $id.".".$file->getClientOriginalExtension();
            $pathfile           = $destinationPath.'/'.$fileName;

            if($request->old_icon != "")
            {
                File::delete($destinationPath."/".$request->old_icon);
            }

            $file->move($destinationPath, $fileName); 

            $gambar = $fileName;
        }
        else
        {
            $gambar = $request->old_icon;
        }

        $data_kategori = Kategori::find($request->kategori);

        $id     = array('id' => $request->id);
        $data   = array(
            'id_kategori'   => $request->kategori,
            'nama_produk'   => $request->nama,
            'kondisi'       => ($data_kategori->jenis)?0:$request->kondisi,
            'jenis'         => ($data_kategori->jenis)?0:$request->jenis,
            'berat'         => ($data_kategori->jenis)?0:$request->berat,
            'warna'         => ($data_kategori->jenis)?"":$request->warna,
            'ukuran'        => ($data_kategori->jenis)?0:$ukuran,
            'harga'         => $request->harga,
            'deskripsi'     => $request->deskripsi,
            'gambar'        => $gambar,
            'stok'          => ($data_kategori->jenis)?$request->aktif:$request->stok,
        );

        Produk::updateOrCreate($id, $data);        

        $process = ($request->id == "")?"created":"updated";

        $this->swal("produk", $process);

        return redirect('admin/produk');
    }

    function deleteProduk(Request $request)
    {
        $data = Produk::find($request->id)->delete();

        return response()->json($data);
    }

    function transaksi(Request $request)
    {
        $data['date_start'] = ($request->date_start!='')?$request->date_start:date('Y-m-d');
        $data['date_end']   = ($request->date_end!='')?$request->date_end:date('Y-m-d');
        $data['status']     = ($request->status!='')?$request->status:"";
        $data['status_trx'] = $this->status_trx;
        
        $trx    = Transaksi::whereBetween('waktu_transaksi', [$data['date_start']." 00:00:00", $data['date_end']." 23:59:59"]);
        $trx    = ($request->status!='')?$trx->where('status', $request->status)->get():$trx->get();
        
        $data['transaksi']  = $trx;

        return view('admin/pages/transaksi', $data);
    }

    function inputTransaksi($id="")
    {
        $data['provinsi']   = Provinsi::orderBy('nama_provinsi')->get();
        $data['kategori']   = Kategori::orderBy('nama_kategori')->get();
        $data['pembayaran'] = Pembayaran::orderBy('nama_pembayaran')->get();
        $data['status_trx'] = $this->status_trx;
        $data['kurir']      = $this->kurir;

        return view('admin/pages/transaksi_input', $data);
    }

    function saveTransaksi(Request $request)
    {
        $nomor_transaksi = $this->classTransaksi->generateNomorTransaksi();

        $data_customer  = Customer::where('phone', $request->telp)->first();
        $id_customer    = ($data_customer)?$data_customer->id:null;

        $cart = \Cart::getContent();

        $list_produk = [];
        foreach($cart as $produk)
        {
            $list_produk[] = [
                "id"                => $produk->id,
                "name"              => $produk->name,
                "kategori"          => $produk->associatedModel->kategori->nama_kategori,
                "jenis_kategori"    => $produk->associatedModel->kategori->jenis,
                "price"             => (int)$produk->associatedModel->harga,
                "quantity"          => (int)$produk->quantity,
                "total"             => (int)$produk->associatedModel->harga * $produk->quantity       
            ];

            $this->classProduk->updateStok($produk->id, -$produk->quantity);
        }

        $sub_total = \Cart::getTotal();

        $tracking           = [];
        $pembayaran_expired = null;
        if($request->status == 1)
        {
            $tracking[] = ["time" => date('Y-m-d H:i:s'), "text" => "Pembayaran sudah diverifikasi"];
        }
        else
        {
            $pembayaran_expired = date("Y-m-d H:i:s", strtotime("+1 day"));
        }

        $data = array(
            'nomor_transaksi'   => $nomor_transaksi,
            'waktu_transaksi'   => date('Y-m-d H:i:s'),
            'id_customer'       => $id_customer,
            'telp'              => $request->telp,
            'nama'              => $request->nama,
            'alamat'            => $request->alamat,
            'list_produk'       => json_encode($list_produk),
            'sub_total'         => $sub_total,
            'biaya_ongkir'      => $request->ongkir,
            'diskon'            => $request->diskon,
            'total_bayar'       => $sub_total + $request->ongkir - $request->diskon,
            'status'            => $request->status,
            'id_pembayaran'     => $request->pembayaran,
            'bukti_pembayaran'  => "",
            'kurir'             => $request->kurir,
            'nomor_resi'        => "",
            'tracking'          => json_encode($tracking),
            'pembayaran_expired'=> $pembayaran_expired
        );

        Transaksi::create($data); 
        
        $this->swal("transaksi", "created");
        
        return redirect('admin/transaksi');
    }

    function detailTransaksi($nomor_transaksi)
    {
        $data = Transaksi::find($nomor_transaksi);

        $data_pembayaran        = Pembayaran::find($data->id_pembayaran);
        $data['pembayaran']     = empty($data->id_pembayaran)?"Tunai":$data_pembayaran->nama_pembayaran;
        $data['list_produk']    = json_decode($data->list_produk);
        $data['ulasan']         = Ulasan::find($nomor_transaksi);
        $data['status_trx']     = $this->status_trx;
        $data['tracking']       = array_reverse(json_decode($data->tracking));

        $arr_status_dikirim = ["status" => 4, "label" => "SET DIKIRIM", "class" => "info"];
        $arr_status_selesai = ["status" => 5, "label" => "SET SELESAI", "class" => "success"];
        $arr_status_3       = !empty($data->kurir)?[$arr_status_dikirim, $arr_status_selesai]:[$arr_status_selesai];

        $data['arr_status']     = [
            "0" => [
                ["status" => 1, "label" => "SET LUNAS", "class" => "success"],
                ["status" => 2, "label" => "SET BATAL", "class" => "danger"],
            ],
            "6" => [
                ["status" => 1, "label" => "VERIFIKASI PEMBAYARAN", "class" => "success"],
                ["status" => 2, "label" => "SET BATAL", "class" => "danger"],
            ],
            "1" => [
                ["status" => 3, "label" => "SET DIPROSES", "class" => "info"],
                $arr_status_selesai,
            ],
            "2" => [],
            "3" => $arr_status_3,
            "4" => [
                $arr_status_selesai,
            ],
            "5" => [],
        ];
        
        return view('admin/pages/transaksi_detail', $data);
    }

    function setStatusTransaksi(Request $request)
    {
        $data_trx = Transaksi::find($request->id);

        $arr_text_status = [
            "1" => "Pembayaran sudah diverifikasi",
            "2" => "Transaksi dibatalkan",
            "3" => "Pesanan sedang diproses",
            "4" => "Pesanan telah dikirim",
            "5" => "Transaksi selesai",
        ];

        $tracking   = !empty($data_trx->tracking)?json_decode($data_trx->tracking):[];
        $tracking[] = ["time" => date('Y-m-d H:i:s'), "text" => $arr_text_status[$request->status]];

        $data = [
            'status'        => $request->status,
            'nomor_resi'    => $request->nomorresi,
            'tracking'      => json_encode($tracking),
        ];

        if($request->status == 2)
        {
            // restore stok produk
            $arr_produk = json_decode($data_trx->list_produk);
            foreach($arr_produk as $produk)
            {
                $this->classProduk->updateStok($produk->id, $produk->quantity);
            }
        }

        Transaksi::find($request->id)->update($data);
    }
    
    function deleteTransaksi(Request $request)
    {
        $data = Transaksi::find($request->id);

        // restore stok produk
        $arr_produk = json_decode($data->list_produk);
        foreach($arr_produk as $produk)
        {
            $this->classProduk->updateStok($produk->id, $produk->quantity);
        }
        
        $delete = $data->delete();

        return response()->json($delete);
    }

    function ulasan()
    {
        $data = Ulasan::with('transaksi')->get();
      
        return view('admin/pages/ulasan', ['ulasan' => $data]);
    }

    function customer()
    {
        $customer = Customer::all();

        $data = [];
        if(count($customer) > 0)
        {
            foreach($customer as $row)
            {
                $row->jumlah_transaksi = $this->classCustomer->getJumlahTransaksi($row->id);
                
                $data[] = $row;
            }
        }
      
        return view('admin/pages/customer', ['customer' => $data]);
    }

    function getDataCustomer(Request $request)
    {
        $data = Customer::find($request->id);

        return response()->json($data);
    }

    function getDataCustomerByTelp(Request $request)
    {
        $data = Customer::with('kota')->where('phone', $request->telp)->first();
        
        return response()->json($data);
    }

    function users()
    {
        $user = User::all();
      
        return view('admin/pages/users', ['user' => $user]);
    }

    function getDataUsers(Request $request)
    {    
        $data = User::find($request->id);

        return response()->json($data);
    }

    function saveUsers(Request $request)
    {
        $data_user = $this->classUser->getDetailDataByUsername($request->username)->first();
        
        if(!empty($data_user->id) && ($data_user->id != $request->id))
        {
            $ret['status']  = "ERROR";
            $ret['message'] = "Username has been used!";
        }
        else
        {   
            $id                 = array('id' => $request->id);
            $data['username']   = $request->username;
            $data['name']       = $request->name;
            
            if($request->password != "")
            {
                $data['password'] = Hash::make($request->password);
            }

            User::updateOrCreate($id, $data);        
    
            $ret['status'] = "OK";
        }

        return response()->json($ret);
    }

    function deleteUsers(Request $request)
    {
        $delete  = User::find($request->id)->delete();

        return response()->json($delete);
    }

    function changePasswordUsers(Request $request)
    {
        if(!Hash::check($request->old_password, Auth::user()->password))
        {
            $ret['status']  = "ERROR";
            $ret['message'] = "Old Password Wrong!";
        }
        else
        {   
            $id                 = array('id' => Auth::user()->id);
            $data['username']   = Auth::user()->username;
            $data['name']       = Auth::user()->name;
            $data['password']   = Hash::make($request->password);

            User::updateOrCreate($id, $data);        
    
            $ret['status'] = "OK";
        }

        return response()->json($ret);
    }

    function getKotaByProvinsi(Request $request)
    {
        $data = Kota::with('provinsi')->where('id_provinsi', $request->id)->orderBy('nama_kota')->get();
        
        return response()->json($data);
    }
    
    function getLayananKurir(Request $request)
    {
        $kota_asal      = 23; //KOTA BANDUNG
        $kota_tujuan    = !empty($request->kota_tujuan)?$request->kota_tujuan:152; //JAKPUS

        $response = Http::post($this->url_api."/cost", [
            'key'           => $this->api_key,
            'origin'        => $kota_asal,
            'destination'   => $kota_tujuan,
            'weight'        => 1000,
            'courier'       => $request->kurir
        ]);

        $data = [];
        foreach($response['rajaongkir']['results'][0]['costs'] as $value)
        {
            $data[] = [
                "kode"      => $value['service'],
                "nama"      => $value['description'] . " (" . $value['service'] . ")",
                "ongkir"    => $value['cost'][0]['value']
            ];
        }

        return response()->json($data);
    }
}
