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
use App\Http\Controllers\backend\InvoiceController;
use App\Http\Controllers\backend\WaybillController;

Route::get('/', function () {
    return view('auth.login');
});

//Floors
Route::get('/floor-overview', function () {
    return view('backend.rack.floorOverview');
})->name('floor.overview');

Route::get('/waybill', [WaybillController::class, 'generateWaybill'])->name('generate.waybill');
Route::get('/invoice', [InvoiceController::class, 'generateInvoice'])->name('generate.invoice');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// User management
Route::prefix('user')->group(function () {
    Route::get('/list', [UsermanagementController::class, 'UserList'])->name('user.index');
    Route::get('/edit/{id}', [UsermanagementController::class, 'UserEdit']);
    Route::post('/update/{id}', [UsermanagementController::class, 'UserUpdate']);
    Route::get('/delete/{id}', [UsermanagementController::class, 'UserDelete']);
});

// Admin side product management
Route::prefix('product')->group(function () {
    Route::get('/list', [ProductController::class, 'listProduct'])->name('product.index');
    Route::post('/insert', [ProductController::class, 'insertProduct'])->name('insert_product');
    Route::patch('/update/{id}', [ProductController::class, 'updateProduct'])->name('update_product');
    Route::delete('/delete/{id}', [ProductController::class, 'deleteProduct'])->name('delete_product');
});

// Password management
Route::prefix('password')->group(function () {
    Route::get('/forget', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
    Route::post('/forget', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
    Route::get('/reset/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
    Route::post('/reset', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
});

// Operations/Delivery & Waybill
Route::prefix('transfer')->group(function () {
    Route::get('/delivery', [ProductController::class, '#'])->name('dolist');
});