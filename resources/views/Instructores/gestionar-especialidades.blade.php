@extends('adminlte::page')

@section('title', 'Gestionar Especialidades')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
    <style>
        .specialty-card {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
        }
        
        .specialty-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .specialty-card.assigned {
            border-color: #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }
        
        .specialty-card.assigned .card-header {
            background: #28a745;
            color: white;
        }
        
        .specialty-card .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        
        .btn-assign, .btn-remove {
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-assign {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            color: white;
        }
        
        .btn-assign:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
            color: white;
            transform: translateY(-1px);
        }
        
        .btn-remove {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            color: white;
        }
        
        .btn-remove:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            color: white;
            transform: translateY(-1px);
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-primary {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .status-secondary {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }
        
        .instructor-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
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
                        <i class="fas fa-graduation-cap text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Especialidades</h1>
                        <p class="text-muted mb-0 font-weight-light">Gestión de especialidades del instructor</p>
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
                                <a href="{{ route('instructor.index') }}" class="link_right_header">
                                    <i class="fas fa-chalkboard-teacher"></i> Instructores
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('instructor.show', $instructor->id) }}" class="link_right_header">
                                    <i class="fas fa-user"></i> {{ $instructor->persona->primer_nombre }} {{ $instructor->persona->primer_apellido }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-graduation-cap"></i> Especialidades
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
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('instructor.show', $instructor->id) }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <!-- Información del Instructor -->
                    <div class="instructor-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-2">
                                    <i class="fas fa-chalkboard-teacher mr-2"></i>
                                    {{ $instructor->persona->primer_nombre }} {{ $instructor->persona->primer_apellido }}
                                </h4>
                                <p class="mb-1">
                                    <strong>Documento:</strong> {{ $instructor->persona->numero_documento }}
                                </p>
                                <p class="mb-0">
                                    <strong>Regional:</strong> {{ $instructor->regional->nombre ?? 'No asignada' }}
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h5 class="mb-0">{{ count($especialidadesSecundarias) + ($especialidadPrincipal ? 1 : 0) }}</h5>
                                            <small>Asignadas</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h5 class="mb-0">{{ $redesConocimiento->count() }}</h5>
                                            <small>Disponibles</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Especialidades Asignadas -->
                    @if(count($especialidadesSecundarias) > 0 || $especialidadPrincipal)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm no-hover">
                                <div class="card-header bg-white py-3">
                                    <h5 class="card-title m-0 font-weight-bold text-primary">
                                        <i class="fas fa-check-circle mr-2"></i>Especialidades Asignadas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if($especialidadPrincipal)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card specialty-card assigned">
                                                <div class="card-header py-2">
                                                    <h6 class="mb-0">
                                                        <i class="fas fa-graduation-cap mr-1"></i>
                                                        {{ $especialidadPrincipal }}
                                                    </h6>
                                                </div>
                                                <div class="card-body py-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="status-badge status-primary">
                                                            <i class="fas fa-star mr-1"></i>
                                                            Principal
                                                        </span>
                                                        <button type="button" class="btn btn-remove btn-sm" 
                                                                onclick="alert('Para remover la especialidad principal, primero debe asignar otra como principal')">
                                                            <i class="fas fa-times mr-1"></i>Remover
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @foreach($especialidadesSecundarias as $especialidad)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card specialty-card assigned">
                                                <div class="card-header py-2">
                                                    <h6 class="mb-0">
                                                        <i class="fas fa-graduation-cap mr-1"></i>
                                                        {{ $especialidad }}
                                                    </h6>
                                                </div>
                                                <div class="card-body py-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="status-badge status-secondary">
                                                            <i class="fas fa-circle mr-1"></i>
                                                            Secundaria
                                                        </span>
                                                        <form action="{{ route('instructor.removerEspecialidad', [$instructor->id, $especialidad]) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-remove btn-sm" 
                                                                    onclick="return confirm('¿Está seguro de remover esta especialidad?')">
                                                                <i class="fas fa-times mr-1"></i>Remover
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Especialidades Disponibles -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm no-hover">
                                <div class="card-header bg-white py-3">
                                    <h5 class="card-title m-0 font-weight-bold text-primary">
                                        <i class="fas fa-plus-circle mr-2"></i>Especialidades Disponibles
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($redesConocimiento as $redConocimiento)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card specialty-card">
                                                <div class="card-header py-2">
                                                    <h6 class="mb-0">
                                                        <i class="fas fa-graduation-cap mr-1"></i>
                                                        {{ $redConocimiento->nombre }}
                                                    </h6>
                                                </div>
                                                <div class="card-body py-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">
                                                            {{ $redConocimiento->descripcion ?? 'Sin descripción' }}
                                                        </small>
                                                        <form action="{{ route('instructor.asignarEspecialidad', [$instructor->id, $redConocimiento->id]) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-assign btn-sm">
                                                                <i class="fas fa-plus mr-1"></i>Asignar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
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
    <script>
        $(document).ready(function() {
            // Tooltips para elementos interactivos
            $('[data-toggle="tooltip"]').tooltip();

            // Animación de entrada para las cards
            $('.specialty-card').each(function(index) {
                $(this).css('opacity', '0').delay(index * 100).animate({
                    opacity: 1
                }, 500);
            });

            // Confirmación para acciones de remover especialidad
            $('form[action*="removerEspecialidad"]').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                
                Swal.fire({
                    title: '¿Remover Especialidad?',
                    text: 'Esta acción removerá la especialidad del instructor. ¿Está seguro?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, remover',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection