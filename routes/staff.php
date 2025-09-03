<?php

use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Staff route
    Route::post('/staffs', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/staffs', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staffs/{staff}', [StaffController::class, 'show'])->name('staff.show');
    Route::put('/staffs/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staffs/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');

});
