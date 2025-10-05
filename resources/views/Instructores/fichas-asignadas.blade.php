@extends('adminlte::page')

@section('title', 'Fichas Asignadas - ' . $instructorActual->nombre_completo)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-clipboard-list mr-2"></i>
                Fichas Asignadas
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb breadcrumb-custom float-sm-right">
                <li class="breadcrumb-item">
                    <a href="{{ route('home.index') }}">
                        <i class="fas fa-home mr-1"></i>Inicio
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('instructor.index') }}">
                        <i class="fas fa-chalkboard-teacher mr-1"></i>Instructores
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('instructor.show', $instructorActual->id) }}">
                        {{ $instructorActual->nombre_completo }}
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fas fa-clipboard-list mr-1"></i>Fichas Asignadas
                </li>
            </ol>
        </div>
    </div>
@stop

@section('css')
    <style>
        .stats-card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: none;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .stats-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .stats-success { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
        .stats-warning { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
        .stats-info { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; }
        
        .ficha-card {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid #007bff;
        }
        
        .ficha-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }
        
        .ficha-card.activa { border-left-color: #28a745; }
        .ficha-card.finalizada { border-left-color: #6c757d; }
        .ficha-card.inactiva { border-left-color: #dc3545; }
        
        .status-badge {
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-activa { background: #d4edda; color: #155724; }
        .badge-finalizada { background: #e2e3e5; color: #383d41; }
        .badge-inactiva { background: #f8d7da; color: #721c24; }
        
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .info-item i {
            width: 20px;
            margin-right: 0.5rem;
            color: #6c757d;
        }
        
        .filter-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
        }
        
        .btn-filter {
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .progress-mini {
            height: 4px;
            border-radius: 2px;
            background: #e9ecef;
        }
        
        .progress-mini .progress-bar {
            border-radius: 2px;
        }
        
        .progress-bar-custom {
            transition: width 0.6s ease;
        }
        
        .instructor-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Información del Instructor -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="instructor-info">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <i class="fas fa-user-circle" style="font-size: 3rem; opacity: 0.8;"></i>
                        </div>
                        <div class="col-md-8">
                            <h4 class="mb-1">{{ $instructorActual->nombre_completo }}</h4>
                            <p class="mb-1"><strong>Documento:</strong> {{ $instructorActual->numero_documento }}</p>
                            <p class="mb-0"><strong>Regional:</strong> {{ $instructorActual->regional->nombre ?? 'Sin asignar' }}</p>
                        </div>
                        <div class="col-md-2 text-right">
                            <a href="{{ route('instructor.show', $instructorActual->id) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-eye mr-1"></i>Ver Perfil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card stats-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-clipboard-list" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <h4 class="mb-1">{{ $estadisticas['total'] }}</h4>
                        <p class="mb-0">Total Fichas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card stats-success">
                    <div class="card-body text-center">
                        <i class="fas fa-play-circle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <h4 class="mb-1">{{ $estadisticas['activas'] }}</h4>
                        <p class="mb-0">Fichas Activas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card stats-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <h4 class="mb-1">{{ $estadisticas['finalizadas'] }}</h4>
                        <p class="mb-0">Fichas Finalizadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card stats-info">
                    <div class="card-body text-center">
                        <i class="fas fa-clock" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <h4 class="mb-1">{{ number_format($estadisticas['total_horas']) }}</h4>
                        <p class="mb-0">Total Horas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-card p-3">
                    <form method="GET" action="{{ request()->url() }}" class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-control">
                                <option value="todas" {{ $filtroEstado === 'todas' ? 'selected' : '' }}>Todas las fichas</option>
                                <option value="activas" {{ $filtroEstado === 'activas' ? 'selected' : '' }}>Solo activas</option>
                                <option value="finalizadas" {{ $filtroEstado === 'finalizadas' ? 'selected' : '' }}>Solo finalizadas</option>
                                <option value="inactivas" {{ $filtroEstado === 'inactivas' ? 'selected' : '' }}>Solo inactivas</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="{{ $filtroFechaInicio }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control" value="{{ $filtroFechaFin }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Programa</label>
                            <select name="programa" class="form-control">
                                <option value="">Todos los programas</option>
                                @foreach($programas as $programa)
                                    <option value="{{ $programa }}" {{ $filtroPrograma === $programa ? 'selected' : '' }}>
                                        {{ $programa }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-filter">
                                <i class="fas fa-search mr-1"></i>Filtrar
                            </button>
                            <a href="{{ request()->url() }}" class="btn btn-secondary btn-filter ml-1">
                                <i class="fas fa-times mr-1"></i>Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lista de Fichas -->
        <div class="row">
            @forelse($fichasAsignadas as $instructorFicha)
                @php
                    $ficha = $instructorFicha->ficha;
                    $esActiva = $ficha->status && $ficha->fecha_fin >= now()->toDateString();
                    $esFinalizada = $ficha->fecha_fin < now()->toDateString();
                    $esInactiva = !$ficha->status;
                    
                    $diasTranscurridos = now()->diffInDays($ficha->fecha_inicio);
                    $diasTotales = $ficha->fecha_inicio->diffInDays($ficha->fecha_fin);
                    $progreso = $diasTotales > 0 ? min(($diasTranscurridos / $diasTotales) * 100, 100) : 0;
                    $progresoWidth = number_format($progreso, 1);
                @endphp
                
                <div class="col-md-6 mb-4">
                    <div class="card ficha-card {{ $esActiva ? 'activa' : ($esFinalizada ? 'finalizada' : 'inactiva') }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="mb-1">{{ $ficha->programaFormacion->nombre ?? 'Sin programa' }}</h5>
                                    <p class="mb-1 text-muted">Ficha: {{ $ficha->ficha }}</p>
                                </div>
                                <span class="status-badge {{ $esActiva ? 'badge-activa' : ($esFinalizada ? 'badge-finalizada' : 'badge-inactiva') }}">
                                    @if($esActiva)
                                        <i class="fas fa-play mr-1"></i>Activa
                                    @elseif($esFinalizada)
                                        <i class="fas fa-check mr-1"></i>Finalizada
                                    @else
                                        <i class="fas fa-pause mr-1"></i>Inactiva
                                    @endif
                                </span>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span><strong>Inicio:</strong> {{ $ficha->fecha_inicio->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-calendar-check"></i>
                                        <span><strong>Fin:</strong> {{ $ficha->fecha_fin->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-clock"></i>
                                        <span><strong>Horas:</strong> {{ number_format($instructorFicha->total_horas_instructor) }}h</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <i class="fas fa-graduation-cap"></i>
                                        <span>{{ $ficha->programaFormacion->redConocimiento->nombre ?? 'Sin red' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $ficha->ambiente->sede->nombre ?? 'Sin sede' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-door-open"></i>
                                        <span>{{ $ficha->ambiente->nombre ?? 'Sin ambiente' }}</span>
                                    </div>
                                </div>
                            </div>

                            @if($esActiva && $progreso > 0)
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Progreso del curso</small>
                                        <small class="text-muted">{{ number_format($progreso, 1) }}%</small>
                                    </div>
                                    <div class="progress progress-mini">
                                        <div class="progress-bar bg-success progress-bar-custom" data-width="{{ $progresoWidth }}"></div>
                                    </div>
                                </div>
                            @endif

                            @if($ficha->diasFormacion->count() > 0)
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-week mr-1"></i>
                                        Horarios: 
                                        @foreach($ficha->diasFormacion as $dia)
                                            {{ $dia->dia_nombre }} ({{ $dia->hora_inicio }}-{{ $dia->hora_fin }})
                                            @if(!$loop->last), @endif
                                        @endforeach
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-clipboard-list"></i>
                        <h5>No se encontraron fichas asignadas</h5>
                        <p class="mb-3">Este instructor no tiene fichas asignadas con los filtros actuales.</p>
                        @if($filtroEstado !== 'todas' || $filtroFechaInicio || $filtroFechaFin || $filtroPrograma)
                            <a href="{{ request()->url() }}" class="btn btn-primary">
                                <i class="fas fa-refresh mr-1"></i>Ver todas las fichas
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        @if($fichasAsignadas->hasPages())
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $fichasAsignadas->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Initialize date picker with today as default for fecha_fin
            if (!$('input[name="fecha_fin"]').val()) {
                $('input[name="fecha_fin"]').val('{{ now()->format("Y-m-d") }}');
            }

            // Set progress bar widths
            $('.progress-bar-custom').each(function() {
                var width = $(this).data('width');
                $(this).css('width', width + '%');
            });
        });
    </script>
@stop
