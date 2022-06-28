<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use File;

use App\Models\Kategori;
use App\Models\Pembayaran;
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

    // function news(Request $request)
    // {
    //     $date_start = ($request->date_start!='')?$request->date_start:date('Y-m-d');
    //     $date_end   = ($request->date_end!='')?$request->date_end:date('Y-m-d');
    //     $filter     = ($request->filter!='')?$request->filter:0;

    //     $news       = News::with("category", "user")->whereBetween('created_at', [$date_start." 00:00:00", $date_end." 23:59:59"]);
    //     $news       = ($filter)?$news->where('id_category', $filter)->get():$news->get();
    //     $category   = Category::orderBy('category_name')->get();

    //     return view('admin/pages/news', [
    //         'news'          => $news,
    //         'category'      => $category,
    //         'date_start'    => $date_start,
    //         'date_end'      => $date_end,
    //         'filter'        => $filter,
    //     ]);
    // }

    // function showDataNews($id="")
    // {
    //     $data       = ($id!="")?News::with("category", "user")->find($id):null;
    //     $category   = Category::orderBy('category_name')->get();

    //     return view('admin/pages/news_detail', [
    //         'proses'    => ($id!="")?"Detail":"Add",
    //         'data'      => $data,
    //         'category'  => $category,
    //     ]);
    // }

    // function saveNews(Request $request)
    // {
    //     $classNews = new News();

    //     $id = ($request->id != "")?$request->id:$classNews->getNextId();
        
    //     if ($request->hasFile('thumbnail'))
    //     {
    //         $destinationPath    = "images/news";
    //         $file               = $request->thumbnail;
    //         $fileName           = $id.".".$file->getClientOriginalExtension();
    //         $pathfile           = $destinationPath.'/'.$fileName;

    //         if($request->old_thumbnail != "")
    //         {
    //             File::delete($destinationPath."/".$request->old_thumbnail);
    //         }

    //         $file->move($destinationPath, $fileName); 

    //         $thumbnail = $fileName;
    //     }
    //     else
    //     {
    //         $thumbnail = $request->old_thumbnail;
    //     }

    //     $id     = array('id_news' => $request->id);
    //     $data   = array(
    //         'thumbnail'     => $thumbnail,
    //         'title'         => $request->title,
    //         'id_category'   => $request->category,
    //         'content'       => $request->content,
    //         'id_user'       => Auth::user()->id,
    //         'created_at'    => date('Y-m-d H:i:s'),
    //     );

    //     News::updateOrCreate($id, $data);        

    //     $process = ($request->id == "")?"created":"updated";

    //     $this->swal("News", $process);

    //     return redirect('admin/news');
    // }

    // function deleteNews(Request $request)
    // {
    //     $data = News::find($request->id);

    //     File::delete("images/news/".$data->thumbnail);

    //     $delete = $data->delete();

    //     return response()->json($delete);
    // }

    // function setRecommendedNews(Request $request)
    // {
    //     $update = News::find($request->id)->update(['is_recommended' => $request->recommended]);

    //     return response()->json($update);
    // }

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
