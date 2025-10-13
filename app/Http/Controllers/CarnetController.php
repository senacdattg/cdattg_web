<?php

namespace App\Http\Controllers;

use App\Services\CarnetService;
use App\Models\Aprendiz;
use App\Models\Instructor;
use App\Jobs\GenerarCarnetsMasivosJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CarnetController extends Controller
{
    protected CarnetService $carnetService;

    public function __construct(CarnetService $carnetService)
    {
        $this->middleware('auth');
        $this->carnetService = $carnetService;
    }

    /**
     * Genera carnet para aprendiz
     */
    public function generarAprendiz(Request $request, $id)
    {
        try {
            $aprendiz = Aprendiz::with('persona', 'fichaCaracterizacion.programaFormacion')->findOrFail($id);
            
            $archivo = $this->carnetService->generarCarnetAprendiz($aprendiz);

            return response()->json([
                'success' => true,
                'message' => 'Carnet generado exitosamente',
                'archivo' => $archivo,
                'url' => asset("storage/{$archivo}"),
            ]);
        } catch (\Exception $e) {
            Log::error('Error generando carnet de aprendiz: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al generar carnet',
            ], 500);
        }
    }

    /**
     * Genera carnet para instructor
     */
    public function generarInstructor(Request $request, $id)
    {
        try {
            $instructor = Instructor::with('persona', 'regional')->findOrFail($id);
            
            $archivo = $this->carnetService->generarCarnetInstructor($instructor);

            return response()->json([
                'success' => true,
                'message' => 'Carnet generado exitosamente',
                'archivo' => $archivo,
                'url' => asset("storage/{$archivo}"),
            ]);
        } catch (\Exception $e) {
            Log::error('Error generando carnet de instructor: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al generar carnet',
            ], 500);
        }
    }

    /**
     * Genera carnets masivos para una ficha
     */
    public function generarMasivos(Request $request)
    {
        $request->validate([
            'ficha_id' => 'required|integer|exists:ficha_caracterizacions,id',
        ]);

        try {
            $aprendices = Aprendiz::where('ficha_caracterizacion_id', $request->ficha_id)
                ->with('persona', 'fichaCaracterizacion')
                ->get();

            if ($aprendices->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay aprendices en esta ficha',
                ], 404);
            }

            // Procesar en background
            GenerarCarnetsMasivosJob::dispatch($aprendices);

            return response()->json([
                'success' => true,
                'message' => "Se están generando {$aprendices->count()} carnets. Recibirás notificación cuando estén listos.",
                'total' => $aprendices->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error generando carnets masivos: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al generar carnets',
            ], 500);
        }
    }

    /**
     * Verifica un carnet mediante QR
     */
    public function verificar(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            $resultado = $this->carnetService->verificarCarnet($request->qr_data);

            return response()->json([
                'success' => $resultado['valido'],
                ...$resultado,
            ]);
        } catch (\Exception $e) {
            Log::error('Error verificando carnet: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al verificar carnet',
            ], 500);
        }
    }
}
