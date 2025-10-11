@extends('adminlte::page')

@section('css')
    @vite(['resources/css/guias_aprendizaje.css'])
    <style>
        .dashboard-header {
            background: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, .03);
        }
        .text-gray-800 {
            color: #5a5c69 !important;
        }
        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.1);
        }
        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.1);
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
                        <i class="fas fa-clipboard-list text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Detalle de Competencia</h1>
                        <p class="text-muted mb-0 font-weight-light">{{ $competencia->codigo }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <!-- Información Principal -->
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i> Información General
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong><i class="fas fa-barcode"></i> Código:</strong><br>
                                    <span class="badge badge-primary badge-lg">{{ $competencia->codigo }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-toggle-on"></i> Estado:</strong><br>
                                    @if($competencia->status)
                                        <span class="badge badge-success">Activa</span>
                                    @else
                                        <span class="badge badge-danger">Inactiva</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong><i class="fas fa-tag"></i> Nombre:</strong><br>
                                {{ $competencia->nombre }}
                            </div>

                            <div class="mb-3">
                                <strong><i class="fas fa-align-left"></i> Descripción:</strong><br>
                                <p class="text-muted">{{ $competencia->descripcion ?? 'Sin descripción' }}</p>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong><i class="fas fa-clock"></i> Duración:</strong><br>
                                    <span class="badge badge-info">{{ $competencia->duracion }} horas</span>
                                </div>
                                <div class="col-md-4">
                                    <strong><i class="fas fa-calendar-alt"></i> Fecha Inicio:</strong><br>
                                    {{ $competencia->fecha_inicio ? $competencia->fecha_inicio->format('d/m/Y') : 'N/A' }}
                                </div>
                                <div class="col-md-4">
                                    <strong><i class="fas fa-calendar-check"></i> Fecha Fin:</strong><br>
                                    {{ $competencia->fecha_fin ? $competencia->fecha_fin->format('d/m/Y') : 'N/A' }}
                                </div>
                            </div>

                            @if($competencia->estaVigente())
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> Esta competencia está <strong>vigente</strong> actualmente.
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Esta competencia <strong>no está vigente</strong> actualmente.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Resultados de Aprendizaje Asociados -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-success text-white">
                            <h3 class="card-title">
                                <i class="fas fa-graduation-cap"></i> Resultados de Aprendizaje Asociados
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($competencia->resultadosAprendizaje->isEmpty())
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No hay resultados de aprendizaje asociados a esta competencia.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Duración</th>
                                                <th>Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($competencia->resultadosAprendizaje as $rap)
                                                <tr>
                                                    <td><span class="badge badge-info">{{ $rap->codigo }}</span></td>
                                                    <td>{{ Str::limit($rap->nombre, 50) }}</td>
                                                    <td>{{ $rap->duracion }}h</td>
                                                    <td>
                                                        @if($rap->status)
                                                            <span class="badge badge-success">Activo</span>
                                                        @else
                                                            <span class="badge badge-danger">Inactivo</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('resultados-aprendizaje.show', $rap->id) }}" 
                                                           class="btn btn-sm btn-info" 
                                                           title="Ver RAP">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Total: <strong>{{ $competencia->resultadosAprendizaje->count() }}</strong> resultado(s) de aprendizaje asociado(s)
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Panel Lateral -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning">
                            <h3 class="card-title">
                                <i class="fas fa-cog"></i> Acciones
                            </h3>
                        </div>
                        <div class="card-body">
                            @can('GESTIONAR RESULTADOS COMPETENCIA')
                                <a href="{{ route('competencias.gestionarResultados', $competencia) }}" class="btn btn-success btn-block mb-2">
                                    <i class="fas fa-tasks"></i> Gestionar Resultados
                                </a>
                            @endcan
                            
                            @can('EDITAR COMPETENCIA')
                                <a href="{{ route('competencias.edit', $competencia) }}" class="btn btn-warning btn-block mb-2">
                                    <i class="fas fa-edit"></i> Editar Competencia
                                </a>
                            @endcan
                            
                            @can('CAMBIAR ESTADO COMPETENCIA')
                                <form action="{{ route('competencias.cambiarEstado', $competencia) }}" method="POST" class="mb-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-{{ $competencia->status ? 'secondary' : 'primary' }} btn-block">
                                        <i class="fas fa-toggle-{{ $competencia->status ? 'off' : 'on' }}"></i> 
                                        {{ $competencia->status ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            @endcan
                            
                            @can('ELIMINAR COMPETENCIA')
                                <button type="button" 
                                        class="btn btn-danger btn-block mb-2"
                                        onclick="confirmarEliminacion('{{ $competencia->codigo }}', '{{ route('competencias.destroy', $competencia) }}')">
                                    <i class="fas fa-trash"></i> Eliminar Competencia
                                </button>
                            @endcan
                            
                            <a href="{{ route('competencias.index') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Volver al Listado
                            </a>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar"></i> Estadísticas
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>RAPs Asociados:</strong>
                                <span class="float-right badge badge-primary">{{ $competencia->resultadosAprendizaje->count() }}</span>
                            </div>
                            <div class="mb-3">
                                <strong>Programas:</strong>
                                <span class="float-right badge badge-info">{{ $competencia->programasFormacion->count() }}</span>
                            </div>
                            <hr>
                            <div>
                                <strong>Duración Total:</strong><br>
                                <span class="badge badge-warning">{{ $competencia->duracion }} horas</span>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title">
                                <i class="fas fa-user-clock"></i> Auditoría
                            </h3>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">
                                <p><strong>Creado por:</strong><br>{{ $competencia->userCreate->name ?? 'N/A' }}</p>
                                <p><strong>Fecha creación:</strong><br>{{ $competencia->created_at ? $competencia->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                <p><strong>Editado por:</strong><br>{{ $competencia->userEdit->name ?? 'N/A' }}</p>
                                <p><strong>Última edición:</strong><br>{{ $competencia->updated_at ? $competencia->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                            </small>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });

    function confirmarEliminacion(nombre, url) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar la competencia "${nombre}"? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection

