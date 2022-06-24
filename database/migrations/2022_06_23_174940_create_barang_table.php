<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kategori');
            $table->foreign('id_kategori')->references('id')->on('kategori')->cascadeOnDelete();
            $table->string('nama_barang', 100);
            $table->string('kondisi', 10)->default(0);
            $table->string('jenis', 10)->default(0);
            $table->integer('berat')->default(0);
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
        Schema::dropIfExists('barang');
    }
}
