<?php

namespace App\Http\Controllers;

use App\Models\FichaCaracterizacion;
use Illuminate\Http\Request;

class FichaCaracterizacionFlutterController extends Controller
{
    /**
     * Obtener todas las fichas de caracterización para Flutter
     */
    public function getAllFichasCaracterizacion()
    {
        try {
            // Primero obtener solo los datos básicos
            $fichas = FichaCaracterizacion::select([
                'id', 'ficha', 'fecha_inicio', 'fecha_fin', 'total_horas', 
                'status', 'programa_formacion_id', 'instructor_id', 'sede_id',
                'modalidad_formacion_id', 'jornada_id', 'ambiente_id'
            ])->orderBy('id', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $fichas,
                'total' => $fichas->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las fichas de caracterización',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener una ficha de caracterización específica por ID
     */
    public function getFichaCaracterizacionById($id)
    {
        try {
            $ficha = FichaCaracterizacion::select([
                'id', 'ficha', 'fecha_inicio', 'fecha_fin', 'total_horas', 
                'status', 'programa_formacion_id', 'instructor_id', 'sede_id',
                'modalidad_formacion_id', 'jornada_id', 'ambiente_id'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $ficha
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ficha de caracterización no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Buscar fichas de caracterización por número
     */
    public function searchFichasByNumber(Request $request)
    {
        try {
            $request->validate([
                'numero' => 'required|string|min:1'
            ]);

            $numero = $request->input('numero');
            
            $fichas = FichaCaracterizacion::where('ficha', 'like', "%{$numero}%")
                ->with([
                    'programaFormacion:id,nombre,codigo',
                    'instructor.persona:id,primer_nombre,primer_apellido',
                    'sede:id,nombre'
                ])
                ->orderBy('id', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $fichas,
                'total' => $fichas->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar fichas de caracterización',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener fichas de caracterización por jornada
     */
    public function getFichasCaracterizacionPorJornada($jornadaId)
    {
        try {
            $fichas = FichaCaracterizacion::where('jornada_id', $jornadaId)
                ->with([
                    'programaFormacion:id,nombre,codigo',
                    'instructor.persona:id,primer_nombre,primer_apellido',
                    'sede:id,nombre',
                    'jornadaFormacion:id,name'
                ])
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $fichas,
                'total' => $fichas->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener fichas por jornada',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener cantidad de aprendices por ficha
     */
    public function getCantidadAprendicesPorFicha($fichaId)
    {
        try {
            $ficha = FichaCaracterizacion::findOrFail($fichaId);
            $cantidad = $ficha->aprendices()->count();

            return response()->json([
                'success' => true,
                'ficha_id' => $fichaId,
                'cantidad_aprendices' => $cantidad
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener cantidad de aprendices',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
