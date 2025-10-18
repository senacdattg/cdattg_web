@extends('adminlte::page')

@section('title', 'Gestionar Instructores - Ficha ' . $ficha->ficha)

@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
    @vite(['resources/css/parametros.css'])
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    <style>
        /* Estilos para badges de estado */
        .badge {
            font-size: 0.75rem;
        }
        
        /* Estilos para alertas expandibles */
        .alert {
            border-radius: 0.375rem;
        }
        
        /* Estilos para alertas de error mejorados */
        .alert-danger {
            border-left: 4px solid #dc3545;
            animation: slideDown 0.4s ease-out;
        }
        
        .alert-success {
            border-left: 4px solid #28a745;
            animation: slideDown 0.4s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-heading {
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .alert ul {
            padding-left: 1.5rem;
        }
        
        .alert ul li {
            margin-bottom: 0.5rem;
        }
        
        /* Estilos para tabla de instructores */
        .table-responsive {
            border-radius: 0.375rem;
        }
        
        .table tbody tr.table-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }
        
        .table tbody tr.table-info {
            background-color: rgba(23, 162, 184, 0.1);
        }
        
        .table tbody tr.table-light {
            background-color: rgba(248, 249, 250, 0.8);
        }
        
        /* Estilos para estadísticas */
        .border-left-primary {
            border-left: 0.25rem solid #007bff !important;
        }
        
        .border-left-success {
            border-left: 0.25rem solid #28a745 !important;
        }
        
        .border-left-warning {
            border-left: 0.25rem solid #ffc107 !important;
        }
        
        .border-left-info {
            border-left: 0.25rem solid #17a2b8 !important;
        }
        
        .border-left-danger {
            border-left: 0.25rem solid #dc3545 !important;
        }
        
        /* Estilos para hover effects */
        .hover-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .hover-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-chalkboard-teacher" 
        title="Gestionar Instructores"
        subtitle="Ficha {{ $ficha->ficha }}"
        :breadcrumb="[['label' => 'Ficha {{ $ficha->ficha }}', 'url' => route('fichaCaracterizacion.show', $ficha->id) , 'icon' => 'fa-eye'], ['label' => 'Gestionar Instructores', 'icon' => 'fa-chalkboard-teacher', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('fichaCaracterizacion.show', $ficha->id) }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a la Ficha
                    </a>

                    <!-- Información de la Ficha -->
                    <div class="card detail-card no-hover mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Información de la Ficha
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <strong>Programa:</strong><br>
                                        <span class="text-muted">{{ $ficha->programaFormacion->nombre ?? 'No asignado' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <strong>Fecha Inicio:</strong><br>
                                        <span class="text-muted">{{ $ficha->fecha_inicio ? $ficha->fecha_inicio->format('d/m/Y') : 'No definida' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <strong>Fecha Fin:</strong><br>
                                        <span class="text-muted">{{ $ficha->fecha_fin ? $ficha->fecha_fin->format('d/m/Y') : 'No definida' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <strong>Total Horas:</strong><br>
                                        <span class="text-muted">{{ $ficha->total_horas ?? 'No definido' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Asignación de Instructores -->
                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-header bg-white border-0 py-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle p-2 me-3">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 text-dark">Asignar Instructores</h4>
                                    <small class="text-muted">Agregue instructores adicionales a esta ficha</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            {{-- Mostrar errores de validación --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle mr-2"></i>Error en la asignación</h5>
                                    <hr>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Mostrar mensajes de éxito --}}
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('fichaCaracterizacion.asignarInstructores', $ficha->id) }}" method="POST" id="formAsignarInstructores">
                                @csrf
                                
                                {{-- Campo oculto para instructor principal --}}
                                <input type="hidden" 
                                       name="instructor_principal_id" 
                                       id="instructor_principal_id" 
                                       value="{{ old('instructor_principal_id', $ficha->instructor_id) }}">
                                
                                <!-- Información del Instructor Principal (Líder) -->
                                @if($ficha->instructor)
                                    <div class="alert alert-success">
                                        <i class="fas fa-star text-warning mr-1"></i>
                                        <strong>Instructor Líder de la Ficha:</strong>
                                        {{ $ficha->instructor->persona->primer_nombre }} 
                                        {{ $ficha->instructor->persona->primer_apellido }}
                                        <small class="text-muted">({{ $ficha->instructor->persona->numero_documento }})</small>
                                        <br>
                                        <small class="text-light">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            El instructor líder fue asignado en la creación de la ficha y no necesita estar en la lista de instructores adicionales.
                                        </small>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <strong>Advertencia:</strong> Esta ficha no tiene un instructor líder asignado. 
                                        Se recomienda asignar un instructor líder desde la edición de la ficha.
                                    </div>
                                @endif

                                <!-- Información de fechas permitidas -->
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Rango de fechas permitidas:</strong>
                                    @if($ficha->fecha_inicio && $ficha->fecha_fin)
                                        Desde {{ $ficha->fecha_inicio->format('d/m/Y') }} hasta {{ $ficha->fecha_fin->format('d/m/Y') }}
                                    @else
                                        <span class="text-warning">Las fechas de la ficha no están definidas</span>
                                    @endif
                                </div>

                                <!-- Información de días de formación -->
                                @if($diasFormacionFicha->count() > 0)
                                    <div class="alert alert-success">
                                        <i class="fas fa-calendar-check"></i>
                                        <strong>Días de formación disponibles:</strong>
                                        {{ $diasFormacionFicha->pluck('name')->implode(', ') }}
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Advertencia:</strong> Esta ficha no tiene días de formación asignados. 
                                        <a href="{{ route('fichaCaracterizacion.gestionarDiasFormacion', $ficha->id) }}" class="alert-link">
                                            Asignar días de formación
                                        </a>
                                    </div>
                                @endif

                                <!-- Lista de Instructores -->
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">
                                        <i class="fas fa-users mr-1"></i>
                                        Instructores Asignados <span class="text-danger">*</span>
                                    </label>
                                    <div id="instructores-container">
                                        <!-- Los instructores se agregarán dinámicamente aquí -->
                                    </div>
                                    <button type="button" class="btn btn-primary btn-lg mt-4" onclick="agregarInstructor()">
                                        <i class="fas fa-plus me-2"></i> Agregar Instructor
                                    </button>
                                </div>

                                <div class="border-top pt-4 mt-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i> Volver
                                        </a>
                                        <button type="submit" class="btn btn-success btn-lg px-4">
                                            <i class="fas fa-check me-2"></i> Asignar Instructores
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Instructores Asignados -->
                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-header bg-white border-0 py-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle p-2 me-3">
                                    <i class="fas fa-user-check text-white"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 text-dark">Instructores Asignados</h4>
                                    <small class="text-muted">Instructores ya asignados a esta ficha</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            @if($instructoresAsignados->count() > 0)
                                <div class="row g-3">
                                    @foreach($instructoresAsignados as $asignacion)
                                        <div class="col-md-6">
                                            <div class="bg-light border rounded p-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1 text-dark">
                                                            {{ $asignacion->instructor->persona->primer_nombre }} 
                                                            {{ $asignacion->instructor->persona->primer_apellido }}
                                                            @if($ficha->instructor_id == $asignacion->instructor_id)
                                                                <span class="badge bg-primary ms-2">Principal</span>
                                                            @else
                                                                <span class="badge bg-secondary ms-2">Auxiliar</span>
                                                            @endif
                                                        </h6>
                                                        <p class="text-muted mb-1 small">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            {{ $asignacion->fecha_inicio->format('d/m/Y') }} - 
                                                            {{ $asignacion->fecha_fin->format('d/m/Y') }}
                                                        </p>
                                                        <p class="text-muted mb-0 small">
                                                            <i class="fas fa-clock me-1"></i>
                                                            {{ $asignacion->total_horas_instructor }} horas
                                                        </p>
                                                        @if($asignacion->instructorFichaDias && $asignacion->instructorFichaDias->count() > 0)
                                                            @php
                                                                $diasAsignados = $asignacion->instructorFichaDias
                                                                    ->filter(function($dia) { return $dia->dia && $dia->dia->name; })
                                                                    ->map(function($dia) { return $dia->dia->name; })
                                                                    ->implode(', ');
                                                            @endphp
                                                            @if($diasAsignados)
                                                                <p class="text-muted mb-0 small mt-1">
                                                                    <i class="fas fa-calendar-week me-1"></i>
                                                                    <strong>Días:</strong> {{ $diasAsignados }}
                                                                </p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <form action="{{ route('fichaCaracterizacion.desasignarInstructor', [$ficha->id, $asignacion->instructor_id]) }}" 
                                                              method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                    onclick="return confirm('¿Está seguro de desasignar este instructor?' )"
                                                                    title="Desasignar instructor">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-user-slash fa-3x mb-3"></i>
                                    <p>No hay instructores adicionales asignados a esta ficha.</p>
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
    @include('layout.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/gestion-especializada.js'])
@endsection
