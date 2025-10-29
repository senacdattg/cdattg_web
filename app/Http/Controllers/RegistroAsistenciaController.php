<?php

namespace App\Http\Controllers;

use App\Events\NuevaAsistenciaRegistrada;
use App\Models\AsistenciaAprendiz;
use App\Models\FichaCaracterizacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para el registro de asistencias por jornada
 * Responsabilidad: Registrar entradas/salidas y organizar por jornada
 */
class RegistroAsistenciaController extends Controller
{
    /**
     * Registra una entrada de asistencia
     * Dispara evento WebSocket en tiempo real
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registrarEntrada(Request $request)
    {
        $request->validate([
            'instructor_ficha_id' => 'required|exists:instructor_fichas_caracterizacion,id',
            'aprendiz_ficha_id' => 'required|exists:aprendiz_fichas_caracterizacion,id',
            'evidencia_id' => 'nullable|exists:evidencias,id',
        ]);

        try {
            DB::beginTransaction();

            // Verificar si ya tiene asistencia de entrada hoy sin salida
            $asistenciaExistente = AsistenciaAprendiz::where('aprendiz_ficha_id', $request->aprendiz_ficha_id)
                ->whereNull('hora_salida')
                ->whereDate('created_at', Carbon::today())
                ->first();

            if ($asistenciaExistente) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ya existe un registro de entrada sin salida para hoy',
                    'asistencia' => $asistenciaExistente
                ], 400);
            }

            // Registrar entrada
            $asistencia = AsistenciaAprendiz::create([
                'instructor_ficha_id' => $request->instructor_ficha_id,
                'aprendiz_ficha_id' => $request->aprendiz_ficha_id,
                'evidencia_id' => $request->evidencia_id,
                'hora_ingreso' => Carbon::now()->format('H:i:s'),
                'hora_salida' => null,
            ]);

            // Cargar relaciones para obtener toda la información
            $asistencia->load([
                'aprendizFicha.aprendiz.persona',
                'aprendizFicha.ficha.jornadaFormacion',
                'instructorFichaCaracterizacion.instructor.persona'
            ]);

            $nombreAprendiz = $asistencia->aprendizFicha->aprendiz->persona->getNombreCompletoAttribute();
            $ficha = $asistencia->aprendizFicha->ficha;
            $jornada = $ficha->jornadaFormacion->jornada ?? 'No especificada';

            // Disparar evento de WebSocket
            event(new NuevaAsistenciaRegistrada([
                'id' => $asistencia->id,
                'aprendiz' => $nombreAprendiz,
                'estado' => 'entrada',
                'timestamp' => now()->toISOString(),
                'jornada' => $jornada,
                'ficha' => $ficha->ficha,
            ]));

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Entrada registrada exitosamente',
                'asistencia' => [
                    'id' => $asistencia->id,
                    'aprendiz' => $nombreAprendiz,
                    'hora_ingreso' => $asistencia->hora_ingreso,
                    'jornada' => $jornada,
                    'ficha' => $ficha->ficha,
                    'fecha' => $asistencia->created_at->format('Y-m-d H:i:s'),
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar entrada: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al registrar la entrada',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registra una salida de asistencia
     * Dispara evento WebSocket en tiempo real
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registrarSalida(Request $request)
    {
        $request->validate([
            'aprendiz_ficha_id' => 'required|exists:aprendiz_fichas_caracterizacion,id',
        ]);

        try {
            DB::beginTransaction();

            // Buscar asistencia de entrada sin salida
            $asistencia = AsistenciaAprendiz::where('aprendiz_ficha_id', $request->aprendiz_ficha_id)
                ->whereNull('hora_salida')
                ->whereDate('created_at', Carbon::today())
                ->latest()
                ->first();

            if (!$asistencia) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontró un registro de entrada sin salida para hoy'
                ], 404);
            }

            // Registrar salida
            $asistencia->hora_salida = Carbon::now()->format('H:i:s');
            $asistencia->save();

            // Cargar relaciones para obtener toda la información
            $asistencia->load([
                'aprendizFicha.aprendiz.persona',
                'aprendizFicha.ficha.jornadaFormacion',
                'instructorFichaCaracterizacion.instructor.persona'
            ]);

            $nombreAprendiz = $asistencia->aprendizFicha->aprendiz->persona->getNombreCompletoAttribute();
            $ficha = $asistencia->aprendizFicha->ficha;
            $jornada = $ficha->jornadaFormacion->jornada ?? 'No especificada';

            // Disparar evento de WebSocket
            event(new NuevaAsistenciaRegistrada([
                'id' => $asistencia->id,
                'aprendiz' => $nombreAprendiz,
                'estado' => 'salida',
                'timestamp' => now()->toISOString(),
                'jornada' => $jornada,
                'ficha' => $ficha->ficha,
            ]));

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Salida registrada exitosamente',
                'asistencia' => [
                    'id' => $asistencia->id,
                    'aprendiz' => $nombreAprendiz,
                    'hora_ingreso' => $asistencia->hora_ingreso,
                    'hora_salida' => $asistencia->hora_salida,
                    'jornada' => $jornada,
                    'ficha' => $ficha->ficha,
                    'fecha' => $asistencia->created_at->format('Y-m-d H:i:s'),
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar salida: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al registrar la salida',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene las asistencias del día actual por jornada
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerAsistenciasPorJornada(Request $request)
    {
        try {
            $jornadaId = $request->input('jornada_id');
            $fecha = $request->input('fecha', Carbon::today()->format('Y-m-d'));

            $query = AsistenciaAprendiz::with([
                'aprendizFicha.aprendiz.persona',
                'aprendizFicha.ficha.jornadaFormacion',
                'instructorFichaCaracterizacion.instructor.persona'
            ])->whereDate('created_at', $fecha);

            // Filtrar por jornada si se especifica
            if ($jornadaId) {
                $query->whereHas('aprendizFicha.ficha', function ($q) use ($jornadaId) {
                    $q->where('jornada_id', $jornadaId);
                });
            }

            $asistencias = $query->orderBy('created_at', 'desc')->get();

            // Formatear datos
            $asistenciasFormateadas = $asistencias->map(function ($asistencia) {
                $ficha = $asistencia->aprendizFicha->ficha;
                return [
                    'id' => $asistencia->id,
                    'aprendiz' => $asistencia->aprendizFicha->aprendiz->persona->getNombreCompletoAttribute(),
                    'numero_documento' => $asistencia->aprendizFicha->aprendiz->persona->numero_documento,
                    'hora_ingreso' => $asistencia->hora_ingreso,
                    'hora_salida' => $asistencia->hora_salida,
                    'ficha' => $ficha->ficha,
                    'jornada' => $ficha->jornadaFormacion->jornada ?? 'No especificada',
                    'jornada_id' => $ficha->jornada_id,
                    'fecha' => $asistencia->created_at->format('Y-m-d'),
                    'estado' => $asistencia->hora_salida ? 'completa' : 'en_curso',
                ];
            });

            // Agrupar por jornada
            $agrupadoPorJornada = $asistenciasFormateadas->groupBy('jornada');

            return response()->json([
                'status' => 'success',
                'fecha' => $fecha,
                'total_asistencias' => $asistencias->count(),
                'asistencias' => $asistenciasFormateadas,
                'por_jornada' => $agrupadoPorJornada,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener asistencias por jornada: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener asistencias',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene todas las fichas con sus jornadas
     * Útil para listar opciones al registrar asistencia
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerFichasConJornadas()
    {
        try {
            $fichas = FichaCaracterizacion::with(['jornadaFormacion', 'programaFormacion'])
                ->where('status', 1) // Solo fichas activas
                ->get()
                ->map(function ($ficha) {
                    return [
                        'id' => $ficha->id,
                        'ficha' => $ficha->ficha,
                        'programa' => $ficha->programaFormacion->nombre ?? 'No especificado',
                        'jornada' => $ficha->jornadaFormacion->jornada ?? 'No especificada',
                        'jornada_id' => $ficha->jornada_id,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'fichas' => $fichas,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener fichas con jornadas: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener fichas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}