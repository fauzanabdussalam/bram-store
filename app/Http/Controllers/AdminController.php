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
use App\Models\Customer;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        date_default_timezone_set('Asia/Jakarta');
    }

    function index()
    {
        // $total_quotes   = Quotes::all()->count();
        // $total_activity = Activity::all()->count();
        // $total_category = Category::all()->count();
        // $total_news     = News::all()->count();
        // $total_customer = Customer::all()->count();
        // $total_user     = User::all()->count();

        // $news = News::with("category", "user")->whereBetween('created_at', [date("Y-m-d")." 00:00:00", date("Y-m-d")." 23:59:59"])->get();

        return view('admin/pages/dashboard', [
            // "total_category"    => $total_category,
            // "total_news"        => $total_news,
            // "total_customer"    => $total_customer,
            // "total_user"        => $total_user,
            // "news"              => $news,
        ]);
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

    // function activity()
    // {
    //     return view('admin/pages/activity');
    // }

    // function getDataActivity(Request $request)
    // {
    //     $data = Activity::find($request->id);

    //     return response()->json($data);
    // }
    
    // function saveActivity(Request $request)
    // {
    //     $classActivity = new Activity();

    //     $id = ($request->id != "")?$request->id:$classActivity->getNextId();

    //     if ($request->hasFile('icon'))
    //     {
    //         $destinationPath    = "images/activity";
    //         $file               = $request->icon;
    //         $fileName           = $id.".".$file->getClientOriginalExtension();
    //         $pathfile           = $destinationPath.'/'.$fileName;

    //         if($request->old_icon != "")
    //         {
    //             File::delete($destinationPath."/".$request->old_icon);
    //         }

    //         $file->move($destinationPath, $fileName); 

    //         $icon = $fileName;
    //     }
    //     else
    //     {
    //         $icon = $request->old_icon;
    //     }

    //     $id     = array('id' => $request->id);
    //     $data   = array(
    //         'activity_name' => $request->name,
    //         'icon'          => $icon
    //     );

    //     Activity::updateOrCreate($id, $data);        
        
    //     $process = ($request->id == "")?"created":"updated";
    //     $this->swal("activity", $process);

    //     return redirect('admin/activity');
    // }

    // function deleteActivity(Request $request)
    // {
    //     $data = Activity::find($request->id);

    //     File::delete("images/activity/".$data->icon);

    //     $delete = $data->delete();

    //     return response()->json($delete);
    // }

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
        $classKategori = new Kategori();

        $id = ($request->id != "")?$request->id:$classKategori->getNextId();

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
        $kategori   = Kategori::orderBy('nama_kategori')->get();
        $produk     = Produk::with("kategori");
        $produk     = ($filter)?$produk->where('id_kategori', $filter)->get():$produk->get();

        return view('admin/pages/produk', [
            'filter'    => $filter,
            'kategori'  => $kategori,
            'produk'    => $produk,
        ]);
    }

    function getDataProduk(Request $request)
    {
        $data = Produk::find($request->id);

        return response()->json($data);
    }
    
    function saveProduk(Request $request)
    {
        $classProduk = new Produk();

        $id = ($request->id != "")?$request->id:$classProduk->getNextId();
        
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

    function customer()
    {
        $classCustomer  = new Customer();
        $customer       = Customer::all();

        $data = [];
        if(count($customer) > 0)
        {
            foreach($customer as $row)
            {
                $row->jumlah_transaksi = $classCustomer->getJumlahTransaksi($row->id);
                
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
        $classUser = new User();
        $data_user = $classUser->getDetailDataByUsername($request->username)->first();
        
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
