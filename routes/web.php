<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\backend\UsermanagementController;
use App\Http\Controllers\backend\ProductController;
use App\Http\Controllers\backend\CustomerController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\Auth\ForgotPasswordController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/homepage', [App\Http\Controllers\HomeController::class, 'index'])->name('homepage');

// User management
Route::get('/user_list', [UsermanagementController::class, 'UserList'])->name('user.index');
Route::get('/edit_user/{id}', [UsermanagementController::class, 'UserEdit']);
Route::post('/update_user/{id}', [UsermanagementController::class, 'UserUpdate']);
Route::get('/delete_user/{id}', [UsermanagementController::class, 'UserDelete']);

// Admin side product management
Route::get('list_product', [ProductController::class, 'ProductList'])->name('product.index');
Route::get('/edit_product/{id}', [ProductController::class, 'ProductEdit']);
Route::delete('/delete_product/{id}', [ProductController::class, 'ProductDelete'])->name('delete_product');

Route::get('/add_product', [ProductController::class, 'ProductAdd'])->name('productadd');
Route::post('/insert_product', [ProductController::class, 'ProductInsert']);
Route::post('/update_product/{id}', [ProductController::class, 'ProductUpdate']);
Route::get('/qr_product',[ProductController::class, 'ProductQR']);
Route::get('/getProductQRInfo/{productCode}',[ProductController::class, 'getProductQRInfo']);

// Customer
Route::get('/customer_list', [CustomerController::class, 'showAll'])->name('customerlist');


//Forget Password
Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');