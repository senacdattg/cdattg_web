<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedConocimientoController;

/*
|--------------------------------------------------------------------------
| Rutas para Red de Conocimiento
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas para la gestión de redes de conocimiento.
|
*/

Route::resource('red-conocimiento', RedConocimientoController::class)->names([
    'index' => 'red-conocimiento.index',
    'create' => 'red-conocimiento.create',
    'store' => 'red-conocimiento.store',
    'show' => 'red-conocimiento.show',
    'edit' => 'red-conocimiento.edit',
    'update' => 'red-conocimiento.update',
    'destroy' => 'red-conocimiento.destroy',
]);
