<?php

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/registro', [RegisterController::class, 'create'])->name('registro');
Route::post('/registrarme', [RegisterController::class, 'store'])->name('registrarme');
