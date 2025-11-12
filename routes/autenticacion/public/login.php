<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

// Login
Route::resource('login', LoginController::class);
Route::controller(LoginController::class)->group(function () {
    Route::get('/verificarLogin', 'verificarLogin')->name('verificarLogin');
    Route::post('/iniciarSesion', 'iniciarSesion')->name('iniciarSesion');
});
