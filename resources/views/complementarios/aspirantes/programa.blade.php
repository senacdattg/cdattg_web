@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('title', 'Aspirantes - ' . $programa->nombre)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="mb-1">
                <i class="fas fa-users mr-2 text-primary"></i>{{ $programa->nombre }}
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-graduation-cap mr-1"></i>Gestión de aspirantes del programa complementario
            </p>
        </div>
        <a href="{{ route('aspirantes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>
@stop

@section('content')
    @php
        $totalAspirantes = $aspirantes->count();
        $enProceso = $aspirantes->where('estado', 1)->count();
        $aceptados = $aspirantes->where('estado', 3)->count();
        $rechazados = $aspirantes->where('estado', 2)->count();
        $conDocumento = $aspirantes->filter(fn($a) => $a->persona->condocumento == 1)->count();
    @endphp

    <!-- Información del Programa -->
    <div class="card border-left-primary shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <i class="{{ $programa->icono ?? 'fas fa-graduation-cap' }} fa-4x text-primary"></i>
                </div>
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-dark mb-2">{{ $programa->nombre }}</h5>
                    <p class="text-muted mb-2 small">{{ Str::limit($programa->descripcion ?? 'Sin descripción', 200) }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        @if(isset($programa->modalidad) && $programa->modalidad)
                            <span class="badge badge-light border">
                                <i class="fas fa-chalkboard-teacher text-primary mr-1"></i>
                                {{ optional($programa->modalidad->parametro)->name ?? 'N/A' }}
                            </span>
                        @endif
                        @if(isset($programa->jornada) && $programa->jornada)
                            <span class="badge badge-light border">
                                <i class="fas fa-clock text-primary mr-1"></i>
                                {{ $programa->jornada->jornada ?? 'N/A' }}
                            </span>
                        @endif
                        <span class="badge badge-pill {{ $programa->badge_class ?? 'bg-secondary' }}">
                            {{ $programa->estado_label ?? 'N/A' }}
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-2 bg-light">
                                <div class="h4 mb-0 text-primary font-weight-bold">{{ $totalAspirantes }}</div>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-2 bg-light">
                                <div class="h4 mb-0 text-success font-weight-bold">{{ $aceptados }}</div>
                                <small class="text-muted">Aceptados</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 bg-light">
                                <div class="h4 mb-0 text-warning font-weight-bold">{{ $enProceso }}</div>
                                <small class="text-muted">En Proceso</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 bg-light">
                                <div class="h4 mb-0 text-danger font-weight-bold">{{ $rechazados }}</div>
                                <small class="text-muted">Rechazados</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap align-items-center" style="gap: 1rem;">
                        <!-- Grupo: Agregar -->
                        <div class="d-flex align-items-center" style="gap: 0.5rem;">
                            <button class="btn btn-primary" id="btn-agregar-aprendiz"
                                @if(isset($existingProgress) && $existingProgress) disabled @endif
                                onclick="$('#modalAgregarAprendiz').modal('show');">
                                <i class="fas fa-user-plus me-1"></i>Agregar Aspirante
                            </button>
                        </div>

                        <!-- Separador visual -->
                        <div class="vr" style="height: 35px; opacity: 0.3;"></div>

                        <!-- Grupo: Exportar/Descargar -->
                        <div class="d-flex align-items-center" style="gap: 0.5rem;">
                            <a href="{{ route('aspirantes.exportar-excel', $programa->id) }}"
                                class="btn btn-success"
                                id="btn-descargar-excel"
                                @if(isset($existingProgress) && $existingProgress) style="pointer-events: none; opacity: 0.5;" @endif>
                                <i class="fas fa-download me-1"></i>Exportar Excel
                            </a>
                            <a href="{{ route('aspirantes.descargar-cedulas', $programa->id) }}"
                                class="btn btn-info"
                                id="btn-descargar-cedulas"
                                @if(isset($existingProgress) && $existingProgress) style="pointer-events: none; opacity: 0.5;" @endif>
                                <i class="fas fa-file-pdf me-1"></i>Descargar Cédulas
                            </a>
                        </div>

                        <!-- Separador visual -->
                        <div class="vr ml-auto" style="height: 35px; opacity: 0.3;"></div>

                        <!-- Grupo: Validaciones -->
                        <div class="d-flex align-items-center" style="gap: 0.5rem;">
                            <button class="btn btn-outline-primary btn-sm" id="btn-validar-sofia"
                                data-programa-id="{{ $programa->id }}"
                                @if(isset($existingProgress) && $existingProgress) disabled @endif>
                                <i class="fas fa-search me-1"></i>Validar SenaSofiaPlus
                            </button>
                            <button class="btn btn-outline-info btn-sm" id="btn-validar-documento"
                                data-programa-id="{{ $programa->id }}"
                                @if(isset($existingProgress) && $existingProgress) disabled @endif>
                                <i class="fas fa-file-pdf me-1"></i>Validar Documentos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter mr-2 text-primary"></i>Filtros y Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5 mb-3 mb-md-0">
                    <label class="form-label font-weight-bold">
                        <i class="fas fa-search mr-1"></i>Buscar Aspirante
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="buscar-aspirante"
                           placeholder="Buscar por nombre, documento o email...">
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <label class="form-label font-weight-bold">
                        <i class="fas fa-tag mr-1"></i>Filtrar por Estado
                    </label>
                    <select class="form-control" id="filtro-estado">
                        <option value="">Todos los estados</option>
                        <option value="1">En Proceso</option>
                        <option value="3">Aceptados</option>
                        <option value="2">Rechazados</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <label class="form-label font-weight-bold">
                        <i class="fas fa-file-pdf mr-1"></i>Documento
                    </label>
                    <select class="form-control" id="filtro-documento">
                        <option value="">Todos</option>
                        <option value="1">Con Documento</option>
                        <option value="0">Sin Documento</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end mb-3 mb-md-0">
                    <button class="btn btn-outline-secondary w-100" id="limpiar-filtros">
                        <i class="fas fa-redo me-1"></i>Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Aspirantes -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-list mr-2 text-primary"></i>Lista de Aspirantes
                <span class="badge badge-primary ml-2" id="contador-aspirantes">{{ $totalAspirantes }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" id="aspirantes-table">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%">Nombre Completo</th>
                            <th width="12%">N# Documento</th>
                            <th width="10%">Fecha Solicitud</th>
                            <th width="10%">Estado</th>
                            <th width="12%">SenaSofiaPlus</th>
                            <th width="12%"> PDF Documento</th>
                            <th width="10%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aspirantes as $index => $aspirante)
                        <tr data-estado="{{ $aspirante->estado }}" 
                            data-documento="{{ $aspirante->persona->condocumento }}"
                            data-nombre="{{ strtolower(trim(($aspirante->persona->primer_nombre ?? '') . ' ' . ($aspirante->persona->segundo_nombre ?? '') . ' ' . ($aspirante->persona->primer_apellido ?? '') . ' ' . ($aspirante->persona->segundo_apellido ?? ''))) }}"
                            data-documento-numero="{{ strtolower($aspirante->persona->numero_documento ?? '') }}"
                            data-email="{{ strtolower($aspirante->persona->email ?? '') }}">
                            <td class="align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle">
                                <div class="font-weight-semibold">
                                    {{ trim(($aspirante->persona->primer_nombre ?? '') . ' ' . ($aspirante->persona->segundo_nombre ?? '') . ' ' . ($aspirante->persona->primer_apellido ?? '') . ' ' . ($aspirante->persona->segundo_apellido ?? '')) }}
                                </div>
                                @if($aspirante->persona->email)
                                    <small class="text-muted">
                                        <i class="fas fa-envelope mr-1"></i>{{ $aspirante->persona->email }}
                                    </small>
                                @endif
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-light border font-weight-normal">
                                    <i class="fas fa-id-card mr-1 text-primary"></i>
                                    {{ $aspirante->persona->numero_documento }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <i class="fas fa-calendar-alt mr-1 text-muted"></i>
                                <small>{{ $aspirante->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="align-middle">
                                @if($aspirante->estado == 1)
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-clock mr-1"></i>EN PROCESO
                                    </span>
                                @elseif($aspirante->estado == 2)
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle mr-1"></i>RECHAZADO
                                    </span>
                                @elseif($aspirante->estado == 3)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle mr-1"></i>ACEPTADO
                                    </span>
                                @else
                                    <span class="badge bg-secondary">DESCONOCIDO</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <span class="badge {{ $aspirante->persona->estado_sofia_badge_class ?? 'bg-secondary' }}">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    {{ $aspirante->persona->estado_sofia_label ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="align-middle">
                                @if($aspirante->persona->condocumento == 1)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle mr-1"></i>Subido
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times-circle mr-1"></i>No subido
                                    </span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-danger aspirante-action-btn" 
                                        title="Rechazar aspirante"
                                        data-aspirante-id="{{ $aspirante->id }}"
                                        data-aspirante-nombre="{{ trim(($aspirante->persona->primer_nombre ?? '') . ' ' . ($aspirante->persona->primer_apellido ?? '')) }}"
                                        @if(isset($existingProgress) && $existingProgress) disabled @endif>
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @can('VER PERSONA')
                                        <a href="{{ route('personas.show', $aspirante->persona->id) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Ver perfil completo"
                                           target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-inbox fa-4x text-muted opacity-50 mb-3"></i>
                                    <h5 class="text-muted font-weight-bold">No hay aspirantes registrados</h5>
                                    <p class="text-muted">Este programa aún no tiene aspirantes inscritos.</p>
                                    <button class="btn btn-primary mt-2" onclick="$('#modalAgregarAprendiz').modal('show');">
                                        <i class="fas fa-user-plus me-1"></i>Agregar Primer Aspirante
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Aprendiz -->
    @include('complementarios.aspirantes.partials.modal-agregar-aprendiz')

@stop

@section('css')
<link rel="stylesheet" href="{{ asset('resources/css/complementario/ver_aspirantes.css') }}">
<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }

    .thead-light {
        background-color: #f8f9fa;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
        cursor: pointer;
    }

    #aspirantes-table tbody tr.hidden-row {
        display: none !important;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.9; }
    }
    
    .sena-loading-logo {
        animation: pulse 2s ease-in-out infinite;
        max-width: 120px;
        height: auto;
    }
</style>
@stop

@section('js')
    <!-- CSRF Token para AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Configuración para pasar datos de PHP a JavaScript -->
    <script>
        window.aspirantesConfig = {
            hasAspirantes: {{ $aspirantes->count() > 0 ? 'true' : 'false' }},
            existingProgressId: {{ isset($existingProgress) && $existingProgress ? $existingProgress->id : 'null' }},
            programaId: {{ $programa->id }},
            routes: {
                buscarPersona: '{{ route("aspirantes.buscar-persona") }}',
                create: '{{ route("aspirantes.create", ["programa" => $programa->id]) }}',
                agregarExistente: '{{ route("aspirantes.agregar-existente", ["complementarioId" => $programa->id]) }}',
                destroy: '{{ route("aspirantes.destroy", ["complementarioId" => $programa->id, "aspiranteId" => "__ID__"]) }}',
                validarDocumento: '{{ route("programas-complementarios.validar-documento", ["programa" => $programa->id]) }}',
                validarSofia: '{{ route("programas-complementarios.validar-sofia", ["programa" => $programa->id]) }}'
            }
        };
    </script>
    
    @vite(['resources/js/complementarios/aspirantes/programa.js'])
@stop
