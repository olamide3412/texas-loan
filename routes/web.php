<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Home');
})->name('home');


Route::inertia('/about-us','About')->name('about');
Route::inertia('/rate','Rate')->name('rate');
Route::inertia('/faq','FAQ')->name('faq');

Route::get('/products-list', [ProductController::class, 'productPublicIndex'])->name('product.home');

Route::inertia('/login','Auth/Login')->name('login');
Route::post('/login', [AuthController::class, 'login']);




// Payment routes
Route::post('/orders/{order}/initiate-payment', [PaymentController::class, 'initiatePayment'])->middleware('auth:web,client')->name('order.payment.initiate');
Route::get('/payment/verify/{order}', [PaymentController::class, 'verifyPayment'])->name('payment.verify');
Route::post('/payment/webhook', [PaymentController::class, 'flwWebHook'])->name('payment.webhook');

Route::middleware(['auth', 'admin'])->group(function (){

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/reports/payments', [ReportController::class, 'paymentReports'])->middleware('super_admin')->name('reports.payments');

    //I dont need must of this user route cause staff link will be use to create those route

    Route::get('/logs', [LogController::class, 'index'])->name('log.index');

});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth:web,client')->name('logout');

// Include modular route files
require __DIR__ . '/client.php';
require __DIR__ . '/product.php';
require __DIR__ . '/order.php';
require __DIR__ . '/staff.php';
