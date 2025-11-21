<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\PersonaController;

Route::resource('instructor', InstructorController::class);

// Ruta para obtener centros de formación por regional (AJAX)
Route::get('/instructor/centros-por-regional', [InstructorController::class, 'centrosPorRegional'])
    ->name('instructor.centrosPorRegional')
    ->middleware('auth');

Route::middleware('can:VER PROGRAMA DE CARACTERIZACION')->group(function () {
    Route::get('instructor/createImportarCSV', [InstructorController::class, 'createImportarCSV'])->name('instructor.createImportarCSV');
    Route::post('storeImportarCSV', [InstructorController::class, 'storeImportarCSV'])->name('instructor.storeImportarCSV');
    Route::post('instructor/store', [InstructorController::class, 'store'])->name('instructor.legacy.store');
    Route::get('instructor/delete/{id}', [InstructorController::class, 'deleteWithoudUser'])->name('instructor.deleteWithoudUser');
});
