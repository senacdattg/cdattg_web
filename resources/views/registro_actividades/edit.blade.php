@extends('adminlte::page')

@section('title', 'Gestión de Actividades')

@section('css')
@vite(['resources/css/Asistencia/caracter_selecter.css'])
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
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

@section('content_header')
    <x-page-header 
        icon="fa-calendar-alt" 
        title="Actividad"
        subtitle="Edición de la actividad"
        :breadcrumb="[['label' => 'Actividades', 'url' => route('registro-actividades.index', $caracterizacion) , 'icon' => 'fa-calendar-check'], ['label' => 'Editar actividad', 'icon' => 'fa-edit', 'active' => true]]"
    />
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
                        <form method="POST" action="{{ route('registro-actividades.update', [$caracterizacion, $actividad]) }}" class="row g-2" id="form-registro-actividad">
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
                                    <input type="text" name="nombre" id="nombre_actividad" value="{{ old('nombre', $actividad->nombre) }}" class="form-control" placeholder="Ingrese el nombre de la actividad">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Fecha de la actividad</label>
                                    <input type="text" name="fecha_evidencia" id="fecha_actividad" value="{{ old('fecha_evidencia', $actividad->fecha_evidencia ? \Carbon\Carbon::parse($actividad->fecha_evidencia)->format('Y-m-d') : '') }}" class="form-control" placeholder="Seleccione la fecha" readonly>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-calendar-alt"></i> Haz clic en el campo para abrir el calendario. Solo se muestran fechas válidas para formación.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Horas de la actividad</label>
                                    <input type="number" name="horas" id="horas_actividad" value="{{ old('horas', $actividad->horas) }}" class="form-control" placeholder="Ingrese las horas" min="1" max="8">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-clock"></i> Ingrese el número de horas de la actividad (máximo 8 horas por día).
                                    </small>
                                </div>
                            </div>

                            <!-- Información del día seleccionado -->
                            <div class="col-md-12" id="info-dia-seleccionado">
                                <!-- Se llenará dinámicamente -->
                            </div>

                            <!-- Información de actividad existente -->
                            <div class="col-md-12" id="info-actividad-existente">
                                <!-- Se llenará dinámicamente -->
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
@include('layouts.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/registro-actividades-form.js'])
@endsection

<script>
// Pasar datos del PHP al JavaScript
window.diasFormacion = @json($caracterizacion->instructorFichaDias->pluck('dia_id')->toArray());
window.fechaFinRap = @json($caracterizacion->ficha->programaFormacion->competenciaActual()->rapActual()->fecha_fin);
window.actividades = @json($actividades);
window.actividadActual = @json($actividad);
</script>
