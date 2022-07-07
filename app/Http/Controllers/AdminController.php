<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use File;

use App\Models\Pembayaran;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Ulasan;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\User;

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

        $this->status_trx = ['Belum Dibayar', 'Lunas', 'Batal', 'Diproses', 'Dikirim', 'Selesai'];
    }

    function index()
    {
        $data['total_kategori']     = Kategori::all()->count();
        $data['total_produk']       = Produk::all()->count();
        $data['total_pelanggan']    = Customer::all()->count();
        $data['total_pengguna']     = User::all()->count();

        // $news = News::with("category", "user")->whereBetween('created_at', [date("Y-m-d")." 00:00:00", date("Y-m-d")." 23:59:59"])->get();

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

        return response()->json($data);
    }

    function getListProdukByKategori(Request $request)
    {
        $data = Produk::where('id_kategori', $request->id)->where('stok', '>', 0)->orderBy('nama_produk')->get();
        
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
        $data['filter']     = ($request->filter!='')?$request->filter:0;
        $data['status']     = ($request->status!='')?$request->status:"";
        $data['status_trx'] = $this->status_trx;
        $data['kategori']   = Kategori::orderBy('nama_kategori')->get();
        $data['pembayaran'] = Pembayaran::orderBy('nama_pembayaran')->get();

        // $news       = News::with("category", "user")->whereBetween('created_at', [$date_start." 00:00:00", $date_end." 23:59:59"]);
        // $news       = ($filter)?$news->where('id_category', $filter)->get():$news->get();
        // $category   = Category::orderBy('category_name')->get();

        return view('admin/pages/transaksi', $data);
    }

    function showDataTransaksi($id="")
    {
        $data['proses'] = ($id!="")?"Detail":"Add";
        $data['trx']    = ($id!="")?News::with("category", "user")->find($id):null; 

        return view('admin/pages/transaksi_detail', $data);
    }

    function ulasan()
    {
        $data = Ulasan::with('customer', 'produk')->get();
      
        return view('admin/pages/ulasan', ['ulasan' => $data]);
    }

    function getDataUlasan(Request $request)
    {
        $data = Ulasan::find($request->id);

        return response()->json($data);
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
        $data = Customer::where('phone', $request->telp)->first();
        
        if(!$data)
        {
            $data = CustomerAddress::where('phone', $request->telp)->first();
        }

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
}
