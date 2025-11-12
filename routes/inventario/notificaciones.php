<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\NotificacionController;

// Rutas de notificaciones del módulo de inventario
Route::prefix('inventario')->middleware(['auth'])->group(function () {
    Route::prefix('notificaciones')->name('inventario.notificaciones.')->group(function () {
        Route::get('/', [NotificacionController::class, 'index'])->name('index');
        Route::get('/unread', [NotificacionController::class, 'getUnread'])->name('unread');
        Route::post('/{id}/read', [NotificacionController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificacionController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/vaciar-todas', [NotificacionController::class, 'destroyAll'])->name('destroy-all');
        Route::delete('/{id}', [NotificacionController::class, 'destroy'])->name('destroy');
    });
});
