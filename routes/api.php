<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\APIController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () 
{
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::any('/kategori', [APIController::class, 'getKategori']);
    Route::any('/pembayaran', [APIController::class, 'getPembayaran']);
    Route::any('/provinsi', [APIController::class, 'getProvinsi']);
    Route::any('/kota', [APIController::class, 'getKota']);

    Route::group(['prefix' => 'profile'], function () 
    {
        Route::any('/', [APIController::class, 'getProfileCustomer']);
        Route::post('/change', [APIController::class, 'changeProfileCustomer']);
        Route::post('/password', [APIController::class, 'changePasswordCustomer']);
    });

    Route::group(['prefix' => 'produk'], function () 
    {
        Route::any('/', [APIController::class, 'getProduk']);
        Route::any('/detail', [APIController::class, 'getDetailProduk']);
    });

    Route::group(['prefix' => 'kurir'], function () 
    {
        Route::any('/', [APIController::class, 'getKurir']);
        Route::any('/layanan', [APIController::class, 'getLayananKurir']);
    });

    Route::group(['prefix' => 'transaksi'], function () 
    {
        Route::any('/total', [APIController::class, 'hitungTotalTransaksi']);
        Route::any('/add', [APIController::class, 'addTransaksi']);
        Route::any('/list', [APIController::class, 'listTransaksi']);
        Route::any('/detail', [APIController::class, 'detailTransaksi']);
    });
    

});