<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\NotificationController;

Route::middleware('auth')->group(function () {
    Route::get('/inventario/notifications', [NotificationController::class, 'index'])->name('inventario.notifications.index');
    Route::patch('/inventario/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('inventario.notifications.markAsRead');
});