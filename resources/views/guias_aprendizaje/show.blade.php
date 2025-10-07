@extends('adminlte::page')

@section('css')
    <style>
        .info-card {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-left: 4px solid #2196f3;
        }
        .stats-card {
            transition: transform 0.2s;
            border-radius: 10px;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .detail-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .detail-section h5 {
            color: #495057;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .badge-custom {
            font-size: 0.9em;
            padding: 0.5em 1em;
        }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
            color: white;
        }
        .btn-custom {
            border-radius: 25px;
            padding: 10px 30px;
        }
        .progress-custom {
            height: 20px;
            border-radius: 10px;
        }
        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 20px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #007bff;
        }
        .timeline-item::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 12px;
            width: 2px;
            height: calc(100% + 8px);
            background: #dee2e6;
        }
        .timeline-item:last-child::after {
            display: none;
        }
    </style>
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-info rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-eye text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Detalles de la Guía</h1>
                        <p class="text-muted mb-0 font-weight-light">{{ $guiaAprendizaje->nombre }}</p>
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
                                <a href="{{ route('guias-aprendizaje.index') }}" class="link_right_header">
                                    <i class="fas fa-book-open"></i> Guías de Aprendizaje
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-eye"></i> Detalles
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
            <!-- Estadísticas Principales -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $guiaAprendizaje->resultadosAprendizaje->count() }}</h4>
                                    <p class="mb-0">Resultados</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-target fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $guiaAprendizaje->actividades->count() }}</h4>
                                    <p class="mb-0">Actividades</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-tasks fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $guiaAprendizaje->porcentajeCompletitud() }}%</h4>
                                    <p class="mb-0">Completitud</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-pie fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $guiaAprendizaje->diasDesdeCreacion() }}</h4>
                                    <p class="mb-0">Días activa</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información General -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card info-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-info-circle mr-2"></i>Información General
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Código:</strong> {{ $guiaAprendizaje->codigo }}</p>
                                            <p><strong>Nombre:</strong> {{ $guiaAprendizaje->nombre }}</p>
                                            <p><strong>Estado:</strong> 
                                                <span class="badge badge-custom {{ $guiaAprendizaje->status == 1 ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $guiaAprendizaje->status == 1 ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Creada:</strong> {{ $guiaAprendizaje->created_at->format('d/m/Y H:i') }}</p>
                                            <p><strong>Última actualización:</strong> {{ $guiaAprendizaje->updated_at->format('d/m/Y H:i') }}</p>
                                            <p><strong>Creada por:</strong> {{ $guiaAprendizaje->userCreate->persona->primer_nombre ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-chart-bar mr-2"></i>Progreso
                                    </h5>
                                    <div class="progress progress-custom mb-3">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $guiaAprendizaje->porcentajeCompletitud() }}%"
                                             aria-valuenow="{{ $guiaAprendizaje->porcentajeCompletitud() }}" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            {{ $guiaAprendizaje->porcentajeCompletitud() }}%
                                        </div>
                                    </div>
                                    <p class="text-muted small">
                                        {{ $guiaAprendizaje->actividades->where('id_estado', '25')->count() }} de {{ $guiaAprendizaje->actividades->count() }} actividades completadas
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resultados de Aprendizaje -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title m-0">
                                <i class="fas fa-target mr-2"></i>Resultados de Aprendizaje Asociados
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($guiaAprendizaje->resultadosAprendizaje->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-borderless table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Descripción</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($guiaAprendizaje->resultadosAprendizaje as $resultado)
                                                <tr>
                                                    <td>
                                                        <span class="font-weight-bold text-primary">{{ $resultado->codigo }}</span>
                                                    </td>
                                                    <td>{{ $resultado->nombre }}</td>
                                                    <td>{{ Str::limit($resultado->descripcion, 100) }}</td>
                                                    <td>
                                                        <span class="badge {{ $resultado->status == 1 ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $resultado->status == 1 ? 'Activo' : 'Inactivo' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-target fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay resultados de aprendizaje asociados</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividades -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title m-0">
                                <i class="fas fa-tasks mr-2"></i>Actividades Asociadas
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($guiaAprendizaje->actividades->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-borderless table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Fecha</th>
                                                <th>Estado</th>
                                                <th>Progreso</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($guiaAprendizaje->actividades as $actividad)
                                                <tr>
                                                    <td>
                                                        <span class="font-weight-bold text-primary">{{ $actividad->codigo }}</span>
                                                    </td>
                                                    <td>{{ $actividad->nombre }}</td>
                                                    <td>{{ $actividad->fecha_evidencia ? \Carbon\Carbon::parse($actividad->fecha_evidencia)->format('d/m/Y') : 'N/A' }}</td>
                                                    <td>
                                                        @php
                                                            $estadoClass = $actividad->id_estado == '25' ? 'badge-success' : 
                                                                          ($actividad->id_estado == '27' ? 'badge-warning' : 'badge-secondary');
                                                            $estadoText = $actividad->id_estado == '25' ? 'Completado' : 
                                                                         ($actividad->id_estado == '27' ? 'Pendiente' : 'En Proceso');
                                                        @endphp
                                                        <span class="badge {{ $estadoClass }}">
                                                            {{ $estadoText }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="progress" style="height: 8px;">
                                                            @php
                                                                $progreso = $actividad->id_estado == '25' ? 100 : 
                                                                           ($actividad->id_estado == '27' ? 0 : 50);
                                                            @endphp
                                                            <div class="progress-bar {{ $progreso == 100 ? 'bg-success' : ($progreso == 0 ? 'bg-warning' : 'bg-info') }}" 
                                                                 role="progressbar" style="width: {{ $progreso }}%"></div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay actividades asociadas</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline de Actividad -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title m-0">
                                <i class="fas fa-history mr-2"></i>Historial de Actividad
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline-item">
                                <h6 class="text-primary">Guía creada</h6>
                                <p class="text-muted mb-1">{{ $guiaAprendizaje->created_at->format('d/m/Y H:i') }}</p>
                                <small class="text-muted">Creada por: {{ $guiaAprendizaje->userCreate->persona->primer_nombre ?? 'Usuario' }}</small>
                            </div>
                            @if($guiaAprendizaje->updated_at != $guiaAprendizaje->created_at)
                                <div class="timeline-item">
                                    <h6 class="text-warning">Última actualización</h6>
                                    <p class="text-muted mb-1">{{ $guiaAprendizaje->updated_at->format('d/m/Y H:i') }}</p>
                                    <small class="text-muted">Actualizada por: {{ $guiaAprendizaje->userEdit->persona->primer_nombre ?? 'Usuario' }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('guias-aprendizaje.index') }}" class="btn btn-secondary btn-custom">
                            <i class="fas fa-arrow-left mr-2"></i>Volver al Listado
                        </a>
                        <div>
                            @can('EDITAR GUIA APRENDIZAJE')
                                <a href="{{ route('guias-aprendizaje.edit', $guiaAprendizaje) }}" class="btn btn-warning btn-custom">
                                    <i class="fas fa-edit mr-2"></i>Editar
                                </a>
                            @endcan
                            @can('ELIMINAR GUIA APRENDIZAJE')
                                <form action="{{ route('guias-aprendizaje.destroy', $guiaAprendizaje) }}" 
                                      method="POST" class="d-inline formulario-eliminar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-custom">
                                        <i class="fas fa-trash mr-2"></i>Eliminar
                                    </button>
                                </form>
                            @endcan
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
    <script>
        $(document).ready(function() {
            // Confirmación de eliminación
            $('.formulario-eliminar').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción eliminará permanentemente la guía de aprendizaje y no se puede deshacer",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Animación de las tarjetas de estadísticas
            $('.stats-card').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });
        });
    </script>
@endsection

