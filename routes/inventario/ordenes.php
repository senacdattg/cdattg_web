<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\OrdenController;

Route::prefix('inventario')->group(function () {
    /*Route::resource('ordenes', OrdenController::class); */
    Route::get('ordenes', function () {
        return view('inventario.ordenes.index');
    })->name('inventario.ordenes.index');
});
