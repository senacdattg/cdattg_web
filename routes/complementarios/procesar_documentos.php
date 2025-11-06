<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Complementarios\DocumentoComplementarioController;

Route::get('/procesar-documentos', [DocumentoComplementarioController::class, 'procesarDocumentos'])
    ->name('procesar-documentos')
    ->middleware('auth');
