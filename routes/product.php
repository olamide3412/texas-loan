<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth','super_admin'])->group(function () {
    // Products
    Route::get('/products/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/products', [ProductController::class, 'store'])->name('product.store');
    Route::get('/products', [ProductController::class, 'index'])->name('product.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('product.show');
    Route::post('/products/{product}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('product.destroy');

});
