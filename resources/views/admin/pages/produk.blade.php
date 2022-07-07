@extends('admin.layouts.layout')

@section('content')
  <div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title"><i class="md md-dns"></i> Produk</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{ Route('dashboard') }}">Admin</a></li>
                        <li class="active">Produk</li>
                    </ol>
                </div>
            </div>

            <div class="panel">          
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="m-b-30">
                                <button class="btn btn-primary" onclick="add()">Tambah <i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{ Route('produk') }}" class="form-inline" role="form" style="float: right;">
                                <div class="form-group">
                                    <label>Kategori</label>
                                    <select class="form-control" id="filter" name="filter" style="width: 200px;">
                                        <option value="0" {{ ($filter==0)?"selected":"" }}>Semua</option>
                                        @foreach($kategori as $data)
                                        <option value="{{ $data->id }}" {{ ($data->id==$filter)?"selected":"" }}>{{ $data->nama_kategori }}</option>
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
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>                  
                        <tbody>
                            @foreach($produk as $data)
                            
                            <tr class="gradeX" style="{{ ($data->stok>0)?"":"background-color: red;color: white;" }}">
                                <td>{{ $data->nama_produk }}</td>
                                <td>{{ $data->kategori->nama_kategori }}</td>
                                <td>{{ "Rp " . number_format($data->harga, 0, ",", ".") }}</td>
                                <td>{{ ($data->kategori->jenis)?"-":$data->stok }}</td>
                                <td>{{ ($data->stok>0)?"Aktif":"Non-Aktif" }}</td>
                                <td class="actions">
                                    <button class="btn btn-icon btn-sm btn-success" onclick="edit({{ $data->id }})"> <i class="fa fa-edit"></i> </button> 
                                    <button class="btn btn-icon btn-sm btn-danger" onclick="deleteData({{ $data->id }})"> <i class="fa fa-trash"></i> </button>
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
  
  <div id="edit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                <h4 class="modal-title">Produk</h4> 
            </div> 
            
            <form action="{{ Route('produk.save') }}" method="post" enctype="multipart/form-data" id="finput">
                {{ csrf_field() }}
                <div class="modal-body">
                    <input type="hidden" id="id" name="id"> 
                    <div class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group"> 
                                <label class="control-label">Nama Produk</label>
                                <input class="form-control" id="nama" name="nama" placeholder="Nama Produk" required>
                            </div> 
                        </div> 
                    </div>
                    <div class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group"> 
                                <label class="control-label">Kategori</label>
                                <select class="form-control" id="kategori" name="kategori" onchange="showInputBarang();">
                                    @foreach($kategori as $data)
                                    <option value="{{ $data->id }}">{{ $data->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div> 
                        </div> 
                    </div>

                    <div id="input_brg" class="row"> 
                        <div class="col-md-4"> 
                            <div class="form-group"> 
                                <label class="control-label">Kondisi</label>
                                <select class="form-control" id="kondisi" name="kondisi">
                                    <option value="0">Baru</option>
                                    <option value="1">Bekas</option>
                                </select>
                            </div> 
                        </div> 
                        <div class="col-md-4"> 
                            <div class="form-group"> 
                                <label class="control-label">Jenis Pembelian</label>
                                <select class="form-control" id="jenis" name="jenis">
                                    <option value="0">Langsung</option>
                                    <option value="1">Pre-Order</option>
                                </select>
                            </div> 
                        </div> 
                        <div class="col-md-4"> 
                            <div class="form-group"> 
                                <label class="control-label">Berat (gram)</label>
                                <input class="form-control" type="number" id="berat" name="berat" value="0" min="0">
                            </div> 
                        </div> 
                        <div class="row"> 
                            <div class="col-md-6"> 
                                <div class="form-group"> 
                                    <label class="control-label">Warna</label>
                                    <input class="form-control" id="warna" name="warna" placeholder="Warna">
                                </div> 
                            </div> 
                            <div class="col-md-6"> 
                                <div class="form-group"> 
                                    <label class="control-label">Ukuran (Pakaian)</label><br>
                                    <select class="select2" name="ukuran[]" id="ukuran" multiple data-placeholder="Pilih Ukuran...">
                                        <option value="XS">XS</option>
                                        <option value="S">S</option>
                                        <option value="M">M</option>
                                        <option value="L">L</option>
                                        <option value="XL">XL</option>
                                        <option value="XXL">XXL</option>
                                    </select>
                                </div> 
                            </div> 
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group"> 
                                <label class="control-label">Harga</label>
                                <input class="form-control" type="number" id="harga" name="harga" value="0" required>
                            </div> 
                        </div> 
                    </div>
                    <div class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group"> 
                                <label class="control-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required></textarea>
                            </div> 
                        </div> 
                    </div>

                    <div id="input_stok" class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group"> 
                                <label class="control-label">Stok</label>
                                <input class="form-control" type="number" id="stok" name="stok" value="0" min="0">
                            </div> 
                        </div> 
                    </div>
                    <div id="input_aktif" class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group"> 
                                <label class="control-label">Aktivasi</label>
                                <select class="form-control" id="aktif" name="aktif">
                                    <option value="1">Aktif</option>
                                    <option value="0">Non-Aktif</option>
                                </select>
                            </div>
                        </div> 
                    </div>
                    <div class="row"> 
                        <div class="col-md-12"> 
                            <div class="form-group"> 
                                <label class="control-label">Gambar</label> 
                                <input type="file" class="form-control" id="icon" name="icon" onchange="showIcon(this);" accept="image/*">
                                <input type="hidden" name="old_icon" id="old_icon">
                            </div> 
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-md-12"> 
                            <div class="panel panel-default" style="width: 250px;">
                                <img id="icon_src" style="width:100%"/>
                            </div>
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
        $("#icon").prop('required',true);
        $("#icon_src").attr("src", "");
        showInputBarang();

        $('.select2').select2();
    }

    function edit(id)
    {
        $('#edit').modal('show');
        $("#icon").prop('required',false);

        $.ajax(
        {
            url:"{{ Route('produk.data') }}",
            type: "POST",
            data: {
                id: id,
                _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(data)
            {
                is_aktif = (data.stok > 0)?1:0;

                $("#id").val(id);
                $("#nama").val(data.nama_produk);
                $("#kategori").val(data.id_kategori);
                $("#kondisi").val(data.kondisi);
                $("#jenis").val(data.jenis);
                $("#berat").val(data.berat);
                $("#harga").val(data.harga);
                $("#deskripsi").val(data.deskripsi);
                $("#stok").val(data.stok);
                $("#aktif").val(is_aktif);
                $("#old_icon").val(data.gambar);
                $("#icon_src").attr("src", "{{ URL::asset('images/produk') }}" + "/" + data.gambar);

                showInputBarang();
            }
        });
    }
    
    function showIcon(input) 
    {
        if (input.files && input.files[0]) 
        {
            var reader = new FileReader();
            reader.onload = function (e) {
            $('#icon_src')
                .attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function deleteData(id)
    {
        if(!confirm("Are you sure want to delete this data?")) 
        {
            return false;
        }

        $.ajax(
        {
            url: "{{ Route('produk.delete') }}",
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

    function showInputBarang()
    {
        $.ajax(
        {
            url:"{{ Route('kategori.data') }}",
            type: "POST",
            data: {
                id: $("#kategori").val(),
                _token: '{{csrf_token()}}'
            },
            dataType : 'json',
            success: function(data)
            {
                if(data.jenis == 0)
                {
                    $("#input_brg").css("display", "block");
                    $("#input_stok").css("display", "block");
                    $("#input_aktif").css("display", "none");
                }
                else
                {
                    $("#input_brg").css("display", "none");
                    $("#input_stok").css("display", "none");
                    $("#input_aktif").css("display", "block");
                }
            }
        });
    }
</script>
@endsection