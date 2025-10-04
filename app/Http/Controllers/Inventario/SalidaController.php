<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SalidaController extends Controller
{
    public function aprobar(): View
    {
        return view('inventario.salida.aprobar_salida');
    }
}
