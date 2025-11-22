<?php

namespace App\Livewire;

use App\Exceptions\PersonaException;
use App\Models\Persona;
use App\Models\Sede;
use App\Services\PersonaIngresoSalidaService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class IngresoSalidaComponent extends Component
{
    // Búsqueda
    public $numeroDocumento = '';
    public $personaEncontrada = null;
    public $mostrarFormulario = false;
    public $modoEdicion = false;

    // Registro de ingreso/salida
    public $sedeId = null;
    public $sedeSeleccionada = null;
    public $mostrarModalSede = false;
    public $accionPendiente = null; // 'entrada' o 'salida'
    public $observaciones = '';

    // Estado de carga
    public $procesando = false;
    public $mensaje = '';
    public $tipoMensaje = '';

    // Datos para el formulario de persona
    public $personaId = null;
    public $datosPersona = [];

    protected $listeners = ['limpiarBusqueda'];

    public function mount()
    {
        // Inicializar con sede por defecto si hay solo una activa
        $sedesActivas = Sede::where('status', 1)->get();
        if ($sedesActivas->count() === 1) {
            $this->sedeId = $sedesActivas->first()->id;
            $this->sedeSeleccionada = $sedesActivas->first();
        }
    }

    /**
     * Hook que se ejecuta cuando cambia el número de documento
     */
    public function updatedNumeroDocumento()
    {
        $this->buscarPersona();
    }

    /**
     * Buscar persona por número de documento
     */
    public function buscarPersona()
    {
        $this->resetearEstado();
        
        if (strlen(trim($this->numeroDocumento)) < 3) {
            return;
        }

        try {
            $persona = app(\App\Services\PersonaService::class)
                ->buscarPorDocumento(trim($this->numeroDocumento));

            if ($persona) {
                $this->personaEncontrada = $persona;
                $this->personaId = $persona->id;
                $this->cargarDatosPersona($persona);
                $this->mostrarFormulario = true;
                $this->modoEdicion = false;
            } else {
                $this->mostrarFormulario = false;
                $this->personaEncontrada = null;
            }
        } catch (\Exception $e) {
            Log::error('Error buscando persona: ' . $e->getMessage());
            $this->mostrarMensaje('error', 'Error al buscar persona: ' . $e->getMessage());
        }
    }

    /**
     * Cargar datos de la persona en el formulario
     */
    protected function cargarDatosPersona($persona)
    {
        $this->datosPersona = [
            'id' => $persona->id,
            'tipo_documento' => $persona->tipo_documento,
            'numero_documento' => $persona->numero_documento,
            'primer_nombre' => $persona->primer_nombre,
            'segundo_nombre' => $persona->segundo_nombre ?? '',
            'primer_apellido' => $persona->primer_apellido,
            'segundo_apellido' => $persona->segundo_apellido ?? '',
            'email' => $persona->email ?? '',
            'celular' => $persona->celular ?? '',
            'telefono' => $persona->telefono ?? '',
        ];
    }

    /**
     * Abrir modal para seleccionar sede y registrar entrada
     */
    public function abrirModalEntrada()
    {
        if (!$this->personaEncontrada) {
            $this->mostrarMensaje('warning', 'Debe buscar una persona primero');
            return;
        }

        // Verificar si ya tiene una entrada activa
        $entradaActiva = $this->verificarEstadoPersona();
        if ($entradaActiva) {
            $sedeNombre = $entradaActiva->sede
                ? $entradaActiva->sede->sede
                : 'una sede';
            $mensaje = "La persona ya tiene un registro de entrada activo en {$sedeNombre}. " .
                "Debe registrar la salida primero.";
            $this->mostrarMensaje('warning', $mensaje);
            return;
        }

        $this->accionPendiente = 'entrada';
        $this->mostrarModalSede = true;
    }

    /**
     * Registrar salida directamente usando la sede de la entrada activa
     */
    public function abrirModalSalida()
    {
        if (!$this->personaEncontrada) {
            $this->mostrarMensaje('warning', 'Debe buscar una persona primero');
            return;
        }

        // Obtener el registro de entrada activo (sin salida) de la persona
        $entradaActiva = \App\Models\PersonaIngresoSalida::where('persona_id', $this->personaId)
            ->whereNull('timestamp_salida')
            ->whereDate('fecha_entrada', now()->toDateString())
            ->first();

        if (!$entradaActiva) {
            $this->mostrarMensaje('warning', 'La persona no tiene un registro de entrada activo para hoy.');
            return;
        }

        // Usar la sede de la entrada activa
        $this->sedeId = $entradaActiva->sede_id;
        $this->sedeSeleccionada = $entradaActiva->sede;
        $this->accionPendiente = 'salida';

        // Registrar la salida directamente sin abrir modal
        $this->registrarIngresoSalida();
    }

    /**
     * Cerrar modal de sede
     */
    public function cerrarModalSede()
    {
        $this->mostrarModalSede = false;
        $this->sedeId = null;
        $this->accionPendiente = null;
    }

    /**
     * Registrar entrada o salida
     */
    public function registrarIngresoSalida()
    {
        $this->validate([
            'sedeId' => 'required|integer|exists:sedes,id',
            'personaId' => 'required|integer|exists:personas,id',
        ], [
            'sedeId.required' => 'Debe seleccionar una sede',
            'sedeId.exists' => 'La sede seleccionada no es válida',
            'personaId.required' => 'No hay una persona seleccionada',
            'personaId.exists' => 'La persona seleccionada no es válida',
        ]);

        $this->procesando = true;
        
        // Solo cerrar el modal si estaba abierto (para entrada)
        if ($this->mostrarModalSede) {
        $this->mostrarModalSede = false;
        }

        try {
            $service = app(PersonaIngresoSalidaService::class);

            if ($this->accionPendiente === 'entrada') {
                // Verificar nuevamente antes de registrar entrada
                if ($this->tieneEntradaActivaEnSede($this->sedeId)) {
                    $mensaje = 'Ya existe un registro de entrada sin salida para hoy en esta sede.';
                    $this->mostrarMensaje('warning', $mensaje);
                    $this->procesando = false;
                    return;
                }
                $service->registrarEntrada(
                    $this->personaId,
                    $this->sedeId,
                    null, // ambiente_id
                    null, // ficha_caracterizacion_id
                    $this->observaciones ?: null
                );

                $this->mostrarMensaje('success', 'Entrada registrada correctamente');
                $this->dispatch('entrada-registrada', [
                    'persona' => $this->personaEncontrada->primer_nombre . ' ' .
                        $this->personaEncontrada->primer_apellido,
                    'sede' => $this->sedeSeleccionada->sede ?? 'N/A',
                ]);
            } else {
                $service->registrarSalida(
                    $this->personaId,
                    $this->sedeId,
                    $this->observaciones ?: null
                );

                // Asegurar que la sede esté cargada
                if (!$this->sedeSeleccionada && $this->sedeId) {
                    $this->sedeSeleccionada = Sede::find($this->sedeId);
                }

                $nombrePersona = $this->personaEncontrada->primer_nombre . ' ' .
                    $this->personaEncontrada->primer_apellido;
                $nombreSede = $this->sedeSeleccionada ? $this->sedeSeleccionada->sede : 'N/A';

                $this->mostrarMensaje('success', 'Salida registrada correctamente');
                $this->dispatch('salida-registrada', [
                    'persona' => $nombrePersona,
                    'sede' => $nombreSede,
                ]);
            }

            // Limpiar campos
            $this->observaciones = '';
            $this->accionPendiente = null;
            
            // Forzar actualización del estado para refrescar los botones
            // Refrescar la relación de la persona para obtener datos actualizados
            if ($this->personaEncontrada) {
                $this->personaEncontrada->refresh();
            }
        } catch (PersonaException $e) {
            $this->mostrarMensaje('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error registrando ingreso/salida: ' . $e->getMessage());
            $this->mostrarMensaje(
                'error',
                'Error al procesar la solicitud: ' . $e->getMessage()
            );
        } finally {
            $this->procesando = false;
        }
    }

    /**
     * Actualizar sede seleccionada
     */
    public function updatedSedeId()
    {
        if ($this->sedeId) {
            $this->sedeSeleccionada = Sede::find($this->sedeId);
        } else {
            $this->sedeSeleccionada = null;
        }
    }

    /**
     * Limpiar búsqueda
     */
    public function limpiarBusqueda()
    {
        $this->resetearEstado();
        $this->numeroDocumento = '';
    }

    /**
     * Resetear estado del componente
     */
    protected function resetearEstado()
    {
        $this->personaEncontrada = null;
        $this->mostrarFormulario = false;
        $this->modoEdicion = false;
        $this->personaId = null;
        $this->datosPersona = [];
        $this->mensaje = '';
        $this->tipoMensaje = '';
    }

    /**
     * Mostrar mensaje al usuario
     */
    protected function mostrarMensaje($tipo, $mensaje)
    {
        $this->tipoMensaje = $tipo;
        $this->mensaje = $mensaje;
        
        // Limpiar mensaje después de 5 segundos
        $this->dispatch('mostrar-mensaje', [
            'tipo' => $tipo,
            'mensaje' => $mensaje,
        ]);
    }

    /**
     * Verificar si la persona está dentro actualmente y obtener el registro activo
     */
    public function verificarEstadoPersona()
    {
        if (!$this->personaId) {
            return null;
        }

        // Obtener entrada activa (sin salida) de hoy en cualquier sede
        // Cargar la relación de sede para mostrar el nombre
        return \App\Models\PersonaIngresoSalida::where('persona_id', $this->personaId)
            ->whereNull('timestamp_salida')
            ->whereDate('fecha_entrada', now()->toDateString())
            ->with('sede')
            ->first();
    }

    /**
     * Verificar si tiene entrada activa en una sede específica
     */
    public function tieneEntradaActivaEnSede($sedeId)
    {
        if (!$this->personaId || !$sedeId) {
            return false;
        }

        return \App\Models\PersonaIngresoSalida::where('persona_id', $this->personaId)
            ->where('sede_id', $sedeId)
            ->whereNull('timestamp_salida')
            ->whereDate('fecha_entrada', now()->toDateString())
            ->exists();
    }

    public function render()
    {
        $sedes = Sede::where('status', 1)->orderBy('sede')->get();
        $entradaActiva = $this->verificarEstadoPersona();
        $estaDentro = $entradaActiva !== null;

        return view('livewire.ingreso-salida-component', [
            'sedes' => $sedes,
            'estaDentro' => $estaDentro,
            'entradaActiva' => $entradaActiva,
        ]);
    }
}

