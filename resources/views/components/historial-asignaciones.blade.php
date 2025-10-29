{{--
    Componente: Historial de Asignaciones
    Descripción: Timeline con el historial de asignaciones/desasignaciones con estados
    
    Props requeridas:
    - $logs: Collection de logs con los siguientes atributos:
        * id: ID del log
        * accion: 'asignar', 'desasignar' o 'editar'
        * resultado: 'exitoso', 'error' o 'advertencia'
        * nombre_instructor: Nombre del instructor
        * nombre_usuario: Usuario que realizó la acción
        * fecha_accion_formateada: Fecha formateada
        * mensaje: Mensaje de error (opcional)
    
    Props opcionales:
    - $titulo: Título del componente (default: 'Historial de Asignaciones')
    - $icono: Icono del título (default: 'fas fa-history')
    - $colorTitulo: Color del título (default: 'text-secondary')
    
    Uso:
    @include('components.historial-asignaciones', [
        'logs' => $logsRecientes,
        'titulo' => 'Historial de Asignaciones',
        'icono' => 'fas fa-history',
        'colorTitulo' => 'text-secondary'
    ])
--}}

@if(isset($logs) && $logs->count() > 0)
    <div class="card shadow-sm no-hover mt-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title m-0 font-weight-bold {{ $colorTitulo ?? 'text-secondary' }}">
                <i class="{{ $icono ?? 'fas fa-history' }} mr-2"></i>{{ $titulo ?? 'Historial de Asignaciones' }}
            </h5>
        </div>
        <div class="card-body">
            <div class="timeline">
                @foreach($logs as $log)
                    <div class="timeline-item mb-3">
                        <div class="timeline-marker bg-{{ $log->resultado === 'exitoso' ? 'success' : ($log->resultado === 'error' ? 'danger' : 'warning') }}"></div>
                        <div class="timeline-content">
                            <div class="card border-left-{{ $log->resultado === 'exitoso' ? 'success' : ($log->resultado === 'error' ? 'danger' : 'warning') }} shadow-sm">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <i class="fas fa-{{ $log->accion === 'asignar' ? 'plus' : ($log->accion === 'desasignar' ? 'minus' : 'edit') }} mr-1"></i>
                                                {{ ucfirst($log->accion) }} Instructor
                                            </h6>
                                            <p class="mb-1 text-muted small">
                                                <strong>Instructor:</strong> {{ $log->nombre_instructor }}
                                                <br>
                                                <strong>Resultado:</strong> 
                                                <span class="badge badge-{{ $log->resultado === 'exitoso' ? 'success' : ($log->resultado === 'error' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($log->resultado) }}
                                                </span>
                                                <br>
                                                <strong>Usuario:</strong> {{ $log->nombre_usuario }}
                                            </p>
                                            <p class="mb-0 small text-muted">
                                                {{ $log->fecha_accion_formateada }}
                                            </p>
                                        </div>
                                        @if($log->resultado === 'error' && $log->mensaje)
                                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                    onclick="toggleDetallesError({{ $log->id }})"
                                                    title="Ver detalles del error">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @if($log->resultado === 'error' && $log->mensaje)
                                        <div id="detalles-error-{{ $log->id }}" class="mt-2" style="display: none;">
                                            <div class="alert alert-danger mb-0">
                                                <strong>Error:</strong> {{ $log->mensaje }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('js')
        @vite(['resources/js/modules/historial-asignaciones.js'])
    @endpush
@endif

