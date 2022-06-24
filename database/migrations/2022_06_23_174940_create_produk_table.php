<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kategori');
            $table->foreign('id_kategori')->references('id')->on('kategori')->cascadeOnDelete();
            $table->string('nama_produk', 100);
            $table->string('kondisi', 10)->default(0);
            $table->string('jenis', 10)->default(0);
            $table->integer('berat')->default(0);
            $table->double('harga_barang')->default(0);
            $table->text('deskripsi')->nullable();
            $table->text('gambar')->nullable();
            $table->integer('stok')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produk');
    }
}
