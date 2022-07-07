@extends('admin.layouts.layout')

@section('content')
  <div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title"><i class="md md-shopping-cart"></i> Transaksi</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{ Route('dashboard') }}">Admin</a></li>
                        <li class="active">Transaksi</li>
                    </ol>
                </div>
            </div>

            <div class="panel">          
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="m-b-30">
                                <td class="actions">
                                    <!-- <a href="{{ url('admin/transaksi/add') }}" class="btn btn-primary">Tambah <i class="fa fa-plus"></i></a> -->
                                    
                                    <button class="btn btn-primary" onclick="add()">Tambah <i class="fa fa-plus"></i></button>
                                </td>
                            </div>
                        </div>
                    </div>
                         
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{ Route('transaksi') }}" class="form-inline" role="form" style="float: right;">
                                <div class="form-group">
                                    <label>Date Start</label>
                                    <input type="date" class="form-control" id="date_start" name="date_start" value="{{ $date_start }}">
                                </div>
                                
                                <div class="form-group">
                                    <label>Date End</label>
                                    <input type="date" class="form-control" id="date_end" name="date_end" value="{{ $date_end }}">
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" id="status" name="status" >
                                        <option value="">Semua</option>
                                        @foreach($status_trx as $key => $value)
                                        <option value="{{ $key }}" {{ ($status!='' && $key==$status)?'selected':'' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-inverse waves-effect waves-light m-l-10"><i class="fa fa-search"> Search</i></button>
                            </form>
                        </div>
                    </div>

                    <hr>
                    
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
                                <th></th>
                            </tr>
                        </thead>                  
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!-- end: page -->
            </div> <!-- end Panel -->
        </div>
    </div>
</div>

<div id="edit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h4 class="modal-title">Transaksi</h4> 
            </div> 
            
            <form action="{{ Route('transaksi.save') }}" method="post" enctype="multipart/form-data" id="finput">
                {{ csrf_field() }}
                <div class="modal-body">
                    <input type="hidden" id="id" name="id"> 
                    <div class="row form-group"> 
                        <div class="col-md-6">
                            <label>Telp Pelanggan</label>
                            <input type="text" class="form-control" id="telp" name="telp" placeholder="Telp" value="{{ !isset($data)?'':$data->title }}" onblur="checkDataPelanggan(this.value)" required>
                        </div>

                        <div class="col-md-6">
                            <label>Nama Pelanggan</label>
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama" value="{{ !isset($data)?'':$data->title }}" required>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-12">
                            <label>Alamat Pelanggan</label>
                            <textarea rows="3" class="form-control" id="alamat" name="alamat" placeholder="Alamat" required></textarea>
                        </div>
                    </div>
                    <div class="row form-group"> 
                        <div class="col-md-6">
                            <label>Kategori</label>
                            <select class="form-control" id="kategori" name="kategori" onchange="getListProduk();" required>
                                <option value="">-Pilih Kategori-</option>
                                @foreach($kategori as $data)
                                    <option value="{{ $data->id }}">{{ $data->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Produk</label>
                            <select class="form-control" id="produk" name="produk" onchange="getDataProduk()" required>
                                <option value="">-Pilih Produk-</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group"> 
                        <div class="col-md-4">
                            <label>Harga Produk</label>
                            <span id="harga" class="form-control" disabled>0</span>
                        </div>

                        <div class="col-md-4">
                            <label>Diskon</label>
                            <input type="number" class="form-control" id="diskon" name="diskon" value="0" min="0" step="1000" onchange="hitungTotal();" required>
                        </div>

                        <div class="col-md-4">
                            <label>Total Bayar</label>
                            <span id="total" class="form-control" disabled>0</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <label>Pembayaran</label>
                            <select class="form-control" id="pembayaran" name="pembayaran">
                                <option value="">Tunai</option>
                                @foreach($pembayaran as $data)
                                    <option value="{{ $data->id }}">{{ $data->nama_pembayaran }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="0">Belum Dibayar</option>
                                <option value="1">Lunas</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close <i class="fa fa-close"></i></button> 
                    <button type="submit" id="submit" class="btn btn-primary waves-effect waves-light">Save <i class="fa fa-save"></i></button> 
                </div> 
            </form>
        </div>
    </div>
</div><!-- /.modal -->

  
<script>
    function add()
    {
        $('#edit').modal('show');
        $('#finput')[0].reset();
    }

    function edit(id)
    {
        $('#edit').modal('show');

        $.ajax(
        {
            url:"{{ Route('pembayaran.data') }}",
            type: "POST",
            data: {
                id: id,
                _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(data)
            {
                $("#id").val(id);
                $("#nama").val(data.nama_pembayaran);
                $("#nomorrekening").val(data.nomor_rekening);
                $("#an").val(data.atas_nama);
            }
        });
    }

    function deleteData(id)
    {
        if(!confirm("Are you sure want to delete this data?")) 
        {
            return false;
        }

        $.ajax(
        {
            url: "{{ Route('transaksi.delete') }}",
            type: 'POST',
            data: 
            {
                id: id,
                _token: '{{csrf_token()}}'
            },
            success: function (response)
            {
                swal({
                    title: "Success!",
                    text: "Data deleted successfully!",
                    type: "success"
                }, function() {
                    location.reload();
                });
            }
        });
        
        return false;
    }

    function checkDataPelanggan(telp)
    {
        $.ajax(
        {
            url:"{{ Route('customer.data2') }}",
            type: "POST",
            data: {
                telp: telp,
                _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(value)
            {
                $("#nama").val(value.name);
                $("#alamat").val(value.address);
            }
        });
    }

    function getListProduk(id_produk)
    {
        $("#produk").html('');
        $("#harga").html('0');
        $("#diskon").val('0');
        $("#total").html('0');

        $.ajax(
        {
            url:"{{ Route('produk.list') }}",
            type: "POST",
            data: {
                id: $("#kategori").val(),
                _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(result)
            {
                $("#produk").append('<option value="">-Pilih Produk-</option>');
                $.each(result,function(key,value)
                {
                    selected = (id_produk != "" && id_produk == value.id)?"selected":"";

                    $("#produk").append('<option value="'+value.id+'" '+selected+'>'+value.nama_produk+'</option>');
                });
            }
        });
    }

    function getDataProduk()
    {
        $.ajax(
        {
            url:"{{ Route('produk.data') }}",
            type: "POST",
            data: {
                id: $("#produk").val(),
                _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(value)
            {
                diskon = ($("#diskon").val() > value.harga)?value.harga:$("#diskon").val();

                $("input").attr({"max" : value.harga});
                $("#harga").html(value.harga);
                $("#diskon").val(diskon);
                $("#total").html(value.harga-diskon);
            }
        });
    }

    function hitungTotal()
    {
        harga   = parseInt($("#harga").html());
        diskon  = ($("#diskon").val() > harga)?harga:$("#diskon").val();

        $("#diskon").val(diskon);
        $("#total").html(harga-diskon);
    }
</script>
@endsection