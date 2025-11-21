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
            border-color: #007bff;
            background: #ffffff;
        }
        
        .specialty-card.assigned .card-header {
            background: #f8f9fa;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
        }
        
        .specialty-card .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        
        .btn-assign {
            border-radius: 4px;
            padding: 6px 12px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .btn-remove {
            border-radius: 4px;
            padding: 6px 12px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-primary {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .status-secondary {
            background-color: #f8f9fa;
            color: #6c757d;
        }
        
        .instructor-info {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
    </style>
@endsection

@section('js')
    @vite(['resources/js/pages/gestion-especializada.js'])
    <script>
        // Función para confirmar asignación de especialidad principal
        function confirmarAsignacionPrincipal(especialidadNombre) {
            return Swal.fire({
                title: '¿Asignar como Especialidad Principal?',
                html: `<div class="text-left">
                    <p><strong>Especialidad:</strong> ${especialidadNombre}</p>
                    <p><strong>Acción:</strong> Se asignará como especialidad principal</p>
                    <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Esto reemplazará la especialidad principal actual (si existe)</p>
                </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-star"></i> Asignar Principal',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                focusConfirm: false,
                reverseButtons: true
            });
        }
        
        // Función para confirmar asignación de especialidad secundaria
        function confirmarAsignacionSecundaria(especialidadNombre) {
            return Swal.fire({
                title: '¿Asignar como Especialidad Secundaria?',
                html: `<div class="text-left">
                    <p><strong>Especialidad:</strong> ${especialidadNombre}</p>
                    <p><strong>Acción:</strong> Se agregará a las especialidades secundarias</p>
                </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-plus"></i> Asignar Secundaria',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                focusConfirm: false,
                reverseButtons: true
            });
        }
        
        // Función para confirmar remoción de especialidad principal
        function confirmarRemocionPrincipal(especialidadNombre) {
            return Swal.fire({
                title: '⚠️ Remover Especialidad Principal',
                html: `<div class="text-left">
                    <p><strong>Especialidad:</strong> ${especialidadNombre}</p>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Advertencia:</strong> Esto dejará al instructor sin especialidad principal
                    </div>
                </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash"></i> Remover',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                focusConfirm: false,
                reverseButtons: true
            });
        }
        
        // Función para confirmar remoción de especialidad secundaria
        function confirmarRemocionSecundaria(especialidadNombre) {
            return Swal.fire({
                title: '¿Remover Especialidad Secundaria?',
                html: `<div class="text-left">
                    <p><strong>Especialidad:</strong> ${especialidadNombre}</p>
                    <p><strong>Acción:</strong> Se removerá de las especialidades secundarias</p>
                </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash"></i> Remover',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                focusConfirm: false,
                reverseButtons: true
            });
        }

        // Interceptar envío de formularios para usar SweetAlert
        document.addEventListener('DOMContentLoaded', function() {
            // Interceptar formularios de asignación
            document.querySelectorAll('form[action*="asignar-especialidad"]').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const tipo = form.querySelector('input[name="tipo"]').value;
                    const redConocimientoId = form.querySelector('input[name="red_conocimiento_id"]').value;
                    const especialidadNombre = form.closest('.specialty-card').querySelector('h6').textContent.trim();
                    
                    let confirmacion;
                    if (tipo === 'principal') {
                        confirmacion = confirmarAsignacionPrincipal(especialidadNombre);
                    } else {
                        confirmacion = confirmarAsignacionSecundaria(especialidadNombre);
                    }
                    
                    confirmacion.then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
            
            // Interceptar formularios de remoción
            document.querySelectorAll('form[action*="remover-especialidad"]').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const tipo = form.querySelector('input[name="tipo"]').value;
                    const especialidad = form.querySelector('input[name="especialidad"]').value;
                    
                    let confirmacion;
                    if (tipo === 'principal') {
                        confirmacion = confirmarRemocionPrincipal(especialidad);
                    } else {
                        confirmacion = confirmarRemocionSecundaria(especialidad);
                    }
                    
                    confirmacion.then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-graduation-cap" 
        title="Especialidades"
        subtitle="Gestión de especialidades del instructor"
        :breadcrumb="[['label' => $instructor->persona->primer_nombre . ' ' . $instructor->persona->primer_apellido, 'url' => route('instructor.show', $instructor->id) , 'icon' => 'fa-user'], ['label' => 'Especialidades', 'icon' => 'fa-graduation-cap', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('instructor.show', $instructor->id) }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <!-- Mensajes de Alerta -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

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
                                                        <form action="{{ route('instructor.removerEspecialidad', $instructor->id) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="especialidad" value="{{ $especialidadPrincipal }}">
                                                            <input type="hidden" name="tipo" value="principal">
                                                            <button type="submit" class="btn btn-warning btn-sm">
                                                                <i class="fas fa-times mr-1"></i>Remover
                                                            </button>
                                                        </form>
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
                                                        <form action="{{ route('instructor.removerEspecialidad', $instructor->id) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="especialidad" value="{{ $especialidad }}">
                                                            <input type="hidden" name="tipo" value="secundaria">
                                                            <button type="submit" class="btn btn-danger btn-sm">
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
                                                        <div class="btn-group" role="group">
                                                            <form action="{{ route('instructor.asignarEspecialidad', $instructor->id) }}" 
                                                                  method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="red_conocimiento_id" value="{{ $redConocimiento->id }}">
                                                                <input type="hidden" name="tipo" value="principal">
                                                                <button type="submit" class="btn btn-success btn-sm">
                                                                    <i class="fas fa-star mr-1"></i>Principal
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('instructor.asignarEspecialidad', $instructor->id) }}" 
                                                                  method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="red_conocimiento_id" value="{{ $redConocimiento->id }}">
                                                                <input type="hidden" name="tipo" value="secundaria">
                                                                <button type="submit" class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-plus mr-1"></i>Secundaria
                                                                </button>
                                                            </form>
                                                        </div>
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
    @include('layouts.footer')
@endsection