<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\PersonaImportController;

Route::middleware('can:CREAR PERSONA')->group(function () {
    Route::get('/personas/importar', [PersonaImportController::class, 'create'])->name('personas.import.create');
    Route::post('/personas/importar', [PersonaImportController::class, 'store'])->name('personas.import.store');
    Route::get('/personas/importar/{personaImport}/estado', [PersonaImportController::class, 'status'])->name('personas.import.status');
});

Route::middleware('can:VER PERSONA')->get('/personas/datatable', [PersonaController::class, 'datatable'])->name('personas.datatable');

Route::resource('personas', PersonaController::class);

Route::middleware('can:CAMBIAR ESTADO PERSONA')->group(function () {
    Route::put('/personas/{id}/cambiarEstadoPersona', [PersonaController::class, 'cambiarEstadoPersona'])->name('persona.cambiarEstadoPersona');
});
Route::middleware('can:EDITAR PERSONA')->group(function () {
    Route::get('/persona/{persona}/edit', [PersonaController::class, 'edit'])->name('persona.edit');
    Route::put('/persona/{persona}', [PersonaController::class, 'update'])->name('persona.update');
});
Route::middleware('can:ELIMINAR PERSONA')->group(function () {
    Route::delete('/persona/{persona}', [PersonaController::class, 'destroy'])->name('persona.destroy');
});
