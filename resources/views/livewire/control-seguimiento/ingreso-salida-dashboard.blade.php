<div wire:init="cargarDatos" id="dashboard-ingreso-salida">
    {{-- Estadísticas Generales --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <div class="row w-100 align-items-center">
                        <div class="col-12 col-md-6">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-chart-pie mr-2"></i>
                                Resumen General
                                @if($fechaSeleccionada === \Carbon\Carbon::today()->format('Y-m-d'))
                                    - Hoy
                                @else
                                    - {{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}
                                @endif
                            </h3>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card-tools d-flex flex-wrap justify-content-md-end align-items-center"
                                style="gap: 0.5rem; margin-top: 0.5rem;">
                                {{-- Navegación de fecha --}}
                                <div class="d-flex align-items-center border rounded px-2 py-1"
                                    style="background-color: #f8f9fa;">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-light"
                                        style="margin-right: 0.5rem;"
                                        wire:click="fechaAnterior"
                                        title="Día anterior con registros"
                                        wire:disabled="{{ $tieneFechaAnterior ? 'false' : 'true' }}">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <input
                                        type="date"
                                        class="form-control form-control-sm border-0"
                                        style="width: 140px; background-color: transparent; margin: 0 0.5rem;"
                                        wire:model.live="fechaSeleccionada"
                                        max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                                        title="Seleccionar fecha (solo días con registros)"
                                        id="fechaSeleccionada">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-light"
                                        style="margin-right: 0.5rem;"
                                        wire:click="fechaSiguiente"
                                        title="Día siguiente con registros"
                                        wire:disabled="{{ $tieneFechaSiguiente ? 'false' : 'true' }}">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-info"
                                        wire:click="irAHoy"
                                        title="Ir a hoy"
                                        @php
                                            $esHoy = $fechaSeleccionada === \Carbon\Carbon::today()->format('Y-m-d');
                                        @endphp
                                        wire:disabled="{{ $esHoy ? 'true' : 'false' }}">
                                        <i class="fas fa-calendar-day mr-1"></i>
                                        Hoy
                                    </button>
                                </div>
                                
                                <a href="{{ route('control-seguimiento.ingreso-salida.create') }}"
                                    class="btn btn-success btn-sm">
                                    <i class="fas fa-plus-circle mr-1"></i>
                                    Registrar Ingreso/Salida
                                </a>

                                {{-- Select de frecuencia de actualización --}}
                                <div class="d-flex align-items-center">
                                    <label for="frecuenciaActualizacion" class="mb-0 small text-muted mr-2">
                                        Actualizar:
                                    </label>
                                    <select
                                        id="frecuenciaActualizacion"
                                        class="form-control form-control-sm"
                                        style="width: auto; min-width: 140px;"
                                        wire:model.live="frecuenciaActualizacion"
                                        wire:change="$dispatch('frecuencia-cambiada')">
                                        <option value="1s">1 segundo</option>
                                        <option value="10s">10 segundos</option>
                                        <option value="30s">30 segundos</option>
                                        <option value="60s">1 minuto</option>
                                        <option value="5mins">5 minutos</option>
                                        <option value="10mins">10 minutos</option>
                                        <option value="30mins">30 minutos</option>
                                        <option value="1h">1 hora</option>
                                        <option value="6h">6 horas</option>
                                        <option value="12h">12 horas</option>
                                        <option value="24h">24 horas</option>
                                    </select>
                                </div>

                                {{-- Botón de refrescar manual --}}
                                <button
                                    type="button"
                                    class="btn btn-primary btn-sm"
                                    wire:click="refrescar"
                                    wire:loading.attr="disabled"
                                    title="Refrescar datos manualmente">
                                    <i class="fas fa-sync-alt mr-1" wire:loading.remove wire:target="refrescar"></i>
                                    <i class="fas fa-spinner fa-spin mr-1" wire:loading wire:target="refrescar"></i>
                                    <span wire:loading.remove wire:target="refrescar">Refrescar</span>
                                    <span wire:loading wire:target="refrescar">Refrescando...</span>
                                </button>

                                {{-- Indicador de actualización automática --}}
                                <span class="badge badge-info" wire:loading.remove wire:target="actualizar">
                                    <i class="fas fa-sync-alt mr-1"></i>
                                    <span class="d-none d-md-inline">Auto-actualizando</span>
                                </span>
                                <span class="badge badge-warning" wire:loading wire:target="actualizar">
                                    <i class="fas fa-spinner fa-spin mr-1"></i>
                                    Actualizando...
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Total Personas Dentro --}}
                        @if($estadisticasGenerales['total'] > 0)
                            <div class="col-lg-2 col-md-4 col-6 mb-3">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $estadisticasGenerales['total'] }}</h3>
                                        <p>Total Personas Dentro</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Generar cajas dinámicamente para cada tipo de persona --}}
                        @foreach($tiposPersona as $tipo)
                            @php
                                $cantidad = $estadisticasGenerales[$tipo] ?? 0;
                                $config = $configuracionTiposPersona[$tipo] ?? [
                                    'nombre' => ucfirst(str_replace('_', ' ', $tipo)),
                                    'color' => 'bg-secondary',
                                    'icono' => 'fa-user',
                                ];
                            @endphp
                            @if($cantidad > 0)
                                <div class="col-lg-2 col-md-4 col-6 mb-3">
                                    <div class="small-box {{ $config['color'] }}"
                                        @if(isset($config['estilo_personalizado']))
                                            style="{{ $config['estilo_personalizado'] }}"
                                        @endif>
                                        <div class="inner">
                                            <h3>{{ $cantidad }}</h3>
                                            <p>{{ $config['nombre'] }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas {{ $config['icono'] }}"></i>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel de Eventos Recientes --}}
    @if(count($eventosRecientes) > 0)
        <div class="row mb-4 justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="card card-primary card-outline">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title text-white">
                            <i class="fas fa-history mr-2"></i>
                            Eventos Recientes
                            @if($fechaSeleccionada === \Carbon\Carbon::today()->format('Y-m-d'))
                                - Hoy
                            @else
                                - {{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}
                            @endif
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-hover mb-0"
                                aria-label="Eventos recientes de ingreso y salida"
                                style="margin-bottom: 0;">
                                <thead class="thead-dark sticky-top"
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <tr class="text-center">
                                        <th style="width: 60px; text-align: center; font-weight: 600;
                                            padding: 12px 8px;">
                                            <i class="fas fa-tag"></i>
                                        </th>
                                        <th style="font-weight: 600; padding: 12px; color: white;">
                                            <i class="fas fa-user mr-1"></i> Persona
                                        </th>
                                        <th style="font-weight: 600; padding: 12px; color: white;">
                                            <i class="fas fa-building mr-1"></i> Sede
                                        </th>
                                        <th style="width: 100px; text-align: right; font-weight: 600; padding: 12px;
                                            color: white;">
                                            <i class="fas fa-clock mr-1"></i> Hora
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($eventosRecientes as $index => $evento)
                                        <tr style="transition: background-color 0.2s;"
                                            class="{{ $index % 2 == 0 ? '' : 'table-light' }}">
                                            <td class="text-center align-middle" style="padding: 12px 8px;">
                                                @if($evento['tipo'] === 'entrada')
                                                    <span class="badge badge-success badge-lg"
                                                        style="padding: 8px 12px; font-size: 0.85rem;
                                                            border-radius: 20px;">
                                                        <i class="fas fa-sign-in-alt mr-1"></i>
                                                        Entrada
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger badge-lg"
                                                        style="padding: 8px 12px; font-size: 0.85rem;
                                                            border-radius: 20px;">
                                                        <i class="fas fa-sign-out-alt mr-1"></i>
                                                        Salida
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="align-middle" style="padding: 12px;">
                                                <div style="font-weight: 500; color: #333; font-size: 0.95rem;">
                                                    {{ $evento['persona_nombre'] }}
                                                </div>
                                            </td>
                                            <td class="align-middle" style="padding: 12px;">
                                                <div style="color: #6c757d; font-size: 0.9rem;">
                                                    <i class="fas fa-map-marker-alt mr-1 text-primary"></i>
                                                    {{ $evento['sede_nombre'] ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td class="text-right align-middle" style="padding: 12px;">
                                                <span class="badge badge-info"
                                                    style="padding: 6px 10px; font-size: 0.85rem; font-weight: 600;
                                                        background-color: #17a2b8;">
                                                    <i class="far fa-clock mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($evento['timestamp'])->format('H:i:s') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Gráficos por Sede --}}
    <div class="row">
        @foreach ($estadisticasPorSede as $sedeId => $data)
            @php
                $sede = $data['sede'];
                $estadisticasHoy = $data['estadisticas_hoy'];
                $estadisticasGenerales = $data['estadisticas_generales'];
                $estadisticasRegistros = $data['estadisticas_registros'] ?? null;
                $tieneRegistrosHoy = $data['tiene_registros_hoy'] ?? false;
            @endphp
            @if($tieneRegistrosHoy || $estadisticasHoy['total'] > 0)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-building mr-2"></i>
                                {{ $sede->sede }}
                            </h3>
                        </div>
                        <div class="card-body">
                            {{-- Estadísticas rápidas --}}
                            <div class="row mb-3">
                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-info elevation-1">
                                            <i class="fas fa-users"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Dentro</span>
                                            <span class="info-box-number">
                                                {{ $estadisticasHoy['total'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-success elevation-1">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Entradas</span>
                                            <span class="info-box-number">
                                                {{ $estadisticasRegistros['entradas']['total'] ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-danger elevation-1">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Salidas</span>
                                            <span class="info-box-number">
                                                {{ $estadisticasRegistros['salidas']['total'] ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Gráfico de barras por tipo de persona --}}
                            @if($estadisticasHoy['total'] > 0 ||
                                ($estadisticasRegistros['entradas']['total'] ?? 0) > 0 ||
                                ($estadisticasRegistros['salidas']['total'] ?? 0) > 0)
                                <div class="chart-container" style="position: relative; height: 250px;" wire:ignore>
                                    <canvas id="chartSede{{ $sedeId }}"></canvas>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Sin registros de actividad hoy
                                    </p>
                                </div>
                            @endif

                            {{-- Detalle por tipo con entradas y salidas --}}
                            @if($estadisticasRegistros)
                                <div class="mt-3">
                                    <h6 class="text-center mb-2 text-muted">
                                        <small>Resumen por Rol
                                            @if($fechaSeleccionada === \Carbon\Carbon::today()->format('Y-m-d'))
                                                - Hoy
                                            @else
                                                - {{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}
                                            @endif
                                        </small>
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0"
                                            aria-label="Resumen de entradas, salidas y personas dentro por rol">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th class="text-center" style="font-size: 0.75rem;">Rol</th>
                                                    <th class="text-center" style="font-size: 0.75rem;">Entradas</th>
                                                    <th class="text-center" style="font-size: 0.75rem;">Salidas</th>
                                                    <th class="text-center" style="font-size: 0.75rem;">Dentro</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $entradas = $estadisticasRegistros['entradas'] ?? [];
                                                    $salidas = $estadisticasRegistros['salidas'] ?? [];
                                                @endphp
                                                <tr>
                                                    <td class="text-left"><small>Instructores</small></td>
                                                    <td class="text-center">
                                                        <span class="badge badge-success">
                                                            {{ $entradas['instructores'] ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-danger">
                                                            {{ $salidas['instructores'] ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-info">
                                                            {{ $estadisticasHoy['instructores'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left"><small>Aprendices</small></td>
                                                    <td class="text-center">
                                                        <span class="badge badge-success">
                                                            {{ $entradas['aprendices'] ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-danger">
                                                            {{ $salidas['aprendices'] ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-info">
                                                            {{ $estadisticasHoy['aprendices'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left"><small>Administrativos</small></td>
                                                    <td class="text-center">
                                                        <span class="badge badge-success">
                                                            {{ $entradas['administrativos'] ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-danger">
                                                            {{ $salidas['administrativos'] ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-info">
                                                            {{ $estadisticasHoy['administrativos'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left">
                                                        <small>Super Administradores</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-success">
                                                            {{ $entradas['super_administradores'] ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-danger">
                                                            {{ $salidas['super_administradores'] ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-info">
                                                            {{ $estadisticasHoy['super_administradores'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left"><small>Visitantes</small></td>
                                                    <td class="text-center">
                                                        <span class="badge badge-success">
                                                            {{ $entradas['visitantes'] ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-danger">
                                                            {{ $salidas['visitantes'] ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-info">
                                                            {{ $estadisticasHoy['visitantes'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left"><small>Aspirantes</small></td>
                                                    <td class="text-center">
                                                        <span class="badge badge-success">
                                                            {{ $entradas['aspirantes'] ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-danger">
                                                            {{ $salidas['aspirantes'] ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-info">
                                                            {{ $estadisticasHoy['aspirantes'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                {{-- Fallback: mostrar solo personas dentro si no hay datos de registros --}}
                                <div class="mt-3">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted">Instructores</small>
                                            <p class="mb-0 font-weight-bold text-success">
                                                {{ $estadisticasHoy['instructores'] }}
                                            </p>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Aprendices</small>
                                            <p class="mb-0 font-weight-bold text-warning">
                                                {{ $estadisticasHoy['aprendices'] }}
                                            </p>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Otros</small>
                                            <p class="mb-0 font-weight-bold text-info">
                                                @php
                                                    $totalOtros = $estadisticasHoy['visitantes'] +
                                                        $estadisticasHoy['administrativos'] +
                                                        $estadisticasHoy['aspirantes'] +
                                                        $estadisticasHoy['super_administradores'];
                                                @endphp
                                                {{ $totalOtros }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Gráfico comparativo general --}}
    @php
        $haySedesConDatos = false;
        foreach ($estadisticasPorSede as $data) {
            $tieneRegistros = $data['tiene_registros_hoy'] ?? false;
            if ($data['estadisticas_hoy']['total'] > 0 || $tieneRegistros) {
                $haySedesConDatos = true;
                break;
            }
        }
    @endphp
    @if($haySedesConDatos)
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Comparativo por Sede
                            @if($fechaSeleccionada === \Carbon\Carbon::today()->format('Y-m-d'))
                                - Hoy
                            @else
                                - {{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}
                            @endif
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 400px;" wire:ignore>
                            <canvas id="chartComparativo"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Gráfico de picos de entradas y salidas por hora --}}
    @php
        $hayDatosPorHora = false;
        $totalEntradas = array_sum($estadisticasPorHora['entradas']);
        $totalSalidas = array_sum($estadisticasPorHora['salidas']);
        if ($totalEntradas > 0 || $totalSalidas > 0) {
            $hayDatosPorHora = true;
        }
    @endphp
    @if($hayDatosPorHora)
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-2"></i>
                            Picos de Entradas y Salidas por Hora
                            @if($fechaSeleccionada === \Carbon\Carbon::today()->format('Y-m-d'))
                                - Hoy
                            @else
                                - {{ \Carbon\Carbon::parse($fechaSeleccionada)->format('d/m/Y') }}
                            @endif
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 400px;" wire:ignore>
                            <canvas id="chartPicosHorarios"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('js')
<script>
    document.addEventListener('livewire:init', () => {
        let charts = {};
        let pollingInterval = null;
        let livewireComponent = null;
        
        // Obtener referencia al componente Livewire
        Livewire.hook('morph.updated', ({ component }) => {
            if (!livewireComponent && component) {
                livewireComponent = component;
            }
        });
        
        // Función para obtener la frecuencia actual
        function obtenerFrecuencia() {
            // Intentar obtener desde localStorage primero
            const frecuenciaGuardada = obtenerFrecuenciaDesdeStorage();
            if (frecuenciaGuardada) {
                console.log('[Frecuencia] Obtenida desde localStorage:', frecuenciaGuardada);
                return frecuenciaGuardada;
            }
            
            // Intentar obtener desde el select directamente
            const select = document.getElementById('frecuenciaActualizacion');
            if (select && select.value) {
                console.log('[Frecuencia] Obtenida desde select:', select.value);
                return select.value;
            }
            
            // Si no está disponible, usar el valor por defecto
            console.log('[Frecuencia] Usando valor por defecto: 5mins');
            return '5mins';
        }
        
        // Función para guardar frecuencia en localStorage
        function guardarFrecuencia(frecuencia) {
            try {
                localStorage.setItem('frecuenciaActualizacion', frecuencia);
                console.log('[Frecuencia] Guardada en localStorage:', frecuencia);
            } catch (e) {
                console.warn('[Frecuencia] Error al guardar en localStorage:', e.message);
            }
        }
        
        // Función para obtener frecuencia desde localStorage
        function obtenerFrecuenciaDesdeStorage() {
            try {
                return localStorage.getItem('frecuenciaActualizacion');
            } catch (e) {
                console.warn('[Frecuencia] Error al leer localStorage:', e.message);
                return null;
            }
        }
        
        // Cargar frecuencia desde localStorage al iniciar
        function cargarFrecuenciaDesdeStorage() {
            const frecuenciaGuardada = obtenerFrecuenciaDesdeStorage();
            console.log('[Frecuencia] Intentando cargar desde localStorage:', frecuenciaGuardada);
            
            if (frecuenciaGuardada) {
                const select = document.getElementById('frecuenciaActualizacion');
                if (select) {
                    select.value = frecuenciaGuardada;
                    console.log('[Frecuencia] Select actualizado a:', frecuenciaGuardada);
                    
                    // Actualizar el componente Livewire
                    const wireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                    if (wireId) {
                        const component = Livewire.find(wireId);
                        if (component) {
                            component.set('frecuenciaActualizacion', frecuenciaGuardada);
                            livewireComponent = component;
                            console.log('[Frecuencia] Componente Livewire actualizado a:', frecuenciaGuardada);
                        } else {
                            console.warn('[Frecuencia] No se pudo encontrar el componente Livewire');
                        }
                    } else {
                        console.warn('[Frecuencia] No se encontró wire:id');
                    }
                } else {
                    console.warn('[Frecuencia] No se encontró el select con id frecuenciaActualizacion');
                }
            } else {
                console.log('[Frecuencia] No hay frecuencia guardada en localStorage');
            }
        }
        
        // Función para iniciar/actualizar el polling
        function iniciarPolling() {
            // Limpiar intervalo anterior si existe
            if (pollingInterval) {
                console.log('[Polling] Limpiando intervalo anterior');
                clearInterval(pollingInterval);
            }
            
            // Obtener la frecuencia actual
            const frecuencia = obtenerFrecuencia();
            
            // Mapear frecuencias a milisegundos
            const intervalos = {
                '1s': 1000,
                '10s': 10000,
                '30s': 30000,
                '60s': 60000,
                '5mins': 300000,
                '10mins': 600000,
                '30mins': 1800000,
                '1h': 3600000,
                '6h': 21600000,
                '12h': 43200000,
                '24h': 86400000
            };
            
            const ms = intervalos[frecuencia] || 300000;
            console.log('[Polling] Iniciando con frecuencia:', frecuencia, '(', ms, 'ms )');
            
            // Obtener el componente Livewire
            const wireElement = document.querySelector('[wire\\:id]');
            const wireId = wireElement?.getAttribute('wire:id');
            const component = livewireComponent || (wireId ? Livewire.find(wireId) : null);
            
            // Iniciar nuevo intervalo
            pollingInterval = setInterval(() => {
                console.log('[Polling] Ejecutando actualización automática');
                
                // Intentar usar $wire si está disponible (Livewire v3)
                if (window.$wire) {
                    try {
                        window.$wire.call('actualizar');
                        return;
                    } catch (e) {
                        console.warn('[Polling] Error con $wire:', e);
                    }
                }
                
                // Fallback: usar el componente encontrado
                if (component) {
                    try {
                        // Intentar diferentes métodos según la versión de Livewire
                        if (typeof component.call === 'function') {
                            component.call('actualizar');
                        } else if (typeof component.$call === 'function') {
                            component.$call('actualizar');
                        } else if (typeof component.update === 'function') {
                            component.update();
                        } else {
                            console.warn('[Polling] Método call no disponible en el componente');
                        }
                    } catch (e) {
                        console.error('[Polling] Error al llamar actualizar:', e);
                    }
                } else {
                    // Fallback: buscar el componente nuevamente
                    const wireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                    if (wireId) {
                        const comp = Livewire.find(wireId);
                        if (comp) {
                            try {
                                if (typeof comp.call === 'function') {
                                    comp.call('actualizar');
                                } else if (typeof comp.$call === 'function') {
                                    comp.$call('actualizar');
                                } else {
                                    console.warn('[Polling] Método call no disponible');
                                }
                            } catch (e) {
                                console.error('[Polling] Error al actualizar:', e);
                            }
                        } else {
                            console.warn('[Polling] No se pudo encontrar el componente Livewire para actualizar');
                        }
                    } else {
                        console.warn('[Polling] No se encontró wire:id');
                    }
                }
            }, ms);
        }
        
        // Esperar a que Livewire esté completamente inicializado
        Livewire.hook('morph.updated', () => {
            if (!livewireComponent) {
                const wireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                if (wireId) {
                    livewireComponent = Livewire.find(wireId);
                }
            }
        });
        
        // Cargar frecuencia al inicializar (antes de iniciar polling)
        setTimeout(() => {
            cargarFrecuenciaDesdeStorage();
            iniciarPolling();
        }, 500);
        
        // Escuchar cambios en la frecuencia desde el select
        document.addEventListener('change', (e) => {
            if (e.target.id === 'frecuenciaActualizacion') {
                console.log('[Frecuencia] Cambio detectado en select:', e.target.value);
                // Guardar en localStorage
                guardarFrecuencia(e.target.value);
                iniciarPolling();
            }
        });
        
        // Escuchar evento de Livewire para cargar frecuencia desde storage
        Livewire.on('cargar-frecuencia-desde-storage', () => {
            setTimeout(() => {
                cargarFrecuenciaDesdeStorage();
                iniciarPolling();
            }, 100);
        });
        
        // Escuchar eventos de Livewire
        Livewire.on('frecuencia-cambiada', () => {
            iniciarPolling();
        });
        
        Livewire.on('datos-actualizados', () => {
            iniciarPolling();
        });

        function crearGraficos() {
            // Obtener datos desde Livewire
            const estadisticasPorSede = @json($estadisticasPorSede);
            const sedes = @json($sedes);
            const estadisticasPorHora = @json($estadisticasPorHora);

            // Destruir gráficos existentes de forma segura
            Object.keys(charts).forEach(key => {
                if (charts[key]) {
                    try {
                        // Verificar que el canvas todavía existe antes de destruir
                        const canvas = charts[key].canvas;
                        if (canvas && canvas.parentNode) {
                            charts[key].destroy();
                        }
                    } catch (e) {
                        // Si hay error al destruir, simplemente limpiar la referencia
                        // No mostrar warning en producción para evitar ruido en consola
                    } finally {
                        // Asegurar que la referencia se limpia
                        charts[key] = null;
                    }
                }
            });
            charts = {};

            // Crear gráficos individuales por sede (solo si tienen datos)
            Object.keys(estadisticasPorSede).forEach(sedeId => {
                const data = estadisticasPorSede[sedeId];
                const estadisticas = data.estadisticas_hoy;
                const estadisticasRegistros = data.estadisticas_registros || null;

                // Verificar si tiene personas dentro o registros del día
                const tienePersonasDentro = estadisticas.total > 0;
                const tieneRegistros = estadisticasRegistros &&
                    (estadisticasRegistros.entradas?.total > 0 ||
                     estadisticasRegistros.salidas?.total > 0);

                if (!tienePersonasDentro && !tieneRegistros) {
                    return;
                }

                const ctx = document.getElementById('chartSede' + sedeId);
                if (ctx && ctx.parentNode) {
                    // Obtener datos de entradas y salidas por rol
                    const entradas = estadisticasRegistros?.entradas || {};
                    const salidas = estadisticasRegistros?.salidas || {};
                    
                    // Preparar datos para todos los roles
                    const labels = [
                        'Instructores',
                        'Aprendices',
                        'Administrativos',
                        'Super Administradores',
                        'Visitantes',
                        'Aspirantes'
                    ];
                    const datosEntradas = [
                        entradas.instructores || 0,
                        entradas.aprendices || 0,
                        entradas.administrativos || 0,
                        entradas.super_administradores || 0,
                        entradas.visitantes || 0,
                        entradas.aspirantes || 0
                    ];
                    const datosSalidas = [
                        salidas.instructores || 0,
                        salidas.aprendices || 0,
                        salidas.administrativos || 0,
                        salidas.super_administradores || 0,
                        salidas.visitantes || 0,
                        salidas.aspirantes || 0
                    ];

                    charts['sede' + sedeId] = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Entradas',
                                    data: datosEntradas,
                                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                                    borderColor: 'rgba(40, 167, 69, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Salidas',
                                    data: datosSalidas,
                                    backgroundColor: 'rgba(220, 53, 69, 0.8)',
                                    borderColor: 'rgba(220, 53, 69, 1)',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 10,
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                title: {
                                    display: false
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            label += context.parsed.y + ' registro(s)';
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    stacked: false,
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45,
                                        font: {
                                            size: 10
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        precision: 0
                                    },
                                    title: {
                                        display: true,
                                        text: 'Cantidad',
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            });

            // Gráfico comparativo por sede (solo sedes con datos)
            const ctxComparativo = document.getElementById('chartComparativo');
            if (ctxComparativo && ctxComparativo.parentNode) {
                const sedesConDatos = sedes.filter(sede => {
                    const data = estadisticasPorSede[sede.id];
                    if (!data) return false;
                    
                    const estadisticas = data.estadisticas_hoy || { total: 0 };
                    const estadisticasRegistros = data.estadisticas_registros || null;
                    const tieneRegistros = estadisticasRegistros &&
                        (estadisticasRegistros.entradas?.total > 0 ||
                         estadisticasRegistros.salidas?.total > 0);

                    return estadisticas.total > 0 || tieneRegistros;
                });
                
                const sedesLabels = sedesConDatos.map(sede => sede.sede);
                const datosTotales = sedesConDatos.map(sede => {
                    const data = estadisticasPorSede[sede.id];
                    const estadisticas = data?.estadisticas_hoy || { total: 0 };
                    // Si no hay personas dentro, usar total de entradas del día
                    if (estadisticas.total === 0 && data?.estadisticas_registros) {
                        return data.estadisticas_registros.entradas?.total || 0;
                    }
                    return estadisticas.total;
                });

                charts['comparativo'] = new Chart(ctxComparativo, {
                    type: 'bar',
                    data: {
                        labels: sedesLabels,
                        datasets: [{
                            label: 'Total Personas Dentro',
                            data: datosTotales,
                            backgroundColor: 'rgba(40, 167, 69, 0.8)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Personas Dentro por Sede - Hoy'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }

            // Gráfico de picos de entradas y salidas por hora
            const ctxPicosHorarios = document.getElementById('chartPicosHorarios');
            if (ctxPicosHorarios && ctxPicosHorarios.parentNode) {
                charts['picos'] = new Chart(ctxPicosHorarios, {
                    type: 'line',
                    data: {
                        labels: estadisticasPorHora.horas,
                        datasets: [
                            {
                                label: 'Entradas',
                                data: estadisticasPorHora.entradas,
                                borderColor: 'rgba(40, 167, 69, 1)',
                                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2
                            },
                            {
                                label: 'Salidas',
                                data: estadisticasPorHora.salidas,
                                borderColor: 'rgba(220, 53, 69, 1)',
                                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: 'rgba(220, 53, 69, 1)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: 'Distribución de Entradas y Salidas por Hora del Día',
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                },
                                padding: {
                                    top: 10,
                                    bottom: 20
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.parsed.y + ' persona(s)';
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Hora del Día',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.1)'
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45,
                                    font: {
                                        size: 10
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Cantidad de Personas',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.1)'
                                },
                                ticks: {
                                    stepSize: 1,
                                    precision: 0,
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });
            }
        }

        // Crear gráficos inicialmente
        crearGraficos();

        // Recrear gráficos cuando Livewire actualice el componente
        Livewire.hook('morph.updated', () => {
            // Esperar un poco más para asegurar que el DOM esté completamente actualizado
            // y que los datos JSON se hayan actualizado
            setTimeout(() => {
                // Verificar que el dashboard exista antes de crear gráficos
                const dashboard = document.getElementById('dashboard-ingreso-salida');
                if (dashboard && dashboard.offsetParent !== null) {
                    try {
                        crearGraficos();
                    } catch (e) {
                        console.error('Error al crear gráficos:', e);
                    }
                }
            }, 250);
        });
    });
</script>
@endpush
