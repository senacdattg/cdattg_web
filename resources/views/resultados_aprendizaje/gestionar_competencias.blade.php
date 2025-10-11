@extends('adminlte::page')

@section('title', 'Gestionar Competencias - RAP')

@section('content_header')
    <div class="content-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-8">
                    <h1 class="m-0" style="color: white; font-weight: 700; font-size: 2rem;">
                        <i class="fas fa-link"></i> Gestionar Competencias
                    </h1>
                    <p class="mt-2 mb-0" style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">
                        {{ $resultadoAprendizaje->codigo }} - {{ $resultadoAprendizaje->nombre }}
                    </p>
                </div>
                <div class="col-sm-4 text-right">
                    <a href="{{ route('resultados-aprendizaje.show', $resultadoAprendizaje->id) }}" 
                       class="btn btn-light btn-lg shadow-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .content-header h1 { text-shadow: 2px 2px 4px rgba(0,0,0,0.2); }
        .btn-light:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s ease; }
    </style>
@stop

@section('content')
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

    <div class="row">
        <!-- Competencias Asignadas -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success">
                    <h3 class="card-title text-white">
                        <i class="fas fa-check-circle"></i> Competencias Asignadas
                    </h3>
                </div>
                <div class="card-body">
                    @if($competenciasAsignadas->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No hay competencias asignadas aún.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th width="100" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($competenciasAsignadas as $competencia)
                                        <tr>
                                            <td><span class="badge badge-info">{{ $competencia->codigo }}</span></td>
                                            <td>{{ $competencia->nombre }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('resultados-aprendizaje.desasociarCompetencia', [$resultadoAprendizaje->id, $competencia->id]) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Está seguro de desasociar esta competencia?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Desasociar">
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
                                Total: <strong>{{ $competenciasAsignadas->count() }}</strong> competencia(s) asignada(s)
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Competencias Disponibles -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <h3 class="card-title text-white">
                        <i class="fas fa-plus-circle"></i> Competencias Disponibles
                    </h3>
                </div>
                <div class="card-body">
                    @if($competenciasDisponibles->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> No hay más competencias disponibles para asignar.
                        </div>
                    @else
                        <form action="{{ route('resultados-aprendizaje.asociarCompetencia', $resultadoAprendizaje->id) }}" 
                              method="POST" 
                              id="formAsociarCompetencia">
                            @csrf
                            
                            <div class="form-group">
                                <label for="competencia_id">
                                    Seleccionar Competencia <span class="text-danger">*</span>
                                </label>
                                <select name="competencia_id" 
                                        id="competencia_id" 
                                        class="form-control select2" 
                                        required
                                        data-placeholder="Seleccione una competencia...">
                                    <option value="">-- Seleccione una competencia --</option>
                                    @foreach($competenciasDisponibles as $competencia)
                                        <option value="{{ $competencia->id }}">
                                            {{ $competencia->codigo }} - {{ $competencia->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('competencia_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-link"></i> Asociar Competencia
                                </button>
                            </div>
                        </form>

                        <div class="mt-3">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-hover table-sm table-striped">
                                    <thead class="thead-light sticky-top">
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($competenciasDisponibles as $competencia)
                                            <tr>
                                                <td><span class="badge badge-secondary">{{ $competencia->codigo }}</span></td>
                                                <td>{{ $competencia->nombre }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Total: <strong>{{ $competenciasDisponibles->count() }}</strong> competencia(s) disponible(s)
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Información del RAP -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info">
                    <h3 class="card-title text-white">
                        <i class="fas fa-graduation-cap"></i> Información del Resultado de Aprendizaje
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong><i class="fas fa-barcode"></i> Código:</strong><br>
                            <span class="badge badge-primary badge-lg">{{ $resultadoAprendizaje->codigo }}</span>
                        </div>
                        <div class="col-md-5">
                            <strong><i class="fas fa-tag"></i> Nombre:</strong><br>
                            {{ $resultadoAprendizaje->nombre }}
                        </div>
                        <div class="col-md-2">
                            <strong><i class="fas fa-clock"></i> Duración:</strong><br>
                            {{ formatear_horas($resultadoAprendizaje->duracion) }} horas
                        </div>
                        <div class="col-md-2">
                            <strong><i class="fas fa-toggle-on"></i> Estado:</strong><br>
                            @if($resultadoAprendizaje->status)
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-danger">Inactivo</span>
                            @endif
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fas fa-calendar-alt"></i> Fecha Inicio:</strong><br>
                            {{ $resultadoAprendizaje->fecha_inicio ? $resultadoAprendizaje->fecha_inicio->format('d/m/Y') : 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-calendar-check"></i> Fecha Fin:</strong><br>
                            {{ $resultadoAprendizaje->fecha_fin ? $resultadoAprendizaje->fecha_fin->format('d/m/Y') : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    <style>
        .card {
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        .table-responsive {
            border-radius: 5px;
        }
        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 1rem;
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
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Seleccione una competencia...',
                allowClear: true,
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });

            // Auto-dismiss alerts después de 5 segundos
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@stop

