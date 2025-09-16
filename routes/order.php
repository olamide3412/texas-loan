<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth:web,client'])->group(function () {
    Route::get('/orders/create/{client}', [OrderController::class, 'create'])->name('order.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('order.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('order.show');
});

Route::middleware(['auth'])->group(function () {
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->middleware('super_admin')->name('order.destroy');
    Route::get('/orders/edit/{order}', [OrderController::class, 'edit'])->name('order.edit');

    // web.php
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->middleware('super_admin')->name('order.update.status');
    Route::post('/orders/{order}/payment', [OrderController::class, 'updatePayment'])->middleware('admin')->name('order.update.payment');

});


