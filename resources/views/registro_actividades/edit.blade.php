@extends('adminlte::page')

@section('title', 'Gestión de Actividades')

@section('css')
@vite(['resources/css/Asistencia/caracter_selecter.css'])
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-calendar-alt text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Actividad</h1>
                        <p class="text-muted mb-0 font-weight-light">Edición de la actividad</p>
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
                                <a href="{{ route('registro-actividades.index', $caracterizacion) }}" class="link_right_header">
                                    <i class="fas fa-calendar-check"></i> Actividades
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-edit"></i> Editar actividad
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
        <div class="row">
            <div class="col-12">
                <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('registro-actividades.index', $caracterizacion) }}">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>

                <div class="card shadow-sm no-hover">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title m-0 font-weight-bold text-primary">
                            <i class="fas fa-edit mr-2"></i>Editar Actividad
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('registro-actividades.update', [$caracterizacion, $actividad]) }}" class="row g-2">
                            @csrf
                            @method('PUT')
    <!-- Información del RAP actual -->
    <div class="col-md-12 mb-3">
        <div class="card border-warning shadow-sm">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-calendar-times mr-2"></i>
                <strong>Resultado de Aprendizaje actual</strong>
            </div>
            <div class="card-body">
                @if($rapActual)
                <div class="row">
                    <div class="col-md-6">
                        <strong>RAP:</strong> {{ $rapActual->codigo }} - {{ $rapActual->nombre }}
                    </div>
                    <div class="col-md-3">
                        <strong>Inicio:</strong> {{ \Carbon\Carbon::parse($rapActual->fecha_inicio)->format('d/m/Y') }}
                    </div>
                    <div class="col-md-3">
                        <strong>Fin:</strong> {{ \Carbon\Carbon::parse($rapActual->fecha_fin)->format('d/m/Y') }}
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">
                    <i class="fas fa-info-circle"></i> Solo podrás crear actividades hasta el {{ \Carbon\Carbon::parse($rapActual->fecha_fin)->format('d/m/Y') }}.
                </small>
                @else
                <div class="text-muted">
                    <i class="fas fa-exclamation-triangle"></i> No hay un RAP activo configurado para el período actual.
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="form-label fw-bold">Nombre de la actividad</label>
            <input type="text" name="nombre" value="{{ old('nombre', $actividad->nombre) }}" class="form-control" placeholder="Ingrese el nombre">
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="form-label fw-bold">Fecha de la actividad</label>
            <input type="text" name="fecha_evidencia" id="fecha_evidencia" value="{{ old('fecha_evidencia', $actividad->fecha_evidencia ? \Carbon\Carbon::parse($actividad->fecha_evidencia)->format('Y-m-d') : '') }}" class="form-control" placeholder="Seleccione la fecha" readonly>
            <small class="form-text text-muted">
                <i class="fas fa-calendar-alt"></i> Haz clic en el campo para abrir el calendario. Solo se muestran fechas válidas para formación.
            </small>
        </div>
    </div>

                            <div class="col-12 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>Actualizar Actividad
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
@include('layout.footer')
@endsection

