<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistroActividadesController;

Route::resource('registro-actividades', RegistroActividadesController::class);
