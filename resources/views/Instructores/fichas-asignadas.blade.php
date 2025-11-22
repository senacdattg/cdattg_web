@extends('adminlte::page')

@section('title', 'Fichas Asignadas')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
    <style>
        .stats-card {
            background: #ffffff;
            color: #495057;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            margin-bottom: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .instructor-info {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .table-custom {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table-custom thead {
            background: #007bff;
            color: white;
        }
        .table-custom thead th {
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
            padding: 12px 8px;
        }
        .table-custom tbody tr {
            transition: all 0.3s ease;
        }
        .table-custom tbody tr:hover {
            background: #f8f9fa;
        }
        .table-custom tbody td {
            padding: 12px 8px;
            vertical-align: middle;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .progress-bar-custom {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            transition: width 0.3s ease;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-clipboard-list" 
        title="Fichas Asignadas"
        subtitle="Gestión de fichas del instructor"
        :breadcrumb="[['label' => '{{ $instructorActual->persona->primer_nombre }} {{ $instructorActual->persona->primer_apellido }}', 'url' => route('instructor.show', $instructorActual->id) , 'icon' => 'fa-user'], ['label' => 'Fichas asignadas', 'icon' => 'fa-clipboard-list', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('instructor.show', $instructorActual->id) }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <!-- Información del Instructor -->
                    <div class="instructor-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-2">
                                    <i class="fas fa-chalkboard-teacher mr-2"></i>
                                    {{ $instructorActual->persona->primer_nombre }} {{ $instructorActual->persona->primer_apellido }}
                                </h4>
                                <p class="mb-1">
                                    <strong>Documento:</strong> {{ $instructorActual->persona->numero_documento }}
                                </p>
                                <p class="mb-0">
                                    <strong>Regional:</strong> {{ $instructorActual->regional->nombre ?? 'No asignada' }}
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="stats-card">
                                            <div class="stats-number">{{ $estadisticas['total'] }}</div>
                                            <div class="stats-label">Total Fichas</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stats-card">
                                            <div class="stats-number">{{ $estadisticas['activas'] }}</div>
                                            <div class="stats-label">Activas</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stats-card">
                                            <div class="stats-number">{{ $estadisticas['total_horas'] ?? 0 }}</div>
                                            <div class="stats-label">Horas</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="card shadow-sm mb-4 no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-filter mr-2"></i>Filtros
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('instructor.fichasAsignadas', $instructorActual->id) }}" class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                        <input type="date" name="fecha_inicio" id="fecha_inicio" 
                                               class="form-control" value="{{ request('fecha_inicio') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                        <input type="date" name="fecha_fin" id="fecha_fin" 
                                               class="form-control" value="{{ request('fecha_fin') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="estado" class="form-label">Estado</label>
                                        <select name="estado" id="estado" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activas</option>
                                            <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivas</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('instructor.fichasAsignadas', $instructorActual->id) }}" class="btn btn-light mr-2">
                                            Limpiar Filtros
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search mr-1"></i>Filtrar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de Fichas -->
                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-list mr-2"></i>Lista de Fichas Asignadas
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            @if($fichasAsignadas->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-custom">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ficha</th>
                                                <th>Programa de Formación</th>
                                                <th>Fecha Inicio</th>
                                                <th>Fecha Fin</th>
                                                <th>Total Horas</th>
                                                <th>Progreso</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($fichasAsignadas as $index => $ficha)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $ficha->ficha->ficha ?? 'N/A' }}</strong>
                                                </td>
                                                <td>{{ $ficha->ficha->programaFormacion->nombre ?? 'N/A' }}</td>
                                                <td>
                                                    @if($ficha->ficha->fecha_inicio)
                                                        {{ \Carbon\Carbon::parse($ficha->ficha->fecha_inicio)->format('d/m/Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($ficha->ficha->fecha_fin)
                                                        {{ \Carbon\Carbon::parse($ficha->ficha->fecha_fin)->format('d/m/Y') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $ficha->ficha->total_horas ?? 'N/A' }}</td>
                                                <td>
                                                    @php
                                                        $progreso = rand(20, 90); // Simulación de progreso
                                                    @endphp
                                                    <div class="progress-bar-custom">
                                                        <div class="progress-fill" data-width="{{ $progreso }}%" style="width: {{ $progreso }}%"></div>
                                                    </div>
                                                    <small class="text-muted">{{ $progreso }}%</small>
                                                </td>
                                                <td>
                                                    <span class="status-badge {{ $ficha->ficha->status ? 'status-active' : 'status-inactive' }}">
                                                        <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                        {{ $ficha->ficha->status ? 'Activa' : 'Inactiva' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('ficha.show', $ficha->ficha->id) }}" 
                                                           class="btn btn-light btn-sm" 
                                                           data-toggle="tooltip" 
                                                           title="Ver detalles">
                                                            <i class="fas fa-eye text-info"></i>
                                                        </a>
                                                        <a href="{{ route('ficha.edit', $ficha->ficha->id) }}" 
                                                           class="btn btn-light btn-sm" 
                                                           data-toggle="tooltip" 
                                                           title="Editar">
                                                            <i class="fas fa-pencil-alt text-warning"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-muted">No hay fichas asignadas a este instructor</p>
                                </div>
                            @endif
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
    @vite(['resources/js/pages/resources-views\Instructores\fichas-asignadas.js'])
@endsection