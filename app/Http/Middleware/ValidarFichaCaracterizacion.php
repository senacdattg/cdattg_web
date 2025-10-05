<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FichaCaracterizacionValidationService;
use Illuminate\Support\Facades\Log;

class ValidarFichaCaracterizacion
{
    /**
     * El servicio de validación
     */
    protected $validationService;

    /**
     * Create a new middleware instance.
     */
    public function __construct()
    {
        $this->validationService = new FichaCaracterizacionValidationService();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  $action Acción específica (create, update, store, etc.)
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $action = null)
    {
        try {
            // Solo validar en rutas específicas de fichas de caracterización
            if (!$this->debeValidar($request, $action)) {
                return $next($request);
            }

            $userId = null;
            if (auth()->check()) {
                $userId = auth()->id();
            }

            Log::info('Validando ficha de caracterización en middleware', [
                'action' => $action,
                'route' => $request->route()->getName(),
                'user_id' => $userId,
                'timestamp' => now()
            ]);

            // Obtener datos de la ficha del request
            $datos = $this->obtenerDatosFicha($request, $action);
            
            if (empty($datos)) {
                return $next($request);
            }

            // Obtener ID de ficha para exclusiones en actualizaciones
            $fichaId = $this->obtenerFichaId($request, $action);

            // Realizar validación
            $resultado = $this->validationService->validarFichaCompleta($datos, $fichaId);

            // Si hay errores críticos, rechazar la request
            if (!$resultado['valido']) {
                Log::warning('Validación de ficha fallida en middleware', [
                    'errores' => $resultado['errores'],
                    'action' => $action,
                    'user_id' => $userId
                ]);

                return $this->manejarErroresValidacion($request, $resultado['errores']);
            }

            // Si hay advertencias, agregarlas a la sesión para mostrar al usuario
            if (count($resultado['advertencias']) > 0) {
                session()->flash('advertencias_validacion', $resultado['advertencias']);
            }

            Log::info('Validación de ficha exitosa en middleware', [
                'action' => $action,
                'advertencias' => count($resultado['advertencias']),
                'user_id' => auth()->id()
            ]);

            return $next($request);

        } catch (\Exception $e) {
            Log::error('Error en middleware de validación de ficha', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'action' => $action,
                'user_id' => auth()->id()
            ]);

            // En caso de error, permitir continuar pero registrar el error
            return $next($request);
        }
    }

    /**
     * Determina si debe validar la request.
     */
    private function debeValidar(Request $request, $action)
    {
        $routeName = $request->route()->getName();
        
        // Validar solo en rutas específicas de fichas de caracterización
        $rutasValidadas = [
            'fichaCaracterizacion.store',
            'fichaCaracterizacion.update',
            'fichaCaracterizacion.asignarInstructores',
            'fichaCaracterizacion.guardarDiasFormacion'
        ];

        return in_array($routeName, $rutasValidadas);
    }

    /**
     * Obtiene los datos de la ficha del request.
     */
    private function obtenerDatosFicha(Request $request, $action)
    {
        $datos = [];

        switch ($action) {
            case 'store':
            case 'update':
                $datos = $request->only([
                    'ficha',
                    'programa_formacion_id',
                    'fecha_inicio',
                    'fecha_fin',
                    'instructor_id',
                    'ambiente_id',
                    'sede_id',
                    'jornada_id',
                    'modalidad_formacion_id',
                    'total_horas'
                ]);
                break;

            case 'asignarInstructores':
                // Para asignación de instructores, obtener datos de la ficha actual
                $fichaId = $request->route('id');
                if ($fichaId) {
                    $ficha = \App\Models\FichaCaracterizacion::find($fichaId);
                    if ($ficha) {
                        $datos = $ficha->toArray();
                    }
                }
                break;

            case 'guardarDiasFormacion':
                // Para días de formación, obtener datos de la ficha actual
                $fichaId = $request->route('id');
                if ($fichaId) {
                    $ficha = \App\Models\FichaCaracterizacion::find($fichaId);
                    if ($ficha) {
                        $datos = $ficha->toArray();
                    }
                }
                break;
        }

        return $datos;
    }

    /**
     * Obtiene el ID de la ficha para exclusiones en actualizaciones.
     */
    private function obtenerFichaId(Request $request, $action)
    {
        if ($action === 'update') {
            return $request->route('fichaCaracterizacion') ?? $request->route('id');
        }

        return null;
    }

    /**
     * Maneja los errores de validación.
     */
    private function manejarErroresValidacion(Request $request, $errores)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación encontrados',
                'errors' => $errores
            ], 422);
        }

        return back()->withErrors([
            'validacion_negocio' => $errores
        ])->withInput();
    }
}
