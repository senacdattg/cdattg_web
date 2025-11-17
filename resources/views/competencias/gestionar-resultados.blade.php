@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title', 'Gestionar Resultados - Competencia')

@section('css')
    {{-- Select2 cargado por AdminLTE nativo --}}
    @vite(['resources/css/competencias.css'])
    <style>
        .dashboard-header {
            background: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, .03);
        }
        .text-gray-800 {
            color: #5a5c69 !important;
        }
        .link_right_header {
            color: #4a5568;
            text-decoration: none;
        }
        .link_right_header:hover {
            color: #4299e1;
        }
        .small-box {
            border-radius: 10px;
        }
        .small-box h3 {
            font-size: 2.2rem;
            font-weight: bold;
        }
        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #f8f9fa;
        }
        .select2-container--bootstrap4 .select2-selection {
            border-radius: 5px;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-tasks" 
        title="Gestionar Resultados"
        subtitle="{{ $competencia->codigo }} - {{ Str::limit($competencia->nombre, 50) }}"
        :breadcrumb="[['label' => 'Detalle', 'url' => route('competencias.show', $competencia->id) , 'icon' => 'fa-info-circle'], ['label' => 'Gestionar Resultados', 'icon' => 'fa-tasks', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('competencias.show', $competencia->id) }}">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Errores de validación:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $totalAsignados }}</h3>
                            <p>Resultados Asignados</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $totalDisponibles }}</h3>
                            <p>Resultados Disponibles</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ formatear_horas($duracionTotal) }}</h3>
                            <p>Horas Totales</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $competencia->status ? 'Activa' : 'Inactiva' }}</h3>
                            <p>Estado Competencia</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-{{ $competencia->status ? 'toggle-on' : 'toggle-off' }}"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Resultados de Aprendizaje Asignados -->
                <div class="col-md-7">
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-check-circle"></i> Resultados de Aprendizaje Asignados
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($resultadosAsignados->isEmpty())
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No hay resultados de aprendizaje asignados aún.
                                </div>
                            @else
                                <!-- Buscador de resultados asignados -->
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <input type="text" 
                                               class="form-control" 
                                               id="searchAsignados" 
                                               placeholder="Buscar en resultados asignados...">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fas fa-search"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                    <table class="table table-hover table-sm table-borderless table-striped" id="tablaAsignados">
                                        <thead class="thead-light sticky-top">
                                            <tr>
                                                <th class="px-4 py-3">Código</th>
                                                <th class="px-4 py-3">Nombre</th>
                                                <th class="px-4 py-3">Duración</th>
                                                <th width="80" class="px-4 py-3 text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($resultadosAsignados as $resultado)
                                                <tr>
                                                    <td class="px-4"><span class="badge badge-info">{{ $resultado->codigo }}</span></td>
                                                    <td class="px-4">{{ $resultado->nombre }}</td>
                                                    <td class="px-4">{{ formatear_horas($resultado->duracion) }} hrs</td>
                                                    <td class="px-4 text-center">
                                                        <form action="{{ route('competencias.desasociarResultado', [$competencia->id, $resultado->id]) }}" 
                                                              method="POST" 
                                                              class="d-inline form-desasociar"
                                                              data-codigo="{{ $resultado->codigo }}"
                                                              data-nombre="{{ $resultado->nombre }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-danger" 
                                                                    title="Desasociar"
                                                                    @cannot('GESTIONAR RESULTADOS COMPETENCIA') disabled @endcannot>
                                                                <i class="fas fa-unlink"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Total: <strong id="countAsignados">{{ $totalAsignados }}</strong> resultado(s) | 
                                        Duración total: <strong>{{ formatear_horas($duracionTotal) }}</strong> horas
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Resultados de Aprendizaje Disponibles -->
                <div class="col-md-5">
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-plus-circle"></i> Resultados Disponibles
                            </h6>
                        </div>
                        <div class="card-body">
                            @can('GESTIONAR RESULTADOS COMPETENCIA')
                                @if($resultadosDisponibles->isEmpty())
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i> No hay más resultados de aprendizaje disponibles para asignar.
                                    </div>
                                @else
                                    <form action="{{ route('competencias.asociarResultados', $competencia->id) }}" 
                                          method="POST" 
                                          id="formAsociarResultados">
                                        @csrf
                                        
                                        <div class="form-group">
                                            <label for="resultado_ids" class="font-weight-bold">
                                                Seleccionar Resultados de Aprendizaje <span class="text-danger">*</span>
                                            </label>
                                            <select name="resultado_ids[]" 
                                                    id="resultado_ids" 
                                                    class="form-control select2" 
                                                    required
                                                    multiple="multiple"
                                                    data-placeholder="Seleccione uno o más resultados...">
                                                @foreach($resultadosDisponibles as $resultado)
                                                    <option value="{{ $resultado->id }}" 
                                                            data-codigo="{{ $resultado->codigo }}"
                                                            data-duracion="{{ $resultado->duracion }}"
                                                            data-nombre="{{ $resultado->nombre }}">
                                                        {{ $resultado->codigo }} - {{ $resultado->nombre }} ({{ formatear_horas($resultado->duracion) }} hrs)
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('resultado_ids')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> Puede seleccionar múltiples resultados manteniendo presionado Ctrl (Cmd en Mac)
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success btn-block">
                                                <i class="fas fa-link"></i> Asociar Resultados Seleccionados
                                            </button>
                                        </div>
                                    </form>

                                    <hr>

                                    <!-- Buscador de resultados disponibles -->
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="searchDisponibles" 
                                                   placeholder="Buscar disponibles...">
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                                        <table class="table table-hover table-sm table-striped" id="tablaDisponibles">
                                            <thead class="thead-light sticky-top">
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Nombre</th>
                                                    <th>Hrs</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($resultadosDisponibles as $resultado)
                                                    <tr>
                                                        <td><span class="badge badge-secondary">{{ $resultado->codigo }}</span></td>
                                                        <td><small>{{ Str::limit($resultado->nombre, 30) }}</small></td>
                                                        <td><small>{{ formatear_horas($resultado->duracion) }}</small></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i> 
                                            Total: <strong id="countDisponibles">{{ $totalDisponibles }}</strong> resultado(s) disponible(s)
                                        </small>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-lock"></i> No tiene permisos para gestionar resultados de competencias.
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de la Competencia -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-clipboard-list"></i> Información de la Competencia
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <strong><i class="fas fa-barcode"></i> Código:</strong><br>
                                    <span class="badge badge-primary badge-lg">{{ $competencia->codigo }}</span>
                                </div>
                                <div class="col-md-4">
                                    <strong><i class="fas fa-tag"></i> Nombre:</strong><br>
                                    {{ $competencia->nombre }}
                                </div>
                                <div class="col-md-2">
                                    <strong><i class="fas fa-clock"></i> Duración:</strong><br>
                                    {{ formatear_horas($competencia->duracion) }} horas
                                </div>
                                <div class="col-md-2">
                                    <strong><i class="fas fa-toggle-on"></i> Estado:</strong><br>
                                    @if($competencia->status)
                                        <span class="badge badge-success">Activa</span>
                                    @else
                                        <span class="badge badge-danger">Inactiva</span>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <strong><i class="fas fa-user"></i> Creado por:</strong><br>
                                    {{ $competencia->userCreate->name ?? 'N/A' }}
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <strong><i class="fas fa-calendar-alt"></i> Fecha Inicio:</strong><br>
                                    {{ $competencia->fecha_inicio ? $competencia->fecha_inicio->format('d/m/Y') : 'N/A' }}
                                </div>
                                <div class="col-md-3">
                                    <strong><i class="fas fa-calendar-check"></i> Fecha Fin:</strong><br>
                                    {{ $competencia->fecha_fin ? $competencia->fecha_fin->format('d/m/Y') : 'N/A' }}
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-align-left"></i> Descripción:</strong><br>
                                    {{ Str::limit($competencia->descripcion, 100) ?? 'Sin descripción' }}
                                </div>
                            </div>
                        </div>
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
    @vite(['resources/js/pages/gestion-especializada.js'])
@endsection