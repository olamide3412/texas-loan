<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Clients
    Route::post('/client-store', [ClientController::class, 'store'])->name('client.store');
    Route::get('/clients', [ClientController::class, 'index'])->name('client.index');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('client.show');
    Route::post('/clients/{client}', [ClientController::class, 'update'])->name('client.update');
    Route::get('/search-clients', [ClientController::class, 'search'])->name('client.search');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('client.destroy');
    Route::put('/client-verify/{client}', [ClientController::class, 'verify'])->name('client.verify');

});
