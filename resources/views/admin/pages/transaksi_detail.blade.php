@extends('admin.layouts.layout')

@section('content')
  <div class="content-page">
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title"><i class="md md-shopping-cart"></i> Transaksi</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{ Route('dashboard') }}">Admin</a></li>
                        <li><a href="{{ Route('transaksi') }}">Transaksi</a></li>
                        <li class="active">{{ $proses }}</li>
                    </ol>
                </div>
            </div>

            <div class="panel col-md-12">
                <div class="panel-body">
                    <form action="{{ Route('transaksi.save') }}" method="post" enctype="multipart/form-data" id="finput">
                        {{ csrf_field() }}
                        <input type="hidden" id="id" name="id" value="{{ !isset($data)?'':$data->nomor_transaksi }}">

                        <div class="row form-group">
                            <div class="col-md-6">
                                <label>Telp Pelanggan</label>
                                <input type="text" class="form-control" id="telp" name="telp" placeholder="Telp" value="{{ !isset($data)?'':$data->title }}" required>
                            </div>

                            <div class="col-md-6">
                                <label>Nama Pelanggan</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama" value="{{ !isset($data)?'':$data->title }}" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-12">
                                <label>Alamat Pelanggan</label>
                                <textarea rows="3" class="form-control" id="alamat" name="alamat" placeholder="Alamat"></textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label>Kategori</label>
                                <select class="form-control" id="kategori" name="kategori">
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Produk</label>
                                <select class="form-control" id="produk" name="produk">
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label>Harga Produk</label>
                                <input type="text" class="form-control" id="telp" name="telp" placeholder="Telp" value="{{ !isset($data)?'':$data->title }}" required>
                            </div>

                            <div class="col-md-4">
                                <label>Diskon</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama" value="{{ !isset($data)?'':$data->title }}" required>
                            </div>

                            <div class="col-md-4">
                                <label>Total Bayar</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama" value="{{ !isset($data)?'':$data->title }}" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label>Pembayaran</label>
                                <select class="form-control" id="kategori" name="kategori">
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Status</label>
                                <select class="form-control" id="produk" name="produk">
                                </select>
                            </div>
                        </div>

                        <!-- <div class="form-group">
                            <label>Content</label>
                            <textarea class="wysihtml5 form-control" rows="22" id="content" name="content" required>{{ !isset($data)?'':$data->content }}</textarea>
                        </div> -->

                        <center>
                            <button type="submit" id="submit" class="btn btn-primary">Simpan <i class="fa fa-save"></i></button> 
                        </center>
                    </form>
                </div>
            </div>
        </div>e
    </div>
</div>
@endsection