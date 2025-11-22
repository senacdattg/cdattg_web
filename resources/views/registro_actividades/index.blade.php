@extends('adminlte::page')

@section('title', 'Gestión de Actividades')

@section('css')
    @vite(['resources/css/Asistencia/caracter_selecter.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-home" 
        title="Registro de actividades"
        subtitle="Gestión de actividades"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin') , 'icon' => 'fa-home'], ['label' => 'Registro de actividades', 'icon' => 'fa-fw fa-paint-brush', 'active' => true]]"
    />
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
                                $porcentajePendiente =
                                    $totalActividades > 0 ? ($totalPendientes / $totalActividades) * 100 : 0;
                                $porcentajePendiente = number_format($porcentajePendiente, 2);
                                $totalEnCurso = $actividades->where('id_estado', 'EN CURSO')->count();
                                $porcentajeEnCurso =
                                    $totalActividades > 0 ? ($totalEnCurso / $totalActividades) * 100 : 0;
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
                                $porcentajeCompletado =
                                    $totalActividades > 0 ? ($totalCompletadas / $totalActividades) * 100 : 0;
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
                                <i class="far fa-calendar-check me-1"></i> {{ $porcentajeCompletado }}% completadas esta
                                semana
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
                                            {{ $guiaAprendizajeActual->codigo}} EV-{{ $loop->iteration}}
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
                                            @if ($fechaActividad == $hoy)
                                                <a class="btn btn-success btn-block mb-1"
                                                    href="{{ route('asistence.caracterSelected', [$caracterizacion, $actividad]) }}">
                                                    <i class="fas fa-clipboard-check"></i> Tomar Asistencia
                                                </a>
                                            @else
                                                <button class="btn btn-outline-secondary btn-block mb-1" disabled>
                                                    <i class="fas fa-clipboard-check"></i> Tomar Asistencia
                                                </button>
                                            @endif
                                            <a class="btn btn-primary btn-block mb-1"
                                                href="{{ route('registro-actividades.edit', ['caracterizacion' => $caracterizacion, 'actividad' => $actividad]) }}">
                                                <i class="fas fa-edit"></i> Editar Actividad
                                            </a>

                                            <button type="button" class="btn btn-danger btn-block mb-1" 
                                                    data-toggle="modal" 
                                                    data-target="#cancelarActividadModal"
                                                    data-actividad-id="{{ $actividad->id }}"
                                                    data-actividad-nombre="{{ $actividad->nombre }}"
                                                    data-caracterizacion-id="{{ $caracterizacion->id }}">
                                                <i class="fas fa-times-circle"></i> Cancelar Actividad
                                            </button>
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

    <!-- Modal de Confirmación para Cancelar Actividad -->
    <div class="modal fade" id="cancelarActividadModal" tabindex="-1" role="dialog" aria-labelledby="cancelarActividadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title d-flex align-items-center" id="cancelarActividadModalLabel">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Confirmar Cancelación
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-danger-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 80px; height: 80px; background-color: rgba(220, 53, 69, 0.1);">
                            <i class="fas fa-times-circle text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                        <h4 class="text-gray-800 mb-3">¿Estás seguro de cancelar esta actividad?</h4>
                        <div class="alert alert-warning border-0 mb-3" style="background-color: rgba(255, 193, 7, 0.1);">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle text-warning mr-2"></i>
                                <span class="text-warning font-weight-bold" id="actividad-nombre-modal"></span>
                            </div>
                        </div>
                        <p class="text-muted mb-0">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Esta acción <strong>no se puede deshacer</strong> y eliminará permanentemente la actividad del sistema.
                        </p>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-4">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-arrow-left mr-1"></i> Cancelar
                    </button>
                    <form id="form-cancelar-actividad" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-lg px-4">
                            <i class="fas fa-times-circle mr-2"></i> Sí, Cancelar Actividad
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('layouts.footer')
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

        /* Ensure dropdown menus appear above other elements */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            z-index: 9999 !important;
            position: fixed !important;
            /* Changed from absolute to fixed */
            margin: 0;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            min-width: 200px;
            max-height: none !important;
            overflow: visible !important;
        }

        /* Reset any transform on the dropdown */
        .dropdown-menu.show {
            transform: none !important;
            top: auto !important;
            left: auto !important;
            right: auto !important;
            bottom: auto !important;
        }

        /* Make sure dropdown is not constrained by parent overflow */
        .activity-card,
        .card,
        .card-body,
        .col-12,
        .row,
        [class*="col-"],
        .content-wrapper,
        .content {
            overflow: visible !important;
            position: static !important;
        }

        /* Ensure dropdown items are properly displayed */
        .dropdown-item {
            white-space: normal;
            word-wrap: break-word;
            padding: 0.5rem 1.5rem;
        }

        /* Fix for Bootstrap's default dropdown behavior */
        .btn-group {
            position: static;
        }

        /* Ensure the dropdown menu is positioned correctly */
        .dropdown-toggle::after {
            vertical-align: middle;
        }

        /* Estilos para el modal de cancelación */
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }

        .modal-header {
            border-radius: 15px 15px 0 0;
        }

        .modal-footer {
            border-radius: 0 0 15px 15px;
        }

        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            transform: translateY(-1px);
            transition: all 0.3s ease;
        }

        /* Animaciones */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -30px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .animate__fadeInDown {
            animation: fadeInDown 0.5s ease-out;
        }

        /* Efecto de pulso para el ícono de advertencia */
        .fa-times-circle {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        /* Mejorar la apariencia del botón de cancelar actividad */
        .btn[data-target="#cancelarActividadModal"] {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn[data-target="#cancelarActividadModal"]:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
    </style>
@endsection

@section('js')
    @vite(['resources/js/pages/registro-actividades-index.js'])
@endsection
