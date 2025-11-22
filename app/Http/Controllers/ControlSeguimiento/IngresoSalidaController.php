<?php

namespace App\Http\Controllers\ControlSeguimiento;

use App\Http\Controllers\Controller;
use App\Repositories\SedeRepository;
use App\Services\PersonaIngresoSalidaService;
use Illuminate\View\View;

/**
 * Controlador para el módulo de Ingreso y Salida
 *
 * Este controlador pertenece al módulo de Control y Seguimiento
 * y maneja el dashboard con estadísticas y el formulario de registro.
 */
class IngresoSalidaController extends Controller
{
    protected SedeRepository $sedeRepository;
    protected PersonaIngresoSalidaService $personaIngresoSalidaService;

    /**
     * Constructor del controlador
     */
    public function __construct(
        SedeRepository $sedeRepository,
        PersonaIngresoSalidaService $personaIngresoSalidaService
    ) {
        $this->middleware('auth');
        $this->sedeRepository = $sedeRepository;
        $this->personaIngresoSalidaService = $personaIngresoSalidaService;
    }

    /**
     * Muestra el dashboard con gráficos del estado actual por cada sede
     *
     * @return View
     */
    public function index(): View
    {
        return view('control-seguimiento.ingreso-salida.index');
    }

    /**
     * Muestra el formulario de registro de ingreso y salida
     *
     * Esta vista usa el componente Livewire IngresoSalidaComponent
     * que maneja toda la lógica de búsqueda y registro de ingresos/salidas.
     *
     * @return View
     */
    public function create(): View
    {
        return view('control-seguimiento.ingreso-salida.create');
    }
}

