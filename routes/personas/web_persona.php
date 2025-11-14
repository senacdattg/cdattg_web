<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\PersonaImportController;

Route::middleware('can:CREAR PERSONA')->group(function () {
    Route::get('/personas/importar', [PersonaImportController::class, 'create'])->name('personas.import.create');
    Route::post('/personas/importar', [PersonaImportController::class, 'store'])->name('personas.import.store');
    Route::get('/personas/importar/{personaImport}/estado', [PersonaImportController::class, 'status'])->name('personas.import.status');
    Route::delete('/personas/importar/{personaImport}', [PersonaImportController::class, 'destroy'])->name('personas.import.destroy');
});

Route::middleware('can:VER PERSONA')->get('/personas/datatable', [PersonaController::class, 'datatable'])->name('personas.datatable');

// Ruta para redirigir al perfil propio (solo con permiso VER PERFIL)
Route::middleware('can:VER PERFIL')->get('/personas/mi-perfil', [PersonaController::class, 'miPerfil'])->name('personas.mi-perfil');

Route::resource('personas', PersonaController::class);

Route::middleware('can:ASIGNAR PERMISOS')->patch('/personas/{persona}/rol', [PersonaController::class, 'updateRole'])->name('personas.update-role');

Route::middleware('can:RESTABLECER PASSWORD')->post('/personas/{persona}/reset-password', [PersonaController::class, 'resetPassword'])->name('personas.reset-password');

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
