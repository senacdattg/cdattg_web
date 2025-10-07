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
        .link_right_header {
            color: #4a5568;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .link_right_header:hover {
            color: #4299e1;
        }
        .breadcrumb-item {
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        .breadcrumb-item i {
            font-size: 0.8rem;
            margin-right: 0.4rem;
        }
        .breadcrumb-item a {
            color: #4a5568;
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: #718096;
        }
        .detail-table th { width: 30%; font-weight: 600; background-color: #f8f9fc; border-right: 1px solid #e3e6f0; }
        .detail-table td { padding: 0.75rem 1rem; }
        .detail-table tr { border-bottom: 1px solid #e3e6f0; }
        .detail-table tr:last-child { border-bottom: none; }
        .section-card { background: #f8f9fc; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; }
        .section-card-title { font-weight: 600; color: #4e73df; margin-bottom: 0.75rem; }
        .list-item { padding: 0.5rem 0; border-bottom: 1px solid #e3e6f0; }
        .list-item:last-child { border-bottom: none; }
    </style>
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-book-open text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Guías de Aprendizaje</h1>
                        <p class="text-muted mb-0 font-weight-light">Gestión de guías de aprendizaje del SENA</p>
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
                                <a href="{{ route('guias-aprendizaje.index') }}" class="link_right_header">
                                    <i class="fas fa-book-open"></i> Guías de Aprendizaje
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
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('guias-aprendizaje.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Detalle de la Guía de Aprendizaje
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Código</th>
                                            <td class="py-3">{{ $guiaAprendizaje->codigo }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Nombre</th>
                                            <td class="py-3">{{ $guiaAprendizaje->nombre }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $guiaAprendizaje->status == 1 ? 'text-success' : 'text-danger' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $guiaAprendizaje->status == 1 ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Resultados de Aprendizaje</th>
                                            <td class="py-3">
                                                <span class="badge badge-info badge-pill">
                                                    {{ $guiaAprendizaje->resultadosAprendizaje->count() }} resultado(s)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Actividades Asociadas</th>
                                            <td class="py-3">
                                                <span class="badge badge-warning badge-pill">
                                                    {{ $guiaAprendizaje->actividades->count() }} actividad(es)
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que crea</th>
                                            <td class="py-3 user-info">
                                                @if ($guiaAprendizaje->userCreate)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $guiaAprendizaje->userCreate->persona->primer_nombre ?? '' }}
                                                    {{ $guiaAprendizaje->userCreate->persona->primer_apellido ?? '' }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de creación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $guiaAprendizaje->created_at ? $guiaAprendizaje->created_at->diffForHumans() : 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que modifica</th>
                                            <td class="py-3 user-info">
                                                @if ($guiaAprendizaje->userEdit)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $guiaAprendizaje->userEdit->persona->primer_nombre ?? '' }}
                                                    {{ $guiaAprendizaje->userEdit->persona->primer_apellido ?? '' }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de modificación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $guiaAprendizaje->updated_at ? $guiaAprendizaje->updated_at->diffForHumans() : 'N/A' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Resultados de Aprendizaje Asociados -->
                        @if($guiaAprendizaje->resultadosAprendizaje->count() > 0)
                            <div class="card-body">
                                <div class="section-card">
                                    <div class="section-card-title text-primary">
                                        <i class="fas fa-target mr-1"></i> Resultados de Aprendizaje Asociados
                                    </div>
                                    @foreach($guiaAprendizaje->resultadosAprendizaje as $resultado)
                                        <div class="list-item">
                                            <strong>{{ $resultado->codigo }}</strong> - {{ $resultado->nombre }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Actividades Asociadas -->
                        @if($guiaAprendizaje->actividades->count() > 0)
                            <div class="card-body">
                                <div class="section-card">
                                    <div class="section-card-title text-primary">
                                        <i class="fas fa-tasks mr-1"></i> Actividades/Evidencias Asociadas ({{ $guiaAprendizaje->actividades->count() }})
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 5%">#</th>
                                                    <th style="width: 20%">Código</th>
                                                    <th style="width: 35%">Nombre</th>
                                                    <th style="width: 15%">Fecha</th>
                                                    <th style="width: 15%">Estado</th>
                                                    <th style="width: 10%">Tipo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($guiaAprendizaje->actividades as $actividad)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td><span class="badge badge-secondary">{{ $actividad->codigo ?? 'N/A' }}</span></td>
                                                        <td>{{ $actividad->nombre }}</td>
                                                        <td>
                                                            @if($actividad->fecha_evidencia)
                                                                <small class="text-muted">
                                                                    <i class="far fa-calendar mr-1"></i>
                                                                    {{ \Carbon\Carbon::parse($actividad->fecha_evidencia)->format('d/m/Y') }}
                                                                </small>
                                                            @else
                                                                <span class="text-muted">Sin fecha</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                $estadoBadge = 'secondary';
                                                                $estadoTexto = 'Sin estado';
                                                                
                                                                if($actividad->id_estado == 25) {
                                                                    $estadoBadge = 'success';
                                                                    $estadoTexto = 'Completada';
                                                                } elseif($actividad->id_estado == 27) {
                                                                    $estadoBadge = 'warning';
                                                                    $estadoTexto = 'Pendiente';
                                                                } elseif($actividad->id_estado) {
                                                                    $estadoBadge = 'info';
                                                                    $estadoTexto = 'En proceso';
                                                                }
                                                            @endphp
                                                            <span class="badge badge-{{ $estadoBadge }}">{{ $estadoTexto }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-primary">{{ $actividad->tipo ?? 'Evidencia' }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-center gap-2">
                                @can('EDITAR GUIA APRENDIZAJE')
                                    <form action="{{ route('guias-aprendizaje.cambiarEstado', $guiaAprendizaje) }}"
                                        method="POST" class="d-inline mx-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-sync mr-1"></i> Cambiar Estado
                                        </button>
                                    </form>
                                    <a href="{{ route('guias-aprendizaje.edit', $guiaAprendizaje) }}"
                                        class="btn btn-outline-info btn-sm mx-1">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan
                                @can('ELIMINAR GUIA APRENDIZAJE')
                                    <button type="button" class="btn btn-outline-danger btn-sm mx-1" 
                                        data-guia="{{ $guiaAprendizaje->codigo }}" 
                                        data-url="{{ route('guias-aprendizaje.destroy', $guiaAprendizaje) }}"
                                        onclick="confirmarEliminacion(this.dataset.guia, this.dataset.url)">
                                        <i class="fas fa-trash mr-1"></i> Eliminar
                                    </button>
                                @endcan
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmarEliminacion(nombre, url) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar la guía "${nombre}"? Esta acción no se puede deshacer.`,
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
    });
</script>
@endsection
