@extends('adminlte::page')

@section('css')
    @vite(['resources/css/competencias.css'])
    <style>
        .detail-table th { width: 30%; font-weight: 600; background-color: #f8f9fc; border-right: 1px solid #e3e6f0; }
        .detail-table td { padding: 0.75rem 1rem; }
        .detail-table tr { border-bottom: 1px solid #e3e6f0; }
        .detail-table tr:last-child { border-bottom: none; }
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
    </style>
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-clipboard-list text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Competencias</h1>
                        <p class="text-muted mb-0 font-weight-light">Gestión de competencias del SENA</p>
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
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-info-circle"></i> Detalles
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
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('competencias.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Detalle de la Competencia
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Código</th>
                                            <td class="py-3">
                                                <span class="badge badge-primary badge-lg">{{ $competencia->codigo }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Nombre</th>
                                            <td class="py-3">{{ $competencia->nombre }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Descripción</th>
                                            <td class="py-3">{{ $competencia->descripcion ?? 'Sin descripción' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Duración</th>
                                            <td class="py-3">
                                                <span class="badge badge-info">{{ formatear_horas($competencia->duracion) }} horas</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Inicio</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $competencia->fecha_inicio ? $competencia->fecha_inicio->format('d/m/Y') : 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de Fin</th>
                                            <td class="py-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $competencia->fecha_fin ? $competencia->fecha_fin->format('d/m/Y') : 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $competencia->status == 1 ? 'text-success' : 'text-danger' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $competencia->status == 1 ? 'Activa' : 'Inactiva' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">RAPs Asociados</th>
                                            <td class="py-3">
                                                <span class="badge badge-primary badge-lg">{{ $competencia->resultadosAprendizaje->count() }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Programas de Formación</th>
                                            <td class="py-3">
                                                <span class="badge badge-info badge-lg">{{ $competencia->programasFormacion->count() }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que crea</th>
                                            <td class="py-3 user-info">
                                                @if ($competencia->userCreate)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $competencia->userCreate->name }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de creación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $competencia->created_at ? $competencia->created_at->diffForHumans() : 'N/A' }}
                                                <small class="text-muted">({{ $competencia->created_at ? $competencia->created_at->format('d/m/Y H:i') : 'N/A' }})</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que modifica</th>
                                            <td class="py-3 user-info">
                                                @if ($competencia->userEdit)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $competencia->userEdit->name }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de modificación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $competencia->updated_at ? $competencia->updated_at->diffForHumans() : 'N/A' }}
                                                <small class="text-muted">({{ $competencia->updated_at ? $competencia->updated_at->format('d/m/Y H:i') : 'N/A' }})</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-center gap-2">
                                @can('GESTIONAR RESULTADOS COMPETENCIA')
                                    <a href="{{ route('competencias.gestionarResultados', $competencia) }}"
                                        class="btn btn-outline-success btn-sm mx-1">
                                        <i class="fas fa-tasks mr-1"></i> Gestionar Resultados
                                    </a>
                                @endcan
                                @can('EDITAR COMPETENCIA')
                                    <a href="{{ route('competencias.edit', $competencia) }}"
                                        class="btn btn-outline-info btn-sm mx-1">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan
                                @can('CAMBIAR ESTADO COMPETENCIA')
                                    <form action="{{ route('competencias.cambiarEstado', $competencia) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-outline-{{ $competencia->status ? 'secondary' : 'primary' }} btn-sm mx-1">
                                            <i class="fas fa-toggle-{{ $competencia->status ? 'off' : 'on' }} mr-1"></i> 
                                            {{ $competencia->status ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                @endcan
                                @can('ELIMINAR COMPETENCIA')
                                    <button type="button" class="btn btn-outline-danger btn-sm mx-1" 
                                        data-nombre="{{ $competencia->codigo }}" 
                                        data-url="{{ route('competencias.destroy', $competencia) }}"
                                        onclick="confirmarEliminacion(this.dataset.nombre, this.dataset.url)">
                                        <i class="fas fa-trash mr-1"></i> Eliminar
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>

                    {{-- Sección de Resultados de Aprendizaje si existen --}}
                    @if($competencia->resultadosAprendizaje->isNotEmpty())
                        <div class="card shadow-sm mt-4 no-hover">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title m-0 font-weight-bold text-success">
                                    <i class="fas fa-graduation-cap mr-2"></i>Resultados de Aprendizaje Asociados
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-borderless table-striped mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="px-4 py-3">#</th>
                                                <th class="px-4 py-3">Código</th>
                                                <th class="px-4 py-3">Nombre</th>
                                                <th class="px-4 py-3">Duración</th>
                                                <th class="px-4 py-3">Estado</th>
                                                <th class="px-4 py-3 text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($competencia->resultadosAprendizaje as $rap)
                                                <tr>
                                                    <td class="px-4">{{ $loop->iteration }}</td>
                                                    <td class="px-4"><span class="badge badge-info">{{ $rap->codigo }}</span></td>
                                                    <td class="px-4">{{ Str::limit($rap->nombre, 50) }}</td>
                                                    <td class="px-4">{{ formatear_horas($rap->duracion) }}h</td>
                                                    <td class="px-4">
                                                        @if($rap->status)
                                                            <span class="badge badge-success">Activo</span>
                                                        @else
                                                            <span class="badge badge-danger">Inactivo</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 text-center">
                                                        <a href="{{ route('resultados-aprendizaje.show', $rap->id) }}" 
                                                           class="btn btn-light btn-sm" 
                                                           data-toggle="tooltip"
                                                           title="Ver RAP">
                                                            <i class="fas fa-eye text-info"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
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

    $(document).ready(function() {
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection
