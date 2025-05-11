<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MunicipioController;

Route::resource("municipio", MunicipioController::class);

Route::get('/cargarMunicipios/{departamento_id}', [MunicipioController::class, 'cargarMunicipios'])->name('municipio.cargarMunicipios');
Route::put('/municipio/{municipio}/cambiar-estado', [MunicipioController::class, 'cambiarEstado'])->name('municipio.cambiarEstado');