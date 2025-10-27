<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\DevolucionController;

// Rutas para devoluciones del inventario
Route::prefix('inventario')
    ->name('inventario.')
    ->group(function () {
        // Listar préstamos pendientes de devolución
        Route::get('devoluciones', [DevolucionController::class, 'index'])
            ->name('devoluciones.index');
        
        // Formulario para registrar devolución de un detalle específico
        Route::get('devoluciones/create/{detalleOrden}', [DevolucionController::class, 'create'])
            ->name('devoluciones.create');
        
        // Guardar devolución
        Route::post('devoluciones', [DevolucionController::class, 'store'])
            ->name('devoluciones.store');
        
        // Ver detalle de una devolución específica
        Route::get('devoluciones/{devolucion}', [DevolucionController::class, 'show'])
            ->name('devoluciones.show');
        
        // Historial de todas las devoluciones
        Route::get('devoluciones-historial', [DevolucionController::class, 'historial'])
            ->name('devoluciones.historial');
    });
