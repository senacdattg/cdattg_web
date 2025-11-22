<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\ConfirmPasswordController;

Route::controller(PasswordResetController::class)->group(function () {
    Route::get('/password/reset', 'request')->name('password.request');
    Route::post('/password/email', 'email')->name('password.email');
    Route::get('/password/reset/{token}', 'showResetForm')->name('password.reset');
    Route::post('/password/reset', 'update')->name('password.update');
});

Route::middleware('auth')->controller(ConfirmPasswordController::class)->group(function () {
    Route::get('/password/confirm', 'show')->name('auth.password.confirm');
    Route::post('/password/confirm', 'store')->name('auth.password.confirm.store');
});
