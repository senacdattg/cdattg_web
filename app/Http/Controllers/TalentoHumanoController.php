<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TalentoHumanoController extends Controller
{
    public function index()
    {
        return view('talento-humano.index');
    }

    public function consultar(Request $request)
    {
        // Aquí irá la lógica de consulta
        // Por ahora solo retornamos una respuesta básica
        return response()->json([
            'success' => true,
            'message' => 'Consulta realizada'
        ]);
    }
}