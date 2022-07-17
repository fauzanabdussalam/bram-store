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
                                    <a href="{{ url('admin/transaksi/add') }}" class="btn btn-primary">Tambah <i class="fa fa-plus"></i></a>
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
                                <td>
                                    <a href="{{ url('admin/transaksi/detail/'.$data->nomor_transaksi) }}" class="btn btn-icon btn-sm btn-warning"><i class="fa fa-indent"></i> </a>
                                    <button class="btn btn-icon btn-sm btn-danger" onclick="deleteData('{{ $data->nomor_transaksi }}')"> <i class="fa fa-trash"></i> </button>
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

    function deleteData(nomor_trx)
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
                id: nomor_trx,
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
</script>
@endsection