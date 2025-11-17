<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/cambiar-password', [ProfileController::class, 'showChangePassword'])->name('password.change');
    Route::put('/cambiar-password', [ProfileController::class, 'changePassword']);
});
