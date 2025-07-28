@extends('adminlte::page')

@section('title', 'Gestión de Actividades')

@section('css')
    @vite(['resources/css/Asistencia/caracter_selecter.css'])
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-fw fa-paint-brush text-white"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Registro de actividades</h1>
                        <p class="text-muted mb-0 font-weight-light">Gestión de actividades</p>
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
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-fw fa-paint-brush"></i> Registro de actividades
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-12 mt-3">
                    <div class="row row-cols-1 row-cols-md-2 g-3">
                        <div class="col">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <i class="fas fa-fw fa-book text-primary me-2 d-none d-sm-inline-block" style="font-size: 1.25rem;"></i>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark d-block">Ficha de formación:</span>
                                        <span class="badge bg-primary text-wrap text-start w-100" style="white-space: normal !important;">{{ $caracterizacion->ficha->ficha }} - {{ $caracterizacion->ficha->programaFormacion->nombre }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <i class="fas fa-fw fa-crown text-primary me-2 d-none d-sm-inline-block" style="font-size: 1.25rem;"></i>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark d-block">Competencia:</span>
                                        <span class="badge bg-primary text-wrap text-start w-100" style="white-space: normal !important;">{{ $caracterizacion->ficha->programaFormacion->competenciaActual()->nombre }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <i class="fas fa-fw fa-graduation-cap text-primary me-2 d-none d-sm-inline-block" style="font-size: 1.25rem;"></i>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark d-block">Resultado de aprendizaje:</span>
                                        <span class="badge bg-primary text-wrap text-start w-100" style="white-space: normal !important;">{{ $caracterizacion->ficha->programaFormacion->competenciaActual()->rapActual()->nombre }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <!-- Estadísticas Rápidas -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 gx-3 gy-4 mb-4">
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="bg-soft-primary rounded-circle p-2 p-sm-3 me-3">
                            <i class="fas fa-tasks text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase text-muted small mb-1">Total de actividades</h6>
                            <div class="d-flex align-items-end">
                                <h3 class="mb-0 fw-bold me-2">{{ $actividades->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="bg-soft-warning rounded-circle p-2 p-sm-3 me-3">
                            <i class="fas fa-user-clock text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            @php
                                $totalActividades = $actividades->count();
                                $totalPendientes = $actividades->where('id_estado', 'PENDIENTE')->count();
                                $porcentajePendiente = $totalActividades > 0 ? ($totalPendientes / $totalActividades) * 100 : 0;
                                $porcentajePendiente = number_format($porcentajePendiente, 2);
                                $totalEnCurso = $actividades->where('id_estado', 'EN CURSO')->count();
                                $porcentajeEnCurso = $totalActividades > 0 ? ($totalEnCurso / $totalActividades) * 100 : 0;
                                $porcentajeEnCurso = number_format($porcentajeEnCurso, 2);
                            @endphp
                            <h6 class="text-uppercase text-muted small mb-1">PENDIENTES</h6>
                            <div class="d-flex align-items-end">
                                <h3 class="mb-0 fw-bold me-2">{{ $totalPendientes }}</h3>
                                <span class="text-warning small mb-1">
                                    <i class="fas fa-pause"></i> {{ $porcentajePendiente }}%
                                </span>
                            </div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-warning" style="width: {{ $porcentajePendiente }}%"></div>
                            </div>
                            <p class="small text-muted mt-2 mb-0">
                                <i class="far fa-clock me-1"></i> {{ $porcentajeEnCurso }}% en curso
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="bg-soft-success rounded-circle p-2 p-sm-3 me-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            @php
                                $totalActividades = $actividades->count();
                                $totalCompletadas = $actividades->where('id_estado', 'COMPLETADO')->count();
                                $porcentajeCompletado = $totalActividades > 0 ? ($totalCompletadas / $totalActividades) * 100 : 0;
                                $porcentajeCompletado = number_format($porcentajeCompletado, 2);
                            @endphp
                            <h6 class="text-uppercase text-muted small mb-1">Completadas</h6>
                            <div class="d-flex align-items-end">
                                <h3 class="mb-0 fw-bold me-2">{{ $totalCompletadas }}</h3>
                                <span class="text-success small mb-1">
                                    <i class="fas fa-check"></i> {{ $porcentajeCompletado }}%
                                </span>
                            </div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: {{ $porcentajeCompletado }}%"></div>
                            </div>
                            <p class="small text-muted mt-2 mb-0">
                                <i class="far fa-calendar-check me-1"></i> {{ $porcentajeCompletado }}% completadas esta semana
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Filtros de Búsqueda -->
    <div class="card shadow-sm mb-4 no-hover">
        <div class="card-header bg-white py-3 d-flex align-items-center">
            <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                <i class="fas fa-plus-circle mr-2"></i> Crear Nueva Actividad
            </h5>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse"
                data-target="#createParameterForm" aria-expanded="true">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>

        <div class="collapse show" id="createParameterForm">
            <div class="card-body">
                @include('registro_actividades.create', compact('caracterizacion'))
            </div>
        </div>
    </div>


    <!-- Lista de Actividades -->
    <div class="row">
        @if (count($actividades) == 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-0 font-weight-light">No hay actividades disponibles.</p>
                    </div>
                </div>
            </div>
        @else
            @foreach ($actividades as $actividad)
                @php
                    //$porcentaje = ($actividad['asistentes'] / $actividad['total_aprendices']) * 100;
                    $porcentaje = 0;
                    $fechaActividad = \Carbon\Carbon::parse($actividad['fecha_evidencia']);
                    $hoy = \Carbon\Carbon::today();
                    $diasRestantes = $fechaActividad->isFuture() ? $hoy->diffInDays($fechaActividad) : 0;
                @endphp

                <div class="col-12 mb-4">
                    <div class="card activity-card h-100">
                        <div class="card-header text-white
                        @if ($actividad['id_estado'] == 'PENDIENTE') bg-gradient-primary
                        @elseif($actividad['id_estado'] == 'EN CURSO') bg-gradient-info
                        @else bg-gradient-success @endif"
                            style="@if ($actividad['id_estado'] == 'PENDIENTE') background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
                        @elseif($actividad['id_estado'] == 'EN CURSO') background: linear-gradient(135deg, #00b4db 0%, #0083b0 100%);
                        @else background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%); @endif">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        @if ($actividad['id_estado'] == 'PENDIENTE')
                                            <i class="far fa-clock fa-2x text-white"></i>
                                        @elseif($actividad['id_estado'] == 'EN CURSO')
                                            <i class="fas fa-spinner fa-spin fa-2x text-white"></i>
                                        @else
                                            <i class="fas fa-check-circle fa-2x text-white"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h5 class="mb-0 text-white">{{ $actividad['nombre'] }}</h5>
                                        <div class="text-white-50">
                                            <i class="far fa-hashtag"></i> {{ $actividad['codigo'] }}
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <span
                                        class="badge badge-pill text-white
                                    @if ($actividad['id_estado'] == 'PENDIENTE') bg-primary
                                    @elseif($actividad['id_estado'] == 'EN CURSO') bg-info
                                    @else bg-success @endif"
                                        style="@if ($actividad['id_estado'] == 'PENDIENTE') background-color: #3a56a8 !important;
                                    @elseif($actividad['id_estado'] == 'EN CURSO') background-color: #00a8d8 !important; font-size: 12px !important;
                                    @else background-color: #28a745 !important; font-size: 12px !important; @endif">
                                        {{ ucfirst(str_replace('_', ' ', $actividad['id_estado'])) }}
                                    </span>
                                    @if ($actividad['id_estado'] == 'PENDIENTE' && $diasRestantes > 0)
                                        <span class="badge badge-pill bg-warning text-dark ml-1">
                                            <i class="far fa-calendar-alt"></i> En {{ $diasRestantes }} días
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <!-- Columna de información principal -->
                                <div class="col-lg-8">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <i class="far fa-calendar-alt text-primary"></i>
                                                <div>
                                                    <small class="text-muted d-block">Fecha de la actividad</small>
                                                    <strong>{{ \Carbon\Carbon::parse($actividad['fecha_evidencia'])->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Progreso de asistencia -->
                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Asistencia registrada</span>
                                            <span class="font-weight-bold">{{ number_format($porcentaje, 0) }}%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar
                                                @if ($porcentaje < 60) bg-danger
                                                @elseif($porcentaje < 80) bg-warning
                                                @else bg-success @endif"
                                                role="progressbar" style="width: {{ $porcentaje }}%"
                                                aria-valuenow="{{ $porcentaje }}" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                        @if ($actividad['id_estado'] != 'PENDIENTE' && $actividad['id_estado'] != 'EN CURSO')
                                            <small class="text-muted">{{ $actividad['asistentes'] }} de
                                                {{ $actividad['total_aprendices'] }} aprendices</small>
                                        @endif
                                    </div>
                                </div>

                                <!-- Columna de acciones -->
                                <div class="col-lg-4 mt-4 mt-lg-0">
                                    <div class="d-flex flex-column h-100">
                                        @if ($actividad['id_estado'] == 'PENDIENTE' || $actividad['id_estado'] == 'EN CURSO')
                                            <a class="btn btn-primary btn-block mb-3"
                                                href="{{ route('asistence.caracterSelected', ['id' => $caracterizacion->id]) }}">
                                                <i class="fas fa-clipboard-check"></i> Tomar Asistencia
                                            </a>

                                            <div class="dropdown mb-3">
                                                <button class="btn btn-outline-secondary btn-block dropdown-toggle"
                                                    type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-h"></i> Más opciones
                                                </button>
                                                <div class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="#">
                                                        <i class="fas fa-edit text-primary mr-2"></i> Editar Actividad
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="fas fa-file-export text-info mr-2"></i> Exportar Lista
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="fas fa-envelope text-success mr-2"></i> Enviar
                                                        Recordatorio
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="#">
                                                        <i class="fas fa-trash-alt mr-2"></i> Cancelar Actividad
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <button class="btn btn-outline-secondary btn-block mb-3" disabled>
                                                <i class="fas fa-check-circle"></i> Asistencia Registrada
                                            </button>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-outline-primary btn-block">
                                                    <i class="fas fa-chart-bar"></i> Estadísticas
                                                </button>
                                                <button class="btn btn-outline-info">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            </div>
                                        @endif
                                        @if ($actividad['id_estado'] != 'PENDIENTE' && $actividad['id_estado'] != 'EN CURSO')
                                            <div class="mt-auto pt-3 border-top">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="far fa-calendar-alt"></i>
                                                        {{ \Carbon\Carbon::parse($actividad['fecha'])->format('d/m/Y') }}
                                                    </small>
                                                    <span
                                                        class="badge
                                                @if ($porcentaje < 60) badge-danger
                                                @elseif($porcentaje < 80) badge-warning
                                                @else badge-success @endif">
                                                        {{ number_format($porcentaje, 0) }}% de asistencia
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection

@section('footer')
    @include('layout.footer')
@endsection


@section('css')
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .progress {
            background-color: #e9ecef;
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
        }

        .badge {
            font-size: 0.9em;
            padding: 0.5em 0.8em;
        }
    </style>
@stop
