<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IndexProductController;
use App\Http\Controllers\Admin\Dashboard\AdminDashboardController;
use App\Http\Controllers\Admin\Dashboard\MerchantsDashboardController;
use App\Http\Controllers\Admin\Dashboard\AdminTokenController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');
    Route::post('/login', [AuthController::class, 'userLogin'])->name('userLogin');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'userRegister'])->name('userRegister');
    route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('/product')->name('product.')->group(function () {
    Route::get('/', [IndexProductController::class, 'index'])->name('index');
    Route::get('/{product:slug}', [IndexProductController::class, 'show'])->name('show');
});

Route::prefix('/merchant')->middleware('auth')->name('merchant.')->group(function () {
    Route::get('/', [MerchantController::class, 'index'])->name('index');
    Route::prefix('/dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::resource('/product', ProductController::class)->except(['show']);
    });
        Route::get('/register', [MerchantController::class, 'register'])->name('register');
        Route::post('/register', [MerchantController::class, 'merchantRegister'])->name('merchantRegister');
});

Route::prefix('/admin')->middleware(['can:admin-access'])->name('admin.')->group(function () {
    // ADMIN TOKEN
    Route::prefix('/dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('index');

        
        
        Route::prefix('/merchants')->name('merchants.')->group(function () {
            Route::get('/', [MerchantsDashboardController::class, 'index'])->name('index');
            Route::get('/{id}', [MerchantsDashboardController::class, 'approved'])->name('approved');
        });

        Route::prefix('/admintokens')->name('admintokens.')->group(function () {
            Route::get('/', [AdminTokenController::class, 'index'])->name('index');
            Route::post('/', [AdminTokenController::class, 'generate'])->name('generate');
            Route::delete('/{adminToken}', [AdminTokenController::class, 'destroy'])->name('destroy');
        });
    });
});
