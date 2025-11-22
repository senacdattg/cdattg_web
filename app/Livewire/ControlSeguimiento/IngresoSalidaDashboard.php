<?php

namespace App\Livewire\ControlSeguimiento;

use App\Repositories\SedeRepository;
use App\Services\PersonaIngresoSalidaService;
use Livewire\Component;

class IngresoSalidaDashboard extends Component
{
    public $sedes = [];
    public $estadisticasPorSede = [];
    public $estadisticasGenerales = [];
    public $estadisticasPorHora = [];
    public $frecuenciaActualizacion = '5mins'; // Predeterminado: 5 minutos
    public $tiposPersona = [];
    public $configuracionTiposPersona = [];
    public $fechaSeleccionada;
    public $tieneFechaAnterior = false;
    public $tieneFechaSiguiente = false;
    public $eventosRecientes = [];

    protected SedeRepository $sedeRepository;
    protected PersonaIngresoSalidaService $personaIngresoSalidaService;

    public function boot(
        SedeRepository $sedeRepository,
        PersonaIngresoSalidaService $personaIngresoSalidaService
    ) {
        $this->sedeRepository = $sedeRepository;
        $this->personaIngresoSalidaService = $personaIngresoSalidaService;
    }

    public function mount()
    {
        $this->fechaSeleccionada = \Carbon\Carbon::today()->format('Y-m-d');
        $this->tiposPersona = $this->personaIngresoSalidaService->obtenerTiposPersona();
        $this->configuracionTiposPersona = $this->personaIngresoSalidaService->obtenerConfiguracionTiposPersona();
        
        // Cargar frecuencia desde localStorage si existe
        $this->dispatch('cargar-frecuencia-desde-storage');
        
        $this->cargarDatos();
    }

    /**
     * Carga todos los datos del dashboard
     */
    public function cargarDatos()
    {
        $this->sedes = $this->sedeRepository->obtenerActivas();
        $this->estadisticasPorSede = [];
        $fecha = $this->fechaSeleccionada ?? \Carbon\Carbon::today()->format('Y-m-d');
        
        // Verificar disponibilidad de fechas anterior y siguiente
        $this->tieneFechaAnterior = $this->personaIngresoSalidaService
            ->obtenerFechaAnteriorConRegistros($fecha) !== null;
        $this->tieneFechaSiguiente = $this->personaIngresoSalidaService
            ->obtenerFechaSiguienteConRegistros($fecha) !== null;

        /** @var \App\Models\Sede $sede */
        foreach ($this->sedes as $sede) {
            $sedeId = (int) $sede->id;
            
            // Obtener estadísticas de personas dentro para la fecha seleccionada
            $estadisticasFecha = $this->personaIngresoSalidaService
                ->obtenerEstadisticasPersonasDentroPorFecha($fecha, $sedeId);
            $estadisticasGenerales = $this->personaIngresoSalidaService
                ->obtenerEstadisticasPersonasDentro($sedeId);
            
            // Obtener estadísticas de registros del día (entradas y salidas)
            $estadisticasRegistros = $this->personaIngresoSalidaService
                ->obtenerEstadisticasPorFecha($fecha, $sedeId);
            
            $this->estadisticasPorSede[$sedeId] = [
                'sede' => $sede,
                'estadisticas_hoy' => $estadisticasFecha,
                'estadisticas_generales' => $estadisticasGenerales,
                'estadisticas_registros' => $estadisticasRegistros,
                'tiene_registros_hoy' => ($estadisticasRegistros['entradas']['total'] > 0 || 
                    $estadisticasRegistros['salidas']['total'] > 0),
            ];
        }

        $this->estadisticasGenerales = $this->personaIngresoSalidaService
            ->obtenerEstadisticasPersonasDentroPorFecha($fecha);

        $this->estadisticasPorHora = $this->personaIngresoSalidaService
            ->obtenerEstadisticasPorHora($fecha);

        // Obtener eventos recientes del día
        $this->eventosRecientes = $this->personaIngresoSalidaService
            ->obtenerEventosRecientes($fecha, null, 30);
    }

    /**
     * Método que se ejecuta automáticamente con wire:poll
     */
    public function actualizar()
    {
        $this->cargarDatos();
    }

    /**
     * Refrescar manualmente los datos
     */
    public function refrescar()
    {
        $this->cargarDatos();
        $this->dispatch('datos-actualizados');
    }

    /**
     * Cambiar a la fecha anterior con registros
     */
    public function fechaAnterior()
    {
        $fechaAnterior = $this->personaIngresoSalidaService
            ->obtenerFechaAnteriorConRegistros($this->fechaSeleccionada);
        
        if ($fechaAnterior) {
            $this->fechaSeleccionada = $fechaAnterior;
            $this->cargarDatos();
        }
    }

    /**
     * Cambiar a la fecha siguiente con registros
     */
    public function fechaSiguiente()
    {
        $fechaSiguiente = $this->personaIngresoSalidaService
            ->obtenerFechaSiguienteConRegistros($this->fechaSeleccionada);
        
        if ($fechaSiguiente) {
            $this->fechaSeleccionada = $fechaSiguiente;
            $this->cargarDatos();
        }
    }

    /**
     * Ir a la fecha de hoy
     */
    public function irAHoy()
    {
        $this->fechaSeleccionada = \Carbon\Carbon::today()->format('Y-m-d');
        $this->cargarDatos();
    }

    /**
     * Cambiar fecha desde el input
     */
    public function updatedFechaSeleccionada($value)
    {
        $fechaCarbon = \Carbon\Carbon::parse($value);
        $hoy = \Carbon\Carbon::today();
        
        // No permitir fechas futuras
        if ($fechaCarbon->isAfter($hoy)) {
            $this->fechaSeleccionada = $hoy->format('Y-m-d');
        } else {
            $fechaFormateada = $fechaCarbon->format('Y-m-d');
            
            // Verificar si la fecha tiene registros
            if (!$this->personaIngresoSalidaService->fechaTieneRegistros($fechaFormateada)) {
                // Buscar la fecha más cercana con registros
                $fechaAnterior = $this->personaIngresoSalidaService
                    ->obtenerFechaAnteriorConRegistros($fechaFormateada);
                $fechaSiguiente = $this->personaIngresoSalidaService
                    ->obtenerFechaSiguienteConRegistros($fechaFormateada);
                
                // Elegir la más cercana
                if ($fechaAnterior && $fechaSiguiente) {
                    $diffAnterior = abs($fechaCarbon->diffInDays(\Carbon\Carbon::parse($fechaAnterior)));
                    $diffSiguiente = abs($fechaCarbon->diffInDays(\Carbon\Carbon::parse($fechaSiguiente)));
                    $this->fechaSeleccionada = $diffAnterior <= $diffSiguiente ? $fechaAnterior : $fechaSiguiente;
                } elseif ($fechaAnterior) {
                    $this->fechaSeleccionada = $fechaAnterior;
                } elseif ($fechaSiguiente) {
                    $this->fechaSeleccionada = $fechaSiguiente;
                } else {
                    // Si no hay fechas con registros, mantener la fecha actual (hoy)
                    $this->fechaSeleccionada = $hoy->format('Y-m-d');
                }
            } else {
                $this->fechaSeleccionada = $fechaFormateada;
            }
        }
        $this->cargarDatos();
    }

    public function render()
    {
        return view('livewire.control-seguimiento.ingreso-salida-dashboard');
    }
}
