<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin'], function () 
{    
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    
    Route::group(['prefix' => 'pembayaran'], function () 
    {
        Route::get('/', [AdminController::class, 'pembayaran'])->name('pembayaran');
        Route::post('data', [AdminController::class, 'getDataPembayaran'])->name('pembayaran.data');
        Route::post('save', [AdminController::class, 'savePembayaran'])->name('pembayaran.save');
        Route::post('delete', [AdminController::class, 'deletePembayaran'])->name('pembayaran.delete');
    });

    Route::group(['prefix' => 'kategori'], function () 
    {
        Route::get('/', [AdminController::class, 'kategori'])->name('kategori');
        Route::post('data', [AdminController::class, 'getDataKategori'])->name('kategori.data');
        Route::post('save', [AdminController::class, 'saveKategori'])->name('kategori.save');
        Route::post('delete', [AdminController::class, 'deleteKategori'])->name('kategori.delete');
    });
    
    Route::group(['prefix' => 'produk'], function () 
    {
        Route::get('/', [AdminController::class, 'produk'])->name('produk');
        Route::post('data', [AdminController::class, 'getDataProduk'])->name('produk.data');
        Route::post('save', [AdminController::class, 'saveProduk'])->name('produk.save');
        Route::post('delete', [AdminController::class, 'deleteProduk'])->name('produk.delete');
        Route::post('list', [AdminController::class, 'getListProdukByKategori'])->name('produk.list');
    });

    Route::group(['prefix' => 'transaksi'], function () 
    {
        Route::get('/', [AdminController::class, 'transaksi'])->name('transaksi');
        Route::get('add', [AdminController::class, 'showDataTransaksi'])->name('transaksi.add');
        Route::get('detail/{id}', [AdminController::class, 'showDataTransaksi'])->name('transaksi.detail');
        Route::post('save', [AdminController::class, 'saveTransaksi'])->name('transaksi.save');
        Route::post('delete', [AdminController::class, 'deleteTransaksi'])->name('transaksi.delete');
    });

    Route::group(['prefix' => 'ulasan'], function () 
    {
        Route::get('/', [AdminController::class, 'ulasan'])->name('ulasan');
        Route::post('data', [AdminController::class, 'getDataUlasan'])->name('ulasan.data');
    });

    Route::group(['prefix' => 'customer'], function () 
    {
        Route::get('/', [AdminController::class, 'customer'])->name('customer');
        Route::post('data', [AdminController::class, 'getDataCustomer'])->name('customer.data');
        Route::post('data2', [AdminController::class, 'getDataCustomerByTelp'])->name('customer.data2');
    });

    Route::group(['prefix' => 'users'], function () 
    {
        Route::get('/', [AdminController::class, 'users'])->name('users');
        Route::post('data', [AdminController::class, 'getDataUsers'])->name('users.data');
        Route::post('save', [AdminController::class, 'saveUsers'])->name('users.save');
        Route::post('delete', [AdminController::class, 'deleteUsers'])->name('users.delete');
        Route::post('password', [AdminController::class, 'changePasswordUsers'])->name('users.password');
    });
});

require __DIR__.'/auth.php';
