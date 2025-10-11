@extends('adminlte::page')

@section('title', 'Gestionar Resultados - Competencia')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
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
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-tasks text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Gestionar Resultados</h1>
                        <p class="text-muted mb-0 font-weight-light">{{ $competencia->codigo }} - {{ Str::limit($competencia->nombre, 50) }}</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                            <li class="breadcrumb-item">
                                <a href="{{ url('/') }}" class="link_right_header">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('competencias.index') }}" class="link_right_header">
                                    <i class="fas fa-clipboard-list"></i> Competencias
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('competencias.show', $competencia->id) }}" class="link_right_header">
                                    <i class="fas fa-info-circle"></i> Detalle
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-tasks"></i> Gestionar Resultados
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
                            <h3>{{ number_format($duracionTotal, 0) }}</h3>
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
                                                    <td class="px-4">{{ $resultado->duracion }} hrs</td>
                                                    <td class="px-4 text-center">
                                                        <form action="{{ route('competencias.desasociarResultado', [$competencia->id, $resultado->id]) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('¿Está seguro de desasociar este resultado de aprendizaje?');">
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
                                        Duración total: <strong>{{ number_format($duracionTotal, 0) }}</strong> horas
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
                                    <form action="{{ route('competencias.asociarResultado', $competencia->id) }}" 
                                          method="POST" 
                                          id="formAsociarResultado">
                                        @csrf
                                        
                                        <div class="form-group">
                                            <label for="resultado_id" class="font-weight-bold">
                                                Seleccionar Resultado de Aprendizaje <span class="text-danger">*</span>
                                            </label>
                                            <select name="resultado_id" 
                                                    id="resultado_id" 
                                                    class="form-control select2" 
                                                    required
                                                    data-placeholder="Seleccione un resultado...">
                                                <option value="">-- Seleccione un resultado --</option>
                                                @foreach($resultadosDisponibles as $resultado)
                                                    <option value="{{ $resultado->id }}" 
                                                            data-codigo="{{ $resultado->codigo }}"
                                                            data-duracion="{{ $resultado->duracion }}">
                                                        {{ $resultado->codigo }} - {{ $resultado->nombre }} ({{ $resultado->duracion }} hrs)
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('resultado_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success btn-block">
                                                <i class="fas fa-link"></i> Asociar Resultado
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
                                                        <td><small>{{ $resultado->duracion }}</small></td>
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
                                    {{ $competencia->duracion }} horas
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
    @include('layout.footer')
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Seleccione un resultado...',
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

            // Búsqueda en tiempo real - Resultados Asignados
            $('#searchAsignados').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                var count = 0;
                
                $('#tablaAsignados tbody tr').filter(function() {
                    var match = $(this).text().toLowerCase().indexOf(value) > -1;
                    $(this).toggle(match);
                    if (match) count++;
                });
                
                $('#countAsignados').text(count);
            });

            // Búsqueda en tiempo real - Resultados Disponibles
            $('#searchDisponibles').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                var count = 0;
                
                $('#tablaDisponibles tbody tr').filter(function() {
                    var match = $(this).text().toLowerCase().indexOf(value) > -1;
                    $(this).toggle(match);
                    if (match) count++;
                });
                
                $('#countDisponibles').text(count);
            });

            // Auto-dismiss alerts después de 5 segundos
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Confirmación antes de asociar
            $('#formAsociarResultado').on('submit', function(e) {
                var selectedOption = $('#resultado_id option:selected');
                var codigo = selectedOption.data('codigo');
                var duracion = selectedOption.data('duracion');
                
                if (!codigo) {
                    e.preventDefault();
                    alert('Debe seleccionar un resultado de aprendizaje');
                    return false;
                }
                
                var confirmMsg = '¿Está seguro de asociar el resultado ' + codigo + ' (' + duracion + ' horas)?';
                if (!confirm(confirmMsg)) {
                    e.preventDefault();
                    return false;
                }
            });

            // Mantener filtros en URL
            function updateURLParameter(url, param, paramVal) {
                var newAdditionalURL = "";
                var tempArray = url.split("?");
                var baseURL = tempArray[0];
                var additionalURL = tempArray[1];
                var temp = "";
                
                if (additionalURL) {
                    tempArray = additionalURL.split("&");
                    for (var i=0; i<tempArray.length; i++){
                        if(tempArray[i].split('=')[0] != param){
                            newAdditionalURL += temp + tempArray[i];
                            temp = "&";
                        }
                    }
                }
                
                var rows_txt = temp + "" + param + "=" + paramVal;
                return baseURL + "?" + newAdditionalURL + rows_txt;
            }
        });
    </script>
@endsection
