@extends('admin.layouts.layout')

@section('content')
<div class="content-page">
  <!-- Start content -->
    <div class="content">
        <div class="container">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title"><i class="md md-dashboard"></i> Dashboard</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{ Route('dashboard') }}">Dashboard</a></li>
                    </ol>
                </div>
            </div>

            <!-- Start Widget -->
            <div class="row">
                <div class="col-md-6 col-sm-6 col-lg-3">
                    <div class="mini-stat clearfix bx-shadow">
                        <span class="mini-stat-icon bg-info"><i class="md md-view-list"></i></span>
                        <div class="mini-stat-info text-right text-muted">
                            <span class="counter">{{ $total_kategori }}</span>
                            Total Kategori
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-lg-3">
                    <div class="mini-stat clearfix bx-shadow">
                        <span class="mini-stat-icon bg-purple"><i class="md md-dns"></i></span>
                        <div class="mini-stat-info text-right text-muted">
                            <span class="counter">{{ $total_produk }}</span>
                            Total Produk
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-sm-6 col-lg-3">
                    <div class="mini-stat clearfix bx-shadow">
                        <span class="mini-stat-icon bg-success"><i class="fa fa-user"></i></span>
                        <div class="mini-stat-info text-right text-muted">
                            <span class="counter">{{ $total_pelanggan }}</span>
                            Total Pelanggan
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-lg-3">
                    <div class="mini-stat clearfix bx-shadow">
                        <span class="mini-stat-icon bg-primary"><i class="fa fa-users"></i></span>
                        <div class="mini-stat-info text-right text-muted">
                            <span class="counter">{{ $total_pengguna }}</span>
                            Total Pengguna
                        </div>
                    </div>
                </div>
            </div> 
            <!-- End row-->

            <div class="panel panel-default">    
                <div class="panel-heading">
                    <h3 class="panel-title">Transaksi Hari Ini</h3>
                </div>      
                <div class="panel-body">
                    <table class="table table-bordered table-striped" id="datatable">
                        <thead>
                            <tr>
                                <th>Nomor <br> Transaksi</th>
                                <th>Waktu <br> Transaksi</th>
                                <th>Nama <br> Pelanggan</th>
                                <th>Telp <br> Pelanggan</th>
                                <th>Produk</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                            </tr>
                        </thead>                  
                        <tbody>
                            @foreach($transaksi as $data)
                            <tr class="gradeX">
                                <td>{{ $data->nomor_transaksi }}</td>
                                <td>{{ date("d-m-Y H:i", strtotime($data->waktu_transaksi)) }}</td>
                                <td>{{ $data->nama }}</td>
                                <td>{{ $data->telp }}</td>
                                <td>
                                    @php
                                        $arr_produk  = json_decode($data->list_produk);
                                        $list_produk = "";
                                        foreach($arr_produk as $produk)
                                        {
                                            $qty = (!$produk->jenis_kategori)?"($produk->quantity pcs)":"";
                                            $list_produk .= "- $produk->name $qty <br>";
                                        }
                                        echo $list_produk;
                                    @endphp
                                </td>
                                <td>{{ "Rp " . number_format($data->total_bayar, 0, ',', ".") }}</td>
                                <td>{{ $status_trx[$data->status] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <hr>
                    <div style="float: right">
                        <a href="{{ Route('transaksi') }}" class="btn btn-icon btn-sm btn-primary">LIHAT SEMUA TRANSAKSI <i class="fa fa-arrow-right"></i></i></a>
                    </div>
                </div>
                <!-- end: page -->
            </div> <!-- end Panel -->
        </div>
    </div>
</div>
@endsection