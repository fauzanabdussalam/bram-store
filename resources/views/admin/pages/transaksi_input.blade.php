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
                        <li class="active">Tambah</li>
                    </ol>
                </div>
            </div>

            <div class="panel col-md-12">
                <form action="{{ Route('transaksi.save') }}" method="post" enctype="multipart/form-data" id="finput">
                    <div class="panel-body">
                        {{ csrf_field() }}
                        <input type="hidden" id="id" name="id" value="{{ !isset($data)?'':$data->nomor_transaksi }}">
                        <div style="float: left;width: 45%;">
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <label>Telp Pelanggan</label>
                                    <input type="text" class="form-control" id="telp" name="telp" placeholder="Telp" onblur="checkDataPelanggan(this.value)" required>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <label>Nama Pelanggan</label>
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama" required>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <label>Alamat Pelanggan</label>
                                    <select class="select2" id="provinsi" nama="provinsi" onchange="getListKota()">
                                        <option value="">-Pilih Provinsi-</option>
                                        @foreach($provinsi as $data)
                                        <option value="{{ $data->id }}">{{ $data->nama_provinsi }}</option>
                                        @endforeach
                                    </select>
                                    <br><br>
                                </div>
                                <div class="col-md-12">
                                    <select class="select2" id="kota" nama="kota">
                                        <option value="">-Pilih Kota-</option>
                                    </select>
                                    <br><br>
                                </div>
                                <div class="col-md-12">
                                    <textarea rows="3" class="form-control" id="alamat" name="alamat" placeholder="Alamat Lengkap" required></textarea>
                                </div>
                            </div>
                        </div>

                        <div style="float: right;width: 45%;">
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <label>Pembayaran</label>
                                    <select class="form-control" id="pembayaran" name="pembayaran">
                                        <option value="">Tunai</option>
                                        @foreach($pembayaran as $data)
                                        <option value="{{ $data->id }}">{{ $data->nama_pembayaran }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <label>Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="0">Belum Dibayar</option>
                                        <option value="1">Lunas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <label>Kirim Dengan Kurir</label>
                                    <select class="form-control" id="iskirim" name="iskirim" onchange="showInputKurir();">
                                        <option value="0">Tidak</option>
                                        <option value="1">Ya</option>
                                    </select>
                                </div>
                            </div>
                            <div id="input_kurir" style="display: none;">
                                <div class="row form-group">
                                    <div class="col-md-12">
                                        <label>Kurir</label>
                                        <select class="form-control" id="kurir" name="kurir" onchange="getListLayanan();">
                                            <option value="">-Pilih Kurir-</option>
                                            @foreach($kurir as $data)
                                            <option value="{{ $data['kode'] }}">{{ $data['nama'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-12">
                                        <label>Layanan Pengiriman</label>
                                        <select class="form-control" id="layanan" name="layanan" onchange="getOngkir();">
                                            <option value="">-Pilih Layanan-</option>
                                            @foreach($kurir as $data)
                                            <option value="{{ $data['kode'] }}">{{ $data['nama'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="is_produk_terisi" name="is_produk_terisi" value="0">
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
                                            <td width="5%"></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select class="form-control" id="kategori" name="kategori" onchange="getListProduk();"">
                                                    <option value="">-Pilih Kategori-</option>
                                                    @foreach($kategori as $data)
                                                        <option value="{{ $data->id }}">{{ $data->nama_kategori }}</option>
                                                    @endforeach
                                                </select> 
                                            </td>
                                            <td>
                                                <select  class="form-control" id="produk" name="produk" onchange="getHarga();">
                                                    <option value="">-Pilih Produk-</option>
                                                </select> 
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" id="harga" name="harga" value="0" disabled>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" id="qty" name="qty" value="1" min="1" onchange="getHarga();">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" id="total" name="total" value="0" disabled>
                                            </td>
                                            <td align="center">
                                                <button type="button" class="btn btn-icon btn-sm btn-primary" onclick="addCart()"><i class="fa fa-plus"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody id="list_produk">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">Sub Total</td>
                                            <td align="right">
                                                <span id="sub_total">Rp 0</span>
                                                <input type="hidden" id="subtotal" name="subtotal" value="0">
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">Biaya Ongkir</td>
                                            <td align="right"><input type="number" class="form-control" id="ongkir" name="ongkir" value="0" min="0" step="100" onchange="hitungTotal();" style="text-align: right;"></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">Diskon</td>
                                            <td align="right"><input type="number" class="form-control" id="diskon" name="diskon" value="0" min="0" step="100" onchange="hitungTotal();" style="text-align: right;"></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4">Total Bayar</td>
                                            <td align="right"><span id="total_bayar">Rp 0</span></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer" style="text-align: center"> 
                        <button type="submit" id="submit" class="btn btn-primary waves-effect waves-light">Simpan <i class="fa fa-save"></i></button> 
                    </div> 
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    window.onload = function() 
    {
        clearCart();
        showInputKurir();
    };

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
                id_provinsi = "";

                if(value.city)
                {
                    id_provinsi = value.kota.id_provinsi;
                }

                $("#nama").val(value.name);
                $("#alamat").val(value.address);
                $("#provinsi").select2("val", id_provinsi);
                getListKota(value.city);
            }
        });
    }

    function getListKota(id_kota="")
    {
        $("#kota").html('');

        $.ajax(
        {
            url:"{{ Route('kota.list') }}",
            type: "POST",
            data: {
                id: $("#provinsi").val(),
                _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(result)
            {
                $("#kota").append('<option value="">-Pilih Kota-</option>');
                $.each(result,function(key,value)
                {
                    selected = (id_kota != "" && id_kota == value.id)?"selected":"";
                    $("#kota").append('<option value="'+value.id+'" '+selected+'>'+value.nama_kota+'</option>');
                });

                $("#kota").select2("val", id_kota);
            }
        });
    }

    function showInputKurir()
    {
        if($("#iskirim").val() == 1)
        {
            $("#input_kurir").show();
        }
        else
        {
            $("#input_kurir").hide();
        }
    }

    function getListLayanan()
    {
        $("#layanan").html('');
        $(".ongkir_layanan").remove();

        $.ajax(
        {
            url:"{{ Route('kurir.layanan') }}",
            type: "POST",
            data: {
                kurir: $("#kurir").val(),
                kota_tujuan: $("#kota").val(),
                _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(result)
            {
                $("#layanan").append('<option value="">-Pilih Layanan-</option>');
                $.each(result,function(key,value)
                {
                    $("#layanan").append('<option value="'+value.kode+'">'+value.nama+'</option>');
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'ongkir_'+value.kode,
                        name: 'ongkir_'+value.kode,
                        value: value.ongkir,
                        class: 'ongkir_layanan'
                    }).appendTo('form');
                });
            }
        });
    }

    function getOngkir()
    {
        $("#ongkir").val($("#ongkir_"+$("#layanan").val()).val());
        hitungTotal();
    }

    function hitungTotal()
    {
        subtotal    = parseInt($("#subtotal").val());
        ongkir      = parseInt($("#ongkir").val());
        diskon      = parseInt($("#diskon").val());
        diskon      = (diskon > subtotal+ongkir)?subtotal+ongkir:diskon;
        total       = subtotal + ongkir - diskon;
        total       = "Rp " + number_format(total, 0, ",", ".");

        $("#diskon").val(diskon);
        $("#total_bayar").text(total);
    }

    function getListProduk(id_produk)
    {
        $("#produk").html('');
        $("#harga").html('0');
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
                    
                    qty_disabled = (value.kategori.jenis==1)?true:false;
                    $("#qty").prop('disabled', qty_disabled);
                });
            }
        });
    }
    
    function getHarga()
    {
        if(!$("#produk").val())
        {
            $("#harga").val(0);
            $("#total").val(0);

            return;
        }

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
                total = parseInt(value.harga) * parseInt($("#qty").val());

                $("#harga").val(value.harga);
                $("#total").val(total);
            }
        });
    }

    function clearCart()
    {
        $.ajax(
        {
            url:"{{ Route('cart.clear') }}",
            type: "POST",
            data: 
            {
                _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(result)
            {
            }
        });
    }

    function getListCart()
    {
        $.ajax(
        {
            url:"{{ Route('cart') }}",
            type: "POST",
            data: 
            {
                _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(result)
            {
                $("#list_produk").text('');
                $('#is_produk_terisi').val('0');

                $.each(result.cart,function(key,value)
                {
                    row =   '<tr>' +
                                '<td>'+value.kategori+'</td>' +
                                '<td>'+value.name+'</td>' +
                                '<td align="right">' + value.price + '</td>' +
                                '<td align="center">' + value.quantity + '</td>' +
                                '<td align="right">' + value.total + '</td>' +
                                '<td align="center"><button type="button" class="btn btn-icon btn-sm btn-danger" onclick="removeCart(\'' + value.id + '\')"><i class="fa fa-remove"></i></button></td>' +
                            '</tr>';

                    $("#list_produk").append(row);
                    $('#is_produk_terisi').val('1');
                });

                $("#sub_total").text(result.total_bayar);
                $("#subtotal").val(result.total_bayar_int);
                hitungTotal();
            }
        });
    }

    function addCart()
    {
        if(!$("#produk").val())
        {
            alert("Produk belum dipilih!");
            return;
        }

        qty_produk = ($("#qty").val() > 0)?$("#qty").val():1;

        $.ajax(
        {
            url:"{{ Route('cart.add') }}",
            type: "POST",
            data: 
            {
                id: $("#produk").val(),
                qty: qty_produk,
                _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(result)
            {
                $("#kategori").val('');
                $("#produk").val('');
                $("#harga").val('0');
                $("#qty").val('1');
                $("#total").val('0');
                getListCart();
            }
        });
    }

    function removeCart(id)
    {
        $.ajax(
        {
            url:"{{ Route('cart.remove') }}",
            type: "POST",
            data: 
            {
                id: id,
                _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(result)
            {
                getListCart();
            }
        });
    }

    function number_format (number, decimals, dec_point, thousands_sep) 
    {
        // Strip all characters but numerical ones.
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }
</script>
@endsection