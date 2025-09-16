<?php

use App\Http\Controllers\Client\ClientController as ClientClientController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;



// Client Auth Routes
Route::prefix('c')->group(function () {

    Route::inertia('/register','Client/Register')->name('client.register');

    Route::post('/register', [ClientClientController::class, 'store'])->name('client.register.store');

    // Client Dashboard (protected)
    Route::middleware(['auth:client', 'client'])->group(function () {
         Route::get('/dashboard', [ClientClientController::class, 'dashboard'])->name('client.dashboard');
         Route::get('/profile', [ClientClientController::class, 'profile'])->name('client.profile');
         Route::post('/password/update', [ClientClientController::class, 'updatePassword'])->name('client.password.update');
         Route::inertia('/password/change', 'Client/ChangePassword')->name('client.password.change');

        // Add other client routes here
    });
});

Route::middleware(['auth','admin'])->group(function () {
    // Clients
    Route::post('/client-store', [ClientController::class, 'store'])->name('client.store');
    Route::get('/clients', [ClientController::class, 'index'])->name('client.index');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('client.show');
    Route::post('/clients/{client}', [ClientController::class, 'update'])->name('client.update');
    Route::get('/search-clients', [ClientController::class, 'search'])->name('client.search');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('client.destroy');
    Route::put('/client-verify/{client}', [ClientController::class, 'verify'])->name('client.verify');

});
