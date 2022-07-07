<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Auth;
use Validator;
use File;
use App\Models\Customer;
use App\Models\Kategori;
use App\Models\Produk;

class APIController extends Controller
{
    function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }

    function getQuotes()
    {
        $data = Quotes::all()->random(1);

        $ret['status']  = "success";
        $ret['data']    = $data;

        return response()->json($ret);
    }

    function getDataActivity(Request $request)
    {
        $classActivity = new Activity();

        $date           = !empty($request->date)?$request->date:date("Y-m-d");
        $id_customer    = Auth::user()->id;

        $trackers   = $classActivity->getTracker($date, $id_customer)->get();
        $tracker    = [];
        foreach($trackers as $row)
        {
            $tracker[$row->id_activity] = 1;
        }

        $activities = Activity::orderBy('order')->get();
        foreach($activities as $row)
        {
            $data[] = [
                'id'            => $row->id,
                'activity_name' => $row->activity_name,
                'icon'          => URL::asset('images/activity').'/'.$row->icon,
                'checked'       => isset($tracker[$row->id])?1:0
            ];
        }

        $ret['status']  = "success";
        $ret['data']    = $data;

        return response()->json($ret);
    }

    function setCheckActivity(Request $request)
    {
        $classActivity  = new Activity();

        $date           = !empty($request->date)?$request->date:date("Y-m-d");
        $id_customer    = Auth::user()->id;

        $update = $classActivity->checklistTracker($date, $id_customer, $request->id_activity, $request->checked);

        $ret['status']  = "success";
        
        return response()->json($ret);
    }

    function getDataNews(Request $request)
    {
        $news = News::with("category", "user")->select('news.*', 'category_name')->leftJoin('category', 'news.id_category', '=', 'category.id_category');
        $news = ($request->exists('id_category') && $request->id_category!="")?$news->where('news.id_category', $request->id_category):$news;
        $news = ($request->exists('keyword') && $request->keyword)?$news->where('title', 'LIKE', '%'.$request->keyword.'%')->orWhere('category_name', 'LIKE', '%'.$request->keyword.'%'):$news;
        $news = $news->orderBy('created_at', 'desc')->get();

        $data = [];
        foreach($news as $row)
        {
            $data[] = [
                "id_news"       => $row->id_news,
                "title"         => $row->title,
                'category_name' => $row->category_name,
                'thumbnail'     => URL::asset('images/news').'/'.$row->thumbnail,
            ];
        }

        $ret['status']  = "success";
        $ret['data']    = $data;

        return response()->json($ret);
    }

    function getRecommendedNews(Request $request)
    {
        $news = News::with("category", "user")
                ->select('news.*', 'category_name')
                ->leftJoin('category', 'news.id_category', '=', 'category.id_category')
                ->where('is_recommended', 1)
                ->orderBy('created_at', 'desc')
                ->offset(0)
                ->limit($request->limit)
                ->get();

        $data = [];
        foreach($news as $row)
        {
            $data[] = [
                "id_news"       => $row->id_news,
                "title"         => $row->title,
                'category_name' => $row->category_name,
                'thumbnail'     => URL::asset('images/news').'/'.$row->thumbnail,
            ];
        }

        $ret['status']  = "success";
        $ret['data']    = $data;

        return response()->json($ret);
    }

    function getDetailNews(Request $request)
    {
        $data = News::with("category", "user")
        ->select('news.*', 'category_name')
        ->leftJoin('category', 'news.id_category', '=', 'category.id_category')
        ->where('id_news', $request->id_news)
        ->first();
        
        if(!empty($data))
        {
            $data->thumbnail = URL::asset('images/news').'/'.$data->thumbnail;

            $ret['status']  = "success";
            $ret['data']    = $data;
        }
        else
        {
            $ret['status']  = "error";
            $ret['message'] = "News Not Found!";
        }

        return response()->json($ret);
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

    function getProduk(Request $request)
    {
        $produk = Produk::with("kategori")->select('produk.*', 'nama_kategori', 'kategori.jenis AS jenis_produk')->leftJoin('kategori', 'produk.id_kategori', '=', 'kategori.id')->where('stok', '>', 0);
        $produk = ($request->exists('id_kategori') && $request->id_kategori!="")?$produk->where('produk.id_kategori', $request->id_kategori):$produk;
        $produk = ($request->exists('keyword') && $request->keyword)?$produk->where('nama_produk', 'LIKE', '%'.$request->keyword.'%')->orWhere('nama_kategori', 'LIKE', '%'.$request->keyword.'%'):$produk;
        $produk = $produk->orderBy('nama_produk', 'desc')->get();

        $data = [];
        foreach($produk as $row)
        { 
            $data[] = [
                "id"                => $row->id,
                "nama_produk"       => $row->nama_produk,
                "jenis_produk"      => (!$row->jenis_produk)?"Barang":"Jasa",
                "kategori"          => $row->nama_kategori,
                "kondisi"           => (!$row->kondisi)?"Baru":"Bekas",
                "jenis_pembelian"   => (!$row->jenis)?"Langsung":"Pre-Order",
                "berat"             => $row->berat . " gr",
                "warna"             => $row->warna,
                "ukuran"            => empty($row->ukuran)?[]:explode(",", $row->ukuran),
                "harga"             => (int)$row->harga,
                "deskripsi"         => $row->deskripsi,
                "gambar"            => URL::asset('images/produk').'/'.$row->gambar,
                "stok"              => $row->stok
            ];
        }

        $ret['status']  = "success";
        $ret['data']    = $data;

        return response()->json($ret);
    }
}
