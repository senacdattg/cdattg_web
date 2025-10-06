@extends('adminlte::page')

@section('title', 'Gestionar Instructores - Ficha ' . $ficha->ficha)

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    @vite(['resources/css/parametros.css'])
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        /* Estilos para timeline de logs */
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-marker {
            position: absolute;
            left: -35px;
            top: 15px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }
        
        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: -29px;
            top: 27px;
            width: 2px;
            height: calc(100% + 10px);
            background-color: #dee2e6;
        }
        
        .timeline-content {
            margin-left: 0;
        }
        
        /* Estilos para badges de estado */
        .badge {
            font-size: 0.75rem;
        }
        
        /* Estilos para alertas expandibles */
        .alert {
            border-radius: 0.375rem;
        }
        
        /* Estilos para tabla de instructores */
        .table-responsive {
            border-radius: 0.375rem;
        }
        
        .table tbody tr.table-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }
        
        .table tbody tr.table-info {
            background-color: rgba(23, 162, 184, 0.1);
        }
        
        .table tbody tr.table-light {
            background-color: rgba(248, 249, 250, 0.8);
        }
        
        /* Estilos para estadísticas */
        .border-left-primary {
            border-left: 0.25rem solid #007bff !important;
        }
        
        .border-left-success {
            border-left: 0.25rem solid #28a745 !important;
        }
        
        .border-left-warning {
            border-left: 0.25rem solid #ffc107 !important;
        }
        
        .border-left-info {
            border-left: 0.25rem solid #17a2b8 !important;
        }
        
        .border-left-danger {
            border-left: 0.25rem solid #dc3545 !important;
        }
        
        /* Estilos para hover effects */
        .hover-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .hover-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-chalkboard-teacher text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Gestionar Instructores</h1>
                        <p class="text-muted mb-0 font-weight-light">Ficha {{ $ficha->ficha }}</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('verificarLogin') }}" class="link_right_header">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('fichaCaracterizacion.index') }}" class="link_right_header">
                                    <i class="fas fa-list"></i> Fichas de Caracterización
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}" class="link_right_header">
                                    <i class="fas fa-eye"></i> Ficha {{ $ficha->ficha }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-chalkboard-teacher"></i> Gestionar Instructores
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('fichaCaracterizacion.show', $ficha->id) }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a la Ficha
                    </a>

                    <!-- Información de la Ficha -->
                    <div class="card detail-card no-hover mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Información de la Ficha
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <strong>Programa:</strong><br>
                                        <span class="text-muted">{{ $ficha->programaFormacion->nombre ?? 'No asignado' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <strong>Fecha Inicio:</strong><br>
                                        <span class="text-muted">{{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format('d/m/Y') : 'No definida' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <strong>Fecha Fin:</strong><br>
                                        <span class="text-muted">{{ $ficha->fecha_fin ? $ficha->fecha_fin->format('d/m/Y') : 'No definida' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <strong>Total Horas:</strong><br>
                                        <span class="text-muted">{{ $ficha->total_horas ?? 'No definido' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instructores Asignados -->
                    <div class="card shadow-sm no-hover mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-success">
                                <i class="fas fa-users mr-2"></i>Instructores Asignados
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($instructoresAsignados->count() > 0)
                                @foreach($instructoresAsignados as $asignacion)
                                    <div class="card mb-3 {{ $ficha->instructor_id == $asignacion->instructor_id ? 'border-primary' : '' }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">
                                                        {{ $asignacion->instructor->persona->primer_nombre }} 
                                                        {{ $asignacion->instructor->persona->primer_apellido }}
                                                        @if($ficha->instructor_id == $asignacion->instructor_id)
                                                            <span class="badge badge-primary ml-2">Principal</span>
                                                        @else
                                                            <span class="badge badge-secondary ml-2">Auxiliar</span>
                                                        @endif
                                                    </h6>
                                                    <p class="text-muted mb-2 small">
                                                        <i class="fas fa-calendar mr-1"></i>
                                                        {{ $asignacion->fecha_inicio->format('d/m/Y') }} - 
                                                        {{ $asignacion->fecha_fin->format('d/m/Y') }}
                                                    </p>
                                                    <p class="text-muted mb-0 small">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        {{ $asignacion->total_horas_instructor }} horas
                                                    </p>
                                                    @if($asignacion->instructorFichaDias && $asignacion->instructorFichaDias->count() > 0)
                                                        @php
                                                            $diasAsignados = $asignacion->instructorFichaDias
                                                                ->filter(function($dia) { return $dia->dia && $dia->dia->name; })
                                                                ->map(function($dia) { return $dia->dia->name; })
                                                                ->implode(', ');
                                                        @endphp
                                                        @if($diasAsignados)
                                                            <p class="text-muted mb-0 small mt-1">
                                                                <i class="fas fa-calendar-week mr-1"></i>
                                                                <strong>Días asignados:</strong> 
                                                                {{ $diasAsignados }}
                                                            </p>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div>
                                                    <form action="{{ route('fichaCaracterizacion.desasignarInstructor', [$ficha->id, $asignacion->instructor_id]) }}" 
                                                          method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                onclick="return confirm('¿Está seguro de desasignar este instructor?')"
                                                                title="Desasignar instructor">
                                                            <i class="fas fa-user-minus mr-1"></i>
                                                            Desasignar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-user-slash fa-3x mb-3"></i>
                                    <p>No hay instructores asignados a esta ficha.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Asignar Instructores -->
                    <div class="card shadow-sm no-hover mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-warning">
                                <i class="fas fa-user-plus mr-2"></i>Asignar Instructores
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('fichaCaracterizacion.asignarInstructores', $ficha->id) }}" method="POST" id="formAsignarInstructores">
                                @csrf
                                
                                <!-- Instructor Principal -->
                                <div class="form-group">
                                    <label for="instructor_principal_id" class="form-label font-weight-bold">
                                        <i class="fas fa-star text-warning mr-1"></i>
                                        Instructor Principal <span class="text-danger">*</span>
                                    </label>
                                    <select name="instructor_principal_id" id="instructor_principal_id" class="form-control select2" required>
                                        <option value="">Seleccione un instructor principal</option>
                                        @foreach($instructoresConDisponibilidad as $instructorId => $data)
                                            <option value="{{ $instructorId }}" 
                                                    {{ $ficha->instructor_id == $instructorId ? 'selected' : '' }}
                                                    {{ !$data['disponible'] ? 'disabled' : '' }}>
                                                {{ $data['instructor']->persona->primer_nombre }} 
                                                {{ $data['instructor']->persona->primer_apellido }}
                                                @if(!$data['disponible'])
                                                    (No disponible - {{ count($data['conflictos'] ?? []) }} conflictos)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_principal_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Información de fechas permitidas -->
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Rango de fechas permitidas:</strong>
                                    @if($ficha->fecha_inicio && $ficha->fecha_fin)
                                        Desde {{ $ficha->fecha_inicio->format('d/m/Y') }} hasta {{ $ficha->fecha_fin->format('d/m/Y') }}
                                    @else
                                        <span class="text-warning">Las fechas de la ficha no están definidas</span>
                                    @endif
                                </div>

                                <!-- Información de días de formación -->
                                @if($diasFormacionFicha->count() > 0)
                                    <div class="alert alert-success">
                                        <i class="fas fa-calendar-check"></i>
                                        <strong>Días de formación disponibles:</strong>
                                        {{ $diasFormacionFicha->pluck('name')->implode(', ') }}
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Advertencia:</strong> Esta ficha no tiene días de formación asignados. 
                                        <a href="{{ route('fichaCaracterizacion.gestionarDiasFormacion', $ficha->id) }}" class="alert-link">
                                            Asignar días de formación
                                        </a>
                                    </div>
                                @endif

                                <!-- Lista de Instructores -->
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">
                                        <i class="fas fa-users mr-1"></i>
                                        Instructores Asignados <span class="text-danger">*</span>
                                    </label>
                                    <div id="instructores-container">
                                        <!-- Los instructores se agregarán dinámicamente aquí -->
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="agregarInstructor()">
                                        <i class="fas fa-plus mr-1"></i> Agregar Instructor
                                    </button>
                                </div>

                                <hr class="mt-4">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}" class="btn btn-light mr-2">
                                        <i class="fas fa-times mr-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i> Guardar Asignaciones
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Estadísticas de Asignaciones -->
                    @if(isset($estadisticasAsignaciones))
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Total Asignaciones
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $estadisticasAsignaciones['total_asignaciones_activas'] ?? 0 }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-users fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Asignaciones Exitosas
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $estadisticasAsignaciones['exitosos'] ?? 0 }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                    Instructores Activos
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $estadisticasAsignaciones['instructores_con_fichas'] ?? 0 }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Promedio por Instructor
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $estadisticasAsignaciones['promedio_fichas_por_instructor'] ?? 0 }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Instructores Disponibles -->
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title m-0 font-weight-bold text-info">
                                <i class="fas fa-list mr-2"></i>Instructores Disponibles
                            </h5>
                            <div class="badge badge-info">
                                {{ count(array_filter($instructoresConDisponibilidad ?? [], fn($i) => $i['disponible'])) }} disponibles
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="py-3">Nombre</th>
                                            <th class="py-3">Disponibilidad</th>
                                            <th class="py-3">Fichas Superpuestas</th>
                                            <th class="py-3">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($instructoresConDisponibilidad as $index => $data)
                                            @php
                                                $instructor = $data['instructor'];
                                                $disponible = $data['disponible'];
                                                $razonesNoDisponible = $data['razones_no_disponible'] ?? [];
                                                $conflictos = $data['conflictos'] ?? [];
                                                $advertencias = $data['advertencias'] ?? [];
                                            @endphp
                                            <tr class="{{ $disponible ? '' : 'table-warning' }}">
                                                <td class="py-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="mr-3">
                                                            @if($disponible)
                                                                <i class="fas fa-check-circle text-success"></i>
                                                            @else
                                                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <strong>{{ $instructor->persona->primer_nombre }} {{ $instructor->persona->primer_apellido }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                {{ $instructor->persona->numero_documento }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    @if($disponible)
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check mr-1"></i>Disponible
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger">
                                                            <i class="fas fa-times mr-1"></i>No disponible
                                                        </span>
                                                    @endif
                                                    
                                                    @if(!empty($advertencias))
                                                        <br>
                                                        <span class="badge badge-warning mt-1">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i>Advertencias
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3">
                                                    @if(!empty($conflictos))
                                                        <div class="text-danger small">
                                                            <strong>Conflictos:</strong>
                                                            @foreach($conflictos as $conflicto)
                                                                <div class="mt-1">
                                                                    <i class="fas fa-calendar-alt mr-1"></i>
                                                                    Ficha {{ $conflicto['ficha_numero'] ?? $conflicto['ficha_id'] }}
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        {{ \Carbon\Carbon::parse($conflicto['fecha_inicio'])->format('d/m/Y') }} - 
                                                                        {{ \Carbon\Carbon::parse($conflicto['fecha_fin'])->format('d/m/Y') }}
                                                                    </small>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="badge badge-success">Sin conflictos</span>
                                                    @endif
                                                </td>
                                                <td class="py-3">
                                                    @if($disponible)
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                onclick="agregarInstructorSeleccionado({{ $instructor->id }})">
                                                            <i class="fas fa-plus mr-1"></i> Agregar
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                                onclick="mostrarRazonesNoDisponible({{ $index }})"
                                                                title="Ver razones">
                                                            <i class="fas fa-info-circle mr-1"></i> Ver razones
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            
                                            <!-- Row para mostrar razones de no disponibilidad (oculto por defecto) -->
                                            @if(!$disponible && !empty($razonesNoDisponible))
                                                <tr id="razones-{{ $index }}" class="table-light" style="display: none;">
                                                    <td colspan="4" class="py-2">
                                                        <div class="alert alert-warning mb-0 py-2">
                                                            <strong><i class="fas fa-exclamation-triangle mr-1"></i>Razones de no disponibilidad:</strong>
                                                            <ul class="mb-0 mt-2">
                                                                @foreach($razonesNoDisponible as $razon)
                                                                    <li>{{ $razon }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                            
                                            <!-- Row para mostrar advertencias -->
                                            @if(!empty($advertencias))
                                                <tr class="table-info">
                                                    <td colspan="4" class="py-2">
                                                        <div class="alert alert-info mb-0 py-2">
                                                            <strong><i class="fas fa-info-circle mr-1"></i>Advertencias:</strong>
                                                            <ul class="mb-0 mt-2">
                                                                @foreach($advertencias as $advertencia)
                                                                    <li>{{ $advertencia }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Logs Recientes de Asignaciones -->
                    @if(isset($logsRecientes) && $logsRecientes->count() > 0)
                        <div class="card shadow-sm no-hover mt-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title m-0 font-weight-bold text-secondary">
                                    <i class="fas fa-history mr-2"></i>Historial de Asignaciones
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    @foreach($logsRecientes as $log)
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
                                                                        onclick="mostrarDetallesError({{ $log->id }})"
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
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    <script src="{{ asset('vendor/adminlte/plugins/select2/js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Cargar instructores existentes
            cargarInstructoresExistentes();
        });

        // Función para cargar instructores ya asignados
        function cargarInstructoresExistentes() {
            @if($instructoresAsignados->count() > 0)
                @foreach($instructoresAsignados as $asignacion)
                    @php
                        $diasFormacion = $asignacion->instructorFichaDias->map(function($dia) {
                            return [
                                'dia_id' => $dia->dia_id
                            ];
                        })->toArray();
                    @endphp
                    agregarInstructorRow(
                        {{ $asignacion->instructor_id }},
                        '{{ addslashes($asignacion->instructor->persona->primer_nombre) }} {{ addslashes($asignacion->instructor->persona->primer_apellido) }}',
                        '{{ $asignacion->fecha_inicio->format('Y-m-d') }}',
                        '{{ $asignacion->fecha_fin->format('Y-m-d') }}',
                        {{ $asignacion->total_horas_instructor }},
                        {{ $ficha->instructor_id == $asignacion->instructor_id ? 'true' : 'false' }},
                        {!! json_encode($diasFormacion) !!}
                    );
                @endforeach
            @endif
        }

        // Función para agregar un instructor desde el botón
        function agregarInstructor() {
            agregarInstructorRow(null, '', '', '', '', false);
        }

        // Función para agregar un instructor seleccionado de la tabla
        function agregarInstructorSeleccionado(instructorId) {
            // Buscar el instructor en los datos disponibles
            const instructores = {!! json_encode($instructoresConDisponibilidad) !!};
            const instructor = instructores[instructorId];
            
            if (instructor && instructor.disponible) {
                const nombre = instructor.instructor.persona.primer_nombre + ' ' + instructor.instructor.persona.primer_apellido;
                agregarInstructorRow(instructorId, nombre, '', '', '', false);
            }
        }

        // Función para agregar una fila de instructor
        function agregarInstructorRow(instructorId, nombre, fechaInicio, fechaFin, horas, esPrincipal, diasFormacion = []) {
            const container = document.getElementById('instructores-container');
            const index = container.children.length;
            
            // Obtener datos de instructores disponibles
            const instructoresDisponibles = {!! json_encode($instructoresConDisponibilidad) !!};
            const diasFormacionDisponibles = {!! json_encode($diasFormacionFicha) !!};
            const fechaInicioMin = '{{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format('Y-m-d') : '' }}';
            const fechaFinMax = '{{ $ficha->fecha_fin ? $ficha->fecha_fin->format('Y-m-d') : '' }}';
            const diasFormacionDisabled = {{ $diasFormacionFicha->count() == 0 ? 'true' : 'false' }};
            
            const div = document.createElement('div');
            div.className = 'card mb-2 instructor-row';
            div.innerHTML = `
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <label class="form-label font-weight-bold">Instructor</label>
                            <select name="instructores[${index}][instructor_id]" class="form-control select2 instructor-select" required>
                                <option value="">Seleccione un instructor</option>
                                ${Object.keys(instructoresDisponibles).map(id => {
                                    const data = instructoresDisponibles[id];
                                    const selected = instructorId == id ? 'selected' : '';
                                    return `<option value="${id}" ${selected}>${data.instructor.persona.primer_nombre} ${data.instructor.persona.primer_apellido}</option>`;
                                }).join('')}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-weight-bold">Fecha Inicio</label>
                            <input type="date" name="instructores[${index}][fecha_inicio]" 
                                   class="form-control" value="${fechaInicio}" 
                                   min="${fechaInicioMin}"
                                   max="${fechaFinMax}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-weight-bold">Fecha Fin</label>
                            <input type="date" name="instructores[${index}][fecha_fin]" 
                                   class="form-control" value="${fechaFin}" 
                                   min="${fechaInicioMin}"
                                   max="${fechaFinMax}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-weight-bold">Horas</label>
                            <input type="number" name="instructores[${index}][total_horas_instructor]" 
                                   class="form-control" value="${horas}" min="1" required>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label font-weight-bold">&nbsp;</label>
                            <button type="button" class="btn btn-sm btn-outline-danger d-block" onclick="eliminarInstructor(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Días de Formación -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-calendar-week mr-1"></i>
                                Días de Formación
                            </label>
                            <div class="dias-formacion-container" data-index="${index}">
                                <!-- Los días se agregarán dinámicamente aquí -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success mt-1" onclick="agregarDiaFormacion(${index})" ${diasFormacionDisabled ? 'disabled' : ''}>
                                <i class="fas fa-plus mr-1"></i> Agregar Día
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(div);
            
            // Inicializar Select2 en el nuevo elemento
            $(div).find('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
            
            // Agregar validación de fechas
            const fechaInicioInput = div.querySelector('input[name*="[fecha_inicio]"]');
            const fechaFinInput = div.querySelector('input[name*="[fecha_fin]"]');
            const instructorSelect = div.querySelector('select[name*="[instructor_id]"]');
            
            // Función para validar disponibilidad de fechas
            function validarDisponibilidadFechas() {
                const instructorId = instructorSelect.value;
                const fechaInicio = fechaInicioInput.value;
                const fechaFin = fechaFinInput.value;
                
                if (instructorId && fechaInicio && fechaFin) {
                    // Crear elemento de mensaje de validación si no existe
                    let mensajeValidacion = div.querySelector('.mensaje-validacion-fechas');
                    if (!mensajeValidacion) {
                        mensajeValidacion = document.createElement('div');
                        mensajeValidacion.className = 'mensaje-validacion-fechas alert mt-2';
                        div.querySelector('.card-body').appendChild(mensajeValidacion);
                    }
                    
                    // Mostrar loading
                    mensajeValidacion.className = 'mensaje-validacion-fechas alert alert-info mt-2';
                    mensajeValidacion.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Verificando disponibilidad...';
                    
                    // Llamar al endpoint de validación
                    fetch('{{ route("api.fichas.verificar-disponibilidad-fechas-instructor") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            instructor_id: instructorId,
                            fecha_inicio: fechaInicio,
                            fecha_fin: fechaFin
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.disponible) {
                            mensajeValidacion.className = 'mensaje-validacion-fechas alert alert-success mt-2';
                            mensajeValidacion.innerHTML = '<i class="fas fa-check mr-1"></i> ' + data.mensaje;
                        } else {
                            mensajeValidacion.className = 'mensaje-validacion-fechas alert alert-danger mt-2';
                            let conflictosText = data.conflictos.map(c => 
                                `Ficha ${c.ficha} (${c.programa}) del ${c.fecha_inicio} al ${c.fecha_fin}`
                            ).join(', ');
                            mensajeValidacion.innerHTML = `<i class="fas fa-exclamation-triangle mr-1"></i> ${data.mensaje}: ${conflictosText}`;
                        }
                    })
                    .catch(error => {
                        console.error('Error al verificar disponibilidad:', error);
                        mensajeValidacion.className = 'mensaje-validacion-fechas alert alert-warning mt-2';
                        mensajeValidacion.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Error al verificar disponibilidad. Intente nuevamente.';
                    });
                }
            }
            
            fechaInicioInput.addEventListener('change', function() {
                fechaFinInput.min = this.value;
                if (fechaFinInput.value && fechaFinInput.value < this.value) {
                    fechaFinInput.value = this.value;
                }
                // Validar disponibilidad cuando cambie la fecha de inicio
                setTimeout(validarDisponibilidadFechas, 500);
            });
            
            fechaFinInput.addEventListener('change', function() {
                fechaInicioInput.max = this.value;
                if (fechaInicioInput.value && fechaInicioInput.value > this.value) {
                    fechaInicioInput.value = this.value;
                }
                // Validar disponibilidad cuando cambie la fecha de fin
                setTimeout(validarDisponibilidadFechas, 500);
            });
            
            instructorSelect.addEventListener('change', function() {
                // Validar disponibilidad cuando cambie el instructor
                setTimeout(validarDisponibilidadFechas, 500);
            });
            
            // Cargar días de formación existentes si los hay
            if (diasFormacion && diasFormacion.length > 0) {
                diasFormacion.forEach(dia => {
                    agregarDiaFormacionRow(index, dia.dia_id);
                });
            }
        }

        // Función para eliminar un instructor
        function eliminarInstructor(button) {
            const row = button.closest('.instructor-row');
            row.remove();
            
            // Renumerar los índices
            renumerarIndices();
        }

        // Función para renumerar los índices de los instructores
        function renumerarIndices() {
            const container = document.getElementById('instructores-container');
            const rows = container.querySelectorAll('.instructor-row');
            
            rows.forEach((row, index) => {
                // Actualizar los nombres de los campos
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/\[\d+\]/, `[${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
        }

        // Función para agregar un día de formación
        function agregarDiaFormacion(instructorIndex) {
            const diasDisponibles = {!! json_encode($diasFormacionFicha) !!};
            if (diasDisponibles.length === 0) {
                alert('No hay días de formación asignados a esta ficha. Debe asignar días de formación primero.');
                return;
            }
            agregarDiaFormacionRow(instructorIndex, '');
        }

        // Función para agregar una fila de día de formación
        function agregarDiaFormacionRow(instructorIndex, diaId) {
            const container = document.querySelector(`[data-index="${instructorIndex}"]`);
            const diaIndex = container.children.length;
            const diasFormacionDisponibles = {!! json_encode($diasFormacionFicha) !!};
            
            const div = document.createElement('div');
            div.className = 'row mb-2 dia-formacion-row';
            div.innerHTML = `
                <div class="col-md-10">
                    <select name="instructores[${instructorIndex}][dias_formacion][${diaIndex}][dia_id]" class="form-control select2" required>
                        <option value="">Seleccione día</option>
                        ${diasFormacionDisponibles.map(dia => {
                            const selected = diaId == dia.id ? 'selected' : '';
                            return `<option value="${dia.id}" ${selected}>${dia.name}</option>`;
                        }).join('')}
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDiaFormacion(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(div);
            
            // Inicializar Select2 en el nuevo elemento
            $(div).find('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }

        // Función para eliminar un día de formación
        function eliminarDiaFormacion(button) {
            const row = button.closest('.dia-formacion-row');
            row.remove();
        }

        // Validación del formulario
        document.getElementById('formAsignarInstructores').addEventListener('submit', function(e) {
            const instructorPrincipal = document.getElementById('instructor_principal_id').value;
            const instructoresAsignados = document.querySelectorAll('.instructor-select');
            
            if (!instructorPrincipal) {
                e.preventDefault();
                alert('Debe seleccionar un instructor principal.');
                return;
            }
            
            if (instructoresAsignados.length === 0) {
                e.preventDefault();
                alert('Debe asignar al menos un instructor.');
                return;
            }
            
            // Verificar que el instructor principal esté en la lista
            let instructorPrincipalEnLista = false;
            instructoresAsignados.forEach(select => {
                if (select.value === instructorPrincipal) {
                    instructorPrincipalEnLista = true;
                }
            });
            
            if (!instructorPrincipalEnLista) {
                e.preventDefault();
                alert('El instructor principal debe estar en la lista de instructores asignados.');
                return;
            }
            
            // Validar fechas de instructores
            const fechaInicioFicha = '{{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format("Y-m-d") : "" }}';
            const fechaFinFicha = '{{ $ficha->fecha_fin ? $ficha->fecha_fin->format("Y-m-d") : "" }}';
            
            const fechaInicioInputs = document.querySelectorAll('input[name*="[fecha_inicio]"]');
            const fechaFinInputs = document.querySelectorAll('input[name*="[fecha_fin]"]');
            
            for (let i = 0; i < fechaInicioInputs.length; i++) {
                const fechaInicio = fechaInicioInputs[i].value;
                const fechaFin = fechaFinInputs[i].value;
                
                if (fechaInicioFicha && fechaInicio < fechaInicioFicha) {
                    e.preventDefault();
                    alert(`La fecha de inicio del instructor ${i + 1} debe ser posterior o igual a ${fechaInicioFicha}.`);
                    return;
                }
                
                if (fechaFinFicha && fechaFin > fechaFinFicha) {
                    e.preventDefault();
                    alert(`La fecha de fin del instructor ${i + 1} debe ser anterior o igual a ${fechaFinFicha}.`);
                    return;
                }
                
                if (fechaInicio > fechaFin) {
                    e.preventDefault();
                    alert(`La fecha de inicio del instructor ${i + 1} debe ser anterior o igual a la fecha de fin.`);
                    return;
                }
            }
        });

        // Cargar instructores existentes al inicializar la página
        document.addEventListener('DOMContentLoaded', function() {
            @if($instructoresAsignados->count() > 0)
                @foreach($instructoresAsignados as $asignacion)
                    agregarInstructorExistente({
                        instructor_id: {{ $asignacion->instructor_id }},
                        fecha_inicio: '{{ $asignacion->fecha_inicio->format("Y-m-d") }}',
                        fecha_fin: '{{ $asignacion->fecha_fin->format("Y-m-d") }}',
                        total_horas_instructor: {{ $asignacion->total_horas_instructor }},
                        dias_formacion: {!! json_encode($asignacion->instructorFichaDias->pluck('dia_id')->toArray()) !!}
                    });
                @endforeach
            @endif
        });

        // Función para agregar instructores existentes
        function agregarInstructorExistente(data) {
            const container = document.getElementById('instructores-container');
            const index = container.children.length;
            
            const div = document.createElement('div');
            div.className = 'card mb-3 instructor-card';
            div.innerHTML = `
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <label class="form-label font-weight-bold">Instructor</label>
                            <select name="instructores[${index}][instructor_id]" class="form-control select2 instructor-select" required>
                                <option value="">Seleccione un instructor</option>
                                ${Object.keys(instructoresDisponibles).map(id => {
                                    const instructorData = instructoresDisponibles[id];
                                    const selected = data.instructor_id == id ? 'selected' : '';
                                    return `<option value="${id}" ${selected}>${instructorData.instructor.persona.primer_nombre} ${instructorData.instructor.persona.primer_apellido}</option>`;
                                }).join('')}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-weight-bold">Fecha Inicio</label>
                            <input type="date" name="instructores[${index}][fecha_inicio]" 
                                   class="form-control" value="${data.fecha_inicio}" 
                                   min="${fechaInicioMin}"
                                   max="${fechaFinMax}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-weight-bold">Fecha Fin</label>
                            <input type="date" name="instructores[${index}][fecha_fin]" 
                                   class="form-control" value="${data.fecha_fin}" 
                                   min="${fechaInicioMin}"
                                   max="${fechaFinMax}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-weight-bold">Horas</label>
                            <input type="number" name="instructores[${index}][total_horas_instructor]" 
                                   class="form-control" value="${data.total_horas_instructor}" min="1" required>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label font-weight-bold">&nbsp;</label>
                            <button type="button" class="btn btn-sm btn-outline-danger d-block" onclick="eliminarInstructor(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Días de Formación -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-calendar-week mr-1"></i>
                                Días de Formación
                            </label>
                            <div class="dias-formacion-container" data-index="${index}">
                                <!-- Los días se agregarán dinámicamente aquí -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="agregarDiaFormacion(${index})">
                                <i class="fas fa-plus mr-1"></i> Agregar Día
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(div);
            
            // Inicializar Select2
            $(div).find('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Cargar días de formación existentes
            if (data.dias_formacion && data.dias_formacion.length > 0) {
                data.dias_formacion.forEach(diaId => {
                    agregarDiaFormacionExistente(index, diaId);
                });
            }
        }

        // Función para agregar días de formación existentes
        function agregarDiaFormacionExistente(index, diaId) {
            const container = document.querySelector(`.dias-formacion-container[data-index="${index}"]`);
            const diaIndex = container.children.length;
            
            const div = document.createElement('div');
            div.className = 'row mt-1 dia-formacion-row';
            div.innerHTML = `
                <div class="col-md-8">
                    <select name="instructores[${index}][dias_formacion][${diaIndex}][dia_id]" class="form-control select2" required>
                        <option value="">Seleccione un día</option>
                        ${diasFormacionFicha.map(dia => {
                            const selected = diaId == dia.id ? 'selected' : '';
                            return `<option value="${dia.id}" ${selected}>${dia.name}</option>`;
                        }).join('')}
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDiaFormacion(this)">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            `;
            
            container.appendChild(div);
            
            // Inicializar Select2
            $(div).find('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }

        // Función para mostrar razones de no disponibilidad
        function mostrarRazonesNoDisponible(index) {
            const row = document.getElementById(`razones-${index}`);
            if (row) {
                if (row.style.display === 'none') {
                    row.style.display = 'table-row';
                } else {
                    row.style.display = 'none';
                }
            }
        }

        // Función para mostrar detalles de errores en logs
        function mostrarDetallesError(logId) {
            const detalles = document.getElementById(`detalles-error-${logId}`);
            if (detalles) {
                if (detalles.style.display === 'none') {
                    detalles.style.display = 'block';
                } else {
                    detalles.style.display = 'none';
                }
            }
        }

        // Función mejorada para agregar instructor seleccionado con validaciones
        function agregarInstructorSeleccionado(instructorId) {
            // Verificar si ya está agregado
            const instructoresContainer = document.getElementById('instructores-container');
            const instructoresExistentes = instructoresContainer.querySelectorAll('.instructor-select');
            
            for (let select of instructoresExistentes) {
                if (select.value == instructorId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Instructor ya agregado',
                        text: 'Este instructor ya está en la lista de asignaciones.',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }
            }

            // Buscar datos del instructor en la lista de disponibles
            const instructorData = instructoresDisponibles[instructorId];
            if (!instructorData) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se encontraron datos del instructor.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Verificar disponibilidad
            if (!instructorData.disponible) {
                Swal.fire({
                    icon: 'error',
                    title: 'Instructor no disponible',
                    text: 'Este instructor no está disponible para esta ficha.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Agregar el instructor
            agregarInstructor(instructorId);
            
            // Mostrar mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: 'Instructor agregado',
                text: `${instructorData.instructor.persona.primer_nombre} ${instructorData.instructor.persona.primer_apellido} ha sido agregado a la lista de asignaciones.`,
                timer: 2000,
                showConfirmButton: false
            });
        }

        // Función para mostrar estadísticas en tiempo real
        function actualizarEstadisticas() {
            const instructoresAsignados = document.querySelectorAll('.instructor-select');
            const instructorPrincipal = document.getElementById('instructor_principal_id');
            
            // Actualizar contador de instructores asignados
            const contador = document.querySelector('.badge-info');
            if (contador) {
                const disponibles = {{ count(array_filter($instructoresConDisponibilidad ?? [], fn($i) => $i['disponible'])) }};
                contador.textContent = `${disponibles} disponibles`;
            }
        }

        // Inicializar estadísticas al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            actualizarEstadisticas();
        });
    </script>
@endsection