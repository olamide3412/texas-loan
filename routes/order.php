<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Clients
    Route::get('/orders/create/{client}', [OrderController::class, 'create'])->name('order.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('order.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('order.show');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('order.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('order.destroy');

    Route::get('/orders/edit/{order}', [OrderController::class, 'edit'])->name('order.edit');

    // web.php
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('order.update.status');
    Route::post('/orders/{order}/payment', [OrderController::class, 'updatePayment'])->name('order.update.payment');

});
