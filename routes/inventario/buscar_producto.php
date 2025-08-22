<?php
use App\Http\Controllers\Inventario\ProductoController;


Route::get('/inventario/buscar-producto', [ProductoController::class, 'to_search'])->name('inventario.producto.buscar');
