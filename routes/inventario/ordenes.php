<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\OrdenController;
use App\Http\Controllers\Inventario\AprobacionController;

// Rutas para órdenes del inventario (préstamos y salidas)
Route::prefix('inventario')
    ->name('inventario.')
    ->middleware(['auth'])
    ->group(function () {
        // Vista de préstamos y salidas (GET) - Formulario de solicitud
        Route::get('ordenes/prestamos-salidas', [OrdenController::class, 'prestamosSalidas'])
            ->name('prestamos-salidas');
            
        // Procesar préstamo/salida (POST)
        Route::post('ordenes/prestamos-salidas', [OrdenController::class, 'storePrestamos'])
            ->name('prestamos-salidas.store');
        
        // Rutas adicionales para órdenes
        $ordenRoute = 'ordenes/{orden}';

        Route::get('ordenes', [OrdenController::class, 'index'])
            ->name('ordenes.index');
        Route::get('ordenes/pendientes', [OrdenController::class, 'pendientes'])
            ->name('ordenes.pendientes');
        Route::get('ordenes/completadas', [OrdenController::class, 'completadas'])
            ->name('ordenes.completadas');
        Route::get('ordenes/rechazadas', [OrdenController::class, 'rechazadas'])
            ->name('ordenes.rechazadas');
        Route::get($ordenRoute, [OrdenController::class, 'show'])
            ->name('ordenes.show');
        Route::put($ordenRoute, [OrdenController::class, 'update'])
            ->name('ordenes.update');
        Route::delete($ordenRoute, [OrdenController::class, 'destroy'])
            ->name('ordenes.destroy');

        // Rutas para aprobaciones (solo superadministrador)
        Route::middleware(['can:APROBAR ORDEN'])->group(function () {
            // Ver solicitudes pendientes
            Route::get('aprobaciones/pendientes', [AprobacionController::class, 'pendientes'])
                ->name('aprobaciones.pendientes');

            // Aprobar/Rechazar detalles de orden
            Route::post('aprobaciones/{detalleOrden}/aprobar', [AprobacionController::class, 'aprobar'])
                ->name('aprobaciones.aprobar');
            Route::post('aprobaciones/{detalleOrden}/rechazar', [AprobacionController::class, 'rechazar'])
                ->name('aprobaciones.rechazar');

            // Aprobar/Rechazar orden completa
            Route::post('aprobaciones/orden/{orden}/aprobar', [AprobacionController::class, 'aprobarOrden'])
                ->name('aprobaciones.aprobar-orden');
            Route::post('aprobaciones/orden/{orden}/rechazar', [AprobacionController::class, 'rechazarOrden'])
                ->name('aprobaciones.rechazar-orden');

            // Historial de aprobaciones
            Route::get('aprobaciones/historial', [AprobacionController::class, 'historial'])
                ->name('aprobaciones.historial');
        });
    });
