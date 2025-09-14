<?php

use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
   // Password change route
    Route::post('/staff/password/update', [StaffController::class, 'updatePassword'])->name('staff.password.update');

    // Profile route
    Route::get('/staff/profile', [StaffController::class, 'profile'])->name('staff.profile');
});

Route::middleware(['super_admin'])->group(function () {
    // Staff route
    Route::post('/staffs', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/staffs', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staffs/{staff}', [StaffController::class, 'show'])->name('staff.show');
    Route::post('/staffs/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staffs/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');

    Route::post('/staff/reset-password/{user}', [StaffController::class,'resetPassword'])->name('staff.resetPassword');
});