@section('css')
<style>
    /* Estilos para Flatpickr */
    .flatpickr-input {
        cursor: pointer !important;
    }

    .flatpickr-input:focus {
        border-color: #17a2b8;
        box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
    }

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
        margin: 0;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        min-width: 200px;
        max-height: none !important;
        overflow: visible !important;
    }

    .dropdown-menu.show {
        transform: none !important;
        top: auto !important;
        left: auto !important;
        right: auto !important;
        bottom: auto !important;
    }

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

    .dropdown-item {
        white-space: normal;
        word-wrap: break-word;
        padding: 0.5rem 1.5rem;
    }

    .btn-group {
        position: static;
    }

    .dropdown-toggle::after {
        vertical-align: middle;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuración inicial
        const CONFIG = {
            diasFormacion: @json($caracterizacion->instructorFichaDias->pluck('dia_id')->toArray()),
            fechaFinRap: @json($caracterizacion->ficha->programaFormacion->competenciaActual()->rapActual()->fecha_fin),
            fechaHoy: @json(\Carbon\Carbon::now()->format('Y-m-d')),
            actividades: @json($actividades)
        };

        // Mapeo de días de la semana
        const DIAS_SEMANA = {
            DOMINGO: 18,
            LUNES: 12,
            MARTES: 13,
            MIERCOLES: 14,
            JUEVES: 15,
            VIERNES: 16,
            SABADO: 17
        };

        // Utilidades de fecha
        const DateUtils = {
            toDateString: (date) => date.toISOString().split('T')[0],

            getDiaId: (fecha) => {
                const diaSemana = fecha.getDay();
                return diaSemana === 0 ? DIAS_SEMANA.DOMINGO : diaSemana + 11;
            },

            esDiaFormacion: (fecha) => {
                const diaId = DateUtils.getDiaId(fecha);
                return CONFIG.diasFormacion.includes(diaId);
            },

            esFechaValida: (fecha) => {
                const fechaHoy = new Date(CONFIG.fechaHoy);
                const fechaFinRap = CONFIG.fechaFinRap ? new Date(CONFIG.fechaFinRap) : null;

                const esPasada = fecha < fechaHoy;
                const esDespuesRap = fechaFinRap && DateUtils.toDateString(fecha) > DateUtils.toDateString(fechaFinRap);
                const esDiaFormacion = DateUtils.esDiaFormacion(fecha);
                const esFechaOcupada = CONFIG.actividades.some(actividad =>
                    DateUtils.toDateString(fecha) === DateUtils.toDateString(new Date(actividad.fecha_evidencia))
                );

                return !esPasada && !esDespuesRap && esDiaFormacion && !esFechaOcupada;
            }
        };

        // Generar fechas permitidas
        const fechasPermitidas = [];
        const fechaInicio = new Date(CONFIG.fechaHoy);
        const fechaFin = CONFIG.fechaFinRap ? new Date(CONFIG.fechaFinRap) : new Date(fechaInicio.getTime() + (365 * 24 * 60 * 60 * 1000)); // 1 año desde hoy

        for (let fecha = new Date(fechaInicio); fecha <= fechaFin; fecha.setDate(fecha.getDate() + 1)) {
            if (DateUtils.esFechaValida(new Date(fecha))) {
                fechasPermitidas.push(DateUtils.toDateString(new Date(fecha)));
            }
        }

        // Obtener fecha actual del input (para edición)
        const fechaActual = document.getElementById('fecha_evidencia').value;
        
        // Configurar Flatpickr
        const flatpickrInstance = flatpickr("#fecha_evidencia", {
            locale: "es",
            dateFormat: "Y-m-d",
            enable: fechasPermitidas,
            defaultDate: fechaActual || null, // Usar fecha actual si existe
            disable: [
                function(date) {
                    return !DateUtils.esFechaValida(date);
                }
            ],
            minDate: CONFIG.fechaHoy,
            maxDate: CONFIG.fechaFinRap,
            allowInput: true,
            clickOpens: true,
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const fechaSeleccionada = selectedDates[0];
                    if (!DateUtils.esFechaValida(fechaSeleccionada)) {
                        instance.clear();
                        alert('La fecha seleccionada no es válida para formación.');
                    }
                }
            },
            onOpen: function(selectedDates, dateStr, instance) {
                console.log('Calendario abierto - Fechas permitidas:', fechasPermitidas.length);
            }
        });

        // Validación del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const inputFecha = document.getElementById('fecha_evidencia');
            
            if (!inputFecha.value) {
                e.preventDefault();
                alert('Por favor, seleccione una fecha para la actividad.');
                return false;
            }

            const fechaSeleccionada = new Date(inputFecha.value);
            if (!DateUtils.esFechaValida(fechaSeleccionada)) {
                e.preventDefault();
                alert('La fecha seleccionada no es válida. Verifique que sea un día de formación disponible.');
                return false;
            }
        });

        // Validación inicial
                if (CONFIG.diasFormacion.length === 0) {
                    alert('ADVERTENCIA: No hay días de formación configurados para este instructor.');
                }

                console.log('Configuración cargada:', {
                    diasFormacion: CONFIG.diasFormacion,
                    fechaFinRap: CONFIG.fechaFinRap,
            fechasPermitidas: fechasPermitidas.length
                });
    });
</script>
@endsection