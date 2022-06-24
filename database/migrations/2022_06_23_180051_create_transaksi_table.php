<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->string('nomor_transaksi', 20);
            $table->primary('nomor_transaksi');
            $table->timestamp('waktu_pembelian');
            $table->unsignedBigInteger('id_customer');
            $table->foreign('id_customer')->references('id')->on('customer')->onDelete('NO ACTION');
            $table->unsignedBigInteger('id_customer_address');
            $table->foreign('id_customer_address')->references('id')->on('customer_address')->onDelete('NO ACTION');
            $table->unsignedBigInteger('id_kategori');
            $table->foreign('id_kategori')->references('id')->on('kategori')->onDelete('NO ACTION');
            $table->unsignedBigInteger('id_produk');
            $table->foreign('id_produk')->references('id')->on('produk')->onDelete('NO ACTION');
            $table->double('harga_produk')->default(0);
            $table->double('biaya_ongkir')->default(0);
            $table->double('diskon')->default(0);
            $table->double('total_bayar')->default(0);
            $table->unsignedTinyInteger('status')->default(0);
            $table->unsignedBigInteger('id_pembayaran');
            $table->foreign('id_pembayaran')->references('id')->on('pembayaran')->onDelete('NO ACTION');
            $table->text('bukti_pembayaran')->nullable();
            $table->string('kurir', 10)->nullable();
            $table->string('nomor_resi', 20)->nullable();
            $table->text('tracking')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
}
