@extends('admin.layouts.layout')

@section('content')
  <div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title"><i class="md md-star"></i> Ulasan</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{ Route('dashboard') }}">Admin</a></li>
                        <li class="active">Ulasan</li>
                    </ol>
                </div>
            </div>

            <div class="panel">          
                <div class="panel-body">
                    <table class="table table-bordered table-striped" id="datatable">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>No. Transaksi</th>
                                <th>Produk</th>
                                <th>Nama Customer</th>
                                <th>Telp Customer</th>
                                <th>Rating</th>
                                <th>Ulasan</th>
                                <th></th>
                            </tr>
                        </thead>                  
                        <tbody>
                            @foreach($ulasan as $data)
                            <tr class="gradeX">
                                <td>{{ date("d-m-Y H:i:s", strtotime( $data->created_at)) }}</td>
                                <td>{{ $data->nomor_transaksi }}</td>
                                <td>
                                    @php
                                        $arr_produk  = json_decode($data->transaksi->list_produk);
                                        $list_produk = "";
                                        foreach($arr_produk as $produk)
                                        {
                                            $qty = (!$produk->jenis_kategori)?"($produk->quantity pcs)":"";
                                            $list_produk .= "- $produk->name $qty <br>";
                                        }
                                        echo $list_produk;
                                    @endphp
                                </td>
                                <td>{{ $data->transaksi->nama }}</td>
                                <td>{{ $data->transaksi->telp }}</td>
                                <td>{{ $data->nilai . "/5" }}</td>
                                <td>{{ $data->ulasan }}</td>
                                <td>    
                                    <a href="{{ url('admin/transaksi/detail/'.$data->nomor_transaksi) }}" class="btn btn-icon btn-sm btn-warning"><i class="fa fa-indent"></i> </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- end: page -->
            </div> <!-- end Panel -->
        </div>
    </div>
</div> 
@endsection