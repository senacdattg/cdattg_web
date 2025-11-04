<?php

namespace App\Http\Controllers\Complementarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AspiranteComplementario;
use App\Models\ComplementarioOfertado;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Services\EstadisticaComplementarioService;

class EstadisticaComplementarioController extends Controller
{
    protected $estadisticaService;

    public function __construct(EstadisticaComplementarioService $estadisticaService)
    {
        $this->estadisticaService = $estadisticaService;
    }

    /**
     * Mostrar dashboard de estadísticas
     */
    public function estadisticas()
    {
        $departamentos = Departamento::select('id', 'departamento')->get();
        $municipios = Municipio::select('id', 'municipio')->get();

        // Obtener datos reales para las estadísticas
        $estadisticas = $this->estadisticaService->obtenerEstadisticasReales();

        return view('complementarios.estadisticas', compact('departamentos', 'municipios', 'estadisticas'));
    }

    /**
     * API para obtener estadísticas con filtros
     */
    public function apiEstadisticas(Request $request)
    {
        $filtros = $request->only(['fecha_inicio', 'fecha_fin', 'departamento_id', 'municipio_id', 'programa_id']);

        $estadisticas = $this->estadisticaService->obtenerEstadisticasReales($filtros);

        return response()->json($estadisticas);
    }
}