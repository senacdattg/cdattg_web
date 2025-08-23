use Illuminate\Support\Facades\Route;

<?php

// Ruta para mostrar todos los préstamos y salidas
Route::get('/inventario/prestamos-salidas', 'Inventario\PrestamosSalidasController@index')->name('prestamos_salidas.index');

// Ruta para crear un nuevo préstamo o salida
Route::get('/inventario/prestamos-salidas/create', 'Inventario\PrestamosSalidasController@create')->name('prestamos_salidas.create');
Route::post('/inventario/prestamos-salidas', 'Inventario\PrestamosSalidasController@store')->name('prestamos_salidas.store');

// Ruta para mostrar un préstamo o salida específico
Route::get('/inventario/prestamos-salidas/{id}', 'Inventario\PrestamosSalidasController@show')->name('prestamos_salidas.show');

// Ruta para editar un préstamo o salida
Route::get('/inventario/prestamos-salidas/{id}/edit', 'Inventario\PrestamosSalidasController@edit')->name('prestamos_salidas.edit');
Route::put('/inventario/prestamos-salidas/{id}', 'Inventario\PrestamosSalidasController@update')->name('prestamos_salidas.update');

// Ruta para eliminar un préstamo o salida
Route::delete('/inventario/prestamos-salidas/{id}', 'Inventario\PrestamosSalidasController@destroy')->name('prestamos_salidas.destroy');