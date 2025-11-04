<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TalentoHumanoController;

Route::get('/talento-humano', [TalentoHumanoController::class, 'index'])->name('talento-humano.index');
Route::post('/talento-humano/consultar', [TalentoHumanoController::class, 'consultar'])->name('talento-humano.consultar');