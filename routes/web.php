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

    Route::group(['prefix' => 'kategori'], function () 
    {
        Route::get('/', [AdminController::class, 'kategori'])->name('kategori');
        Route::post('data', [AdminController::class, 'getDataKategori'])->name('kategori.data');
        Route::post('save', [AdminController::class, 'saveKategori'])->name('kategori.save');
        Route::post('delete', [AdminController::class, 'deleteKategori'])->name('kategori.delete');
    });

    Route::group(['prefix' => 'news'], function () 
    {
        Route::get('/', [AdminController::class, 'news'])->name('news');
        Route::get('add', [AdminController::class, 'showDataNews'])->name('news.add');
        Route::get('detail/{id}', [AdminController::class, 'showDataNews'])->name('news.detail');
        Route::post('save', [AdminController::class, 'saveNews'])->name('news.save');
        Route::post('delete', [AdminController::class, 'deleteNews'])->name('news.delete');
        Route::post('recommended', [AdminController::class, 'setRecommendedNews'])->name('news.recommended');
    });

    Route::group(['prefix' => 'customer'], function () 
    {
        Route::get('/', [AdminController::class, 'customer'])->name('customer');
        Route::post('data', [AdminController::class, 'getDataCustomer'])->name('customer.data');
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
