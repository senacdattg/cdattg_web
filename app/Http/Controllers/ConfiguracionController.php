<?php

namespace App\Http\Controllers;

use App\Repositories\ConfiguracionRepository;
use App\Repositories\TemaRepository;
use App\Repositories\RegionalRepository;
use App\Repositories\ProgramaFormacionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConfiguracionController extends Controller
{
    protected ConfiguracionRepository $configuracionRepo;
    protected TemaRepository $temaRepo;
    protected RegionalRepository $regionalRepo;
    protected ProgramaFormacionRepository $programaRepo;

    public function __construct(
        ConfiguracionRepository $configuracionRepo,
        TemaRepository $temaRepo,
        RegionalRepository $regionalRepo,
        ProgramaFormacionRepository $programaRepo
    ) {
        $this->middleware('auth');
        $this->configuracionRepo = $configuracionRepo;
        $this->temaRepo = $temaRepo;
        $this->regionalRepo = $regionalRepo;
        $this->programaRepo = $programaRepo;
    }

    /**
     * Obtiene fichas activas (API)
     */
    public function fichasActivas()
    {
        try {
            $fichas = $this->configuracionRepo->obtenerFichasActivas();

            return response()->json([
                'success' => true,
                'data' => $fichas,
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo fichas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener fichas',
            ], 500);
        }
    }

    /**
     * Obtiene regionales activas (API)
     */
    public function regionalesActivas()
    {
        try {
            $regionales = $this->regionalRepo->obtenerActivas();

            return response()->json([
                'success' => true,
                'data' => $regionales,
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo regionales: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener regionales',
            ], 500);
        }
    }

    /**
     * Obtiene programas activos (API)
     */
    public function programasActivos()
    {
        try {
            $programas = $this->programaRepo->obtenerActivos();

            return response()->json([
                'success' => true,
                'data' => $programas,
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo programas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener programas',
            ], 500);
        }
    }

    /**
     * Obtiene tipos de documento
     */
    public function tiposDocumento()
    {
        try {
            $tipos = $this->temaRepo->obtenerTiposDocumento();

            return response()->json([
                'success' => true,
                'data' => $tipos->parametros ?? [],
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo tipos documento: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener tipos de documento',
            ], 500);
        }
    }

    /**
     * Obtiene géneros
     */
    public function generos()
    {
        try {
            $generos = $this->temaRepo->obtenerGeneros();

            return response()->json([
                'success' => true,
                'data' => $generos->parametros ?? [],
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo géneros: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener géneros',
            ], 500);
        }
    }

    /**
     * Limpia caché de configuración
     */
    public function limpiarCache()
    {
        try {
            $this->configuracionRepo->invalidarCache();
            $this->temaRepo->invalidarCache();
            $this->regionalRepo->invalidarCache();
            $this->programaRepo->invalidarCache();

            return response()->json([
                'success' => true,
                'message' => 'Caché de configuración limpiada',
            ]);
        } catch (\Exception $e) {
            Log::error('Error limpiando caché: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar caché',
            ], 500);
        }
    }
}

