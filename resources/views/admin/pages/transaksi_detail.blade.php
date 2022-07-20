@extends('admin.layouts.layout')

@section('content')
  <div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title"><i class="fa fa-cart-plus"></i> Transaksi</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{ Route('dashboard') }}">Admin</a></li>
                        <li><a href="{{ Route('transaksi') }}">Transaksi</a></li>
                        <li class="active">Detail</li>
                    </ol>
                </div>
            </div>

            <div class="panel panel-default">          
                <div class="panel-heading">
                    <h3 class="panel-title">Detail</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form">        
                        <div class="col-sm-6">                                    
                            <div class="col-md-12">
                                <label class="col-md-4 control-label">Nomor Transaksi : </label>
                                <div class="col-md-8 form-control-static">
                                    {{ $nomor_transaksi }}
                                </div>
                                <input type="hidden" id="nomor_transaksi" name="nomor_transaksi" value="{{ $nomor_transaksi }}">
                            </div>
                            <div class="col-md-12">
                                <label class="col-md-4 control-label">Waktu Transaksi : </label>
                                <div class="col-md-8 form-control-static">
                                    {{ date("d-m-Y H:i:s", strtotime($waktu_transaksi)) }}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="col-md-4 control-label">Telp Pelanggan : </label>
                                <div class="col-md-8 form-control-static">
                                    {{ $telp }}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="col-md-4 control-label">Nama Pelanggan : </label>
                                <div class="col-md-8 form-control-static">
                                    {{ $nama }}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="col-md-4 control-label">Alamat Pelanggan : </label>
                                <div class="col-md-8 form-control-static">
                                    {{ $alamat }}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="col-md-4 control-label">Rating Pelanggan : </label>
                                <div class="col-md-8 form-control-static">
                                    {{ !empty($ulasan)?$ulasan->nilai . "/5":"-" }}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="col-md-4 control-label">Ulasan Pelanggan : </label>
                                <div class="col-md-8 form-control-static">
                                {{ !empty($ulasan)?$ulasan->ulasan:"-" }}
                                <br>
                                @php 
                                    if(!empty($ulasan->gambar))
                                    {
                                        echo "<a href='data:image/png;base64, $ulasan->gambar' class='image-popup' title='Ulasan'><img src='data:image/png;base64, $ulasan->gambar' class='thumb-img' alt='Ulasan' style='max-height: 100px'></a>";
                                    }
                                @endphp
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">    
                            <div class="col-md-12">
                                <label class="col-md-4 control-label">Pembayaran : </label>
                                <div class="col-md-8 form-control-static">
                                    {{ $pembayaran }}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="col-md-4 control-label">Bukti Pembayaran : </label>
                                <div class="col-md-8 form-control-static">
                                    @php 
                                        if(empty($bukti_pembayaran))
                                        {
                                            echo "-";
                                        }
                                        else
                                        {
                                            echo "<a href='data:image/png;base64, $bukti_pembayaran' class='image-popup' title='Bukti Pembayaran'><img src='data:image/png;base64, $bukti_pembayaran' class='thumb-img' alt='Bukti Pembayaran' style='max-height: 100px'></a>";
                                        }
                                    @endphp
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="col-md-4 control-label">Status : </label>
                                <div class="col-md-8 form-control-static">
                                    {{ $status_trx[$status] }}
                                    <br>
                                    @php
                                        $list_status = "";
                                        foreach($arr_status[$status] as $data_status)
                                        {
                                            $list_status .= "<button class='btn btn-icon btn-xs btn-$data_status[class]' onclick='setStatus($data_status[status]);return false;'>$data_status[label]</button> | ";
                                        }
                                        echo rtrim($list_status, " |");
                                    @endphp
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="col-md-4 control-label">Kurir : </label>
                                <div class="col-md-8 form-control-static">
                                    {{ !empty($kurir)?strtoupper($kurir):"-" }}
                                    <input type="hidden" id="kurir" name="kurir" value="{{ $kurir }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="col-md-4 control-label">Layanan Kurir : </label>
                                <div class="col-md-8 form-control-static">
                                    {{ !empty($layanan_kurir)?strtoupper($layanan_kurir):"-" }}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="col-md-4 control-label">Nomor Resi : </label>
                                <div class="col-md-8 form-control-static">
                                    {{ !empty($nomor_resi)?$nomor_resi:"-" }}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="col-md-4 control-label">Tracking : </label>
                                <div class="col-md-8 form-control-static" style="height: 150px; overflow: auto;background-color: #f2f2f2;">
                                    <table>
                                        @foreach($tracking as $value)
                                            <tr>
                                                <td>
                                                    <b>{{ $value->text }}</b>
                                                    <br>
                                                    {{ date("d-m-Y H:i:s", strtotime($value->time)) }}
                                                    <br><br>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row"> 
                            <div class="col-md-12">
                                <hr>
                                <h4 style="text-align: center;">Daftar Produk</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <td width="25%">Kategori</td>
                                            <td width="30%">Produk</td>
                                            <td width="15%">Harga</td>
                                            <td width="10%">Qty</td>
                                            <td width="15%">Total</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($list_produk as $produk)
                                        <tr>
                                            <td>{{ $produk->kategori }}</td>
                                            <td>{{ $produk->name }}</td>
                                            <td>{{ "Rp " . number_format($produk->price, 0, ",", ".") }}</td>
                                            <td>{{ (!$produk->jenis_kategori)?$produk->quantity:"-" }}</td>
                                            <td>{{ "Rp " . number_format($produk->total, 0, ",", ".") }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">Sub Total</td>
                                            <td>{{ "Rp " . number_format($sub_total, 0, ",", ".") }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">Biaya Ongkir</td>
                                            <td>{{ "Rp " . number_format($biaya_ongkir, 0, ",", ".") }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">Diskon</td>
                                            <td>{{ "- Rp " . number_format($diskon, 0, ",", ".") }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">Total Bayar</td>
                                            <td>{{ "Rp " . number_format($total_bayar, 0, ",", ".") }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </form>
                </div> <!-- end Panel -->
            </div>
        </div>
    </div> 
</div>  

<div id="show_resi" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h4 class="modal-title">SET STATUS DIKIRIM</h4> 
            </div> 
            
            <form method="post" enctype="multipart/form-data" id="finput">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group"> 
                                <label class="control-label">Nomor Resi</label>
                                <input class="form-control" id="nomor_resi" name="nomor_resi" placeholder="Nomor Resi">
                            </div> 
                        </div> 
                    </div>
                </div>
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close <i class="fa fa-close"></i></button> 
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="setStatus(4, false)">Save <i class="fa fa-save"></i></button> 
                </div> 
            </form>
        </div>
    </div>
</div><!-- /.modal -->

<script>
    function setStatus(status, show_dlg_resi=true)
    {
        if(status == 4 && $("#kurir").val() != "")
        {
            if(show_dlg_resi)
            {
                $('#show_resi').modal('show');
                return false;   
            }
            else if(!show_dlg_resi && $("#nomor_resi").val() == "")
            {
                alert("Nomor Resi harus diisi");
                return false;
            }
        }

        if(!confirm("Apakah anda yakin?")) 
        {
            return false;
        }

        $.ajax(
        {
            url: "{{ Route('transaksi.status') }}",
            type: 'POST',
            data: 
            {
                id: $("#nomor_transaksi").val(),
                status: status,
                nomorresi: $("#nomor_resi").val(),
                _token: '{{csrf_token()}}'
            },
            success: function (response)
            {
                swal({
                    title: "Success!",
                    text: "Status berhasil diubah",
                    type: "success"
                }, function() {
                    location.reload();
                });
            }
        });
    }
</script>
@endsection