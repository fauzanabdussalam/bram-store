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
            $table->timestamp('waktu_transaksi');
            $table->unsignedBigInteger('id_customer')->nullable();
            $table->string('telp', 15);
            $table->string('nama', 100);
            $table->text('alamat')->nullable();
            $table->text('list_produk')->nullable();
            $table->double('sub_total')->default(0);
            $table->double('biaya_ongkir')->default(0);
            $table->double('diskon')->default(0);
            $table->double('total_bayar')->default(0);
            $table->unsignedTinyInteger('status')->default(0);
            $table->unsignedBigInteger('id_pembayaran')->nullable();
            $table->text('bukti_pembayaran')->nullable();
            $table->string('kurir', 10)->nullable();
            $table->string('layanan_kurir', 50)->nullable();
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
