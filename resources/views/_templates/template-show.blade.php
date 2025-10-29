@extends('adminlte::page')

{{-- 
    PLANTILLA DE SHOW
    Reemplazar:
    - {module} = nombre del módulo (ej: usuarios, productos)
    - {Module} = nombre en singular capitalizado (ej: Usuario, Producto)
    - {icon} = icono FontAwesome (ej: fa-users, fa-box)
    - {permission-prefix} = prefijo del permiso (ej: USUARIO, PRODUCTO)
    - $item = elemento a mostrar
--}}

@section('css')
    @vite(['resources/css/{module}.css'])
    <style>
        .detail-table th { width: 30%; font-weight: 600; background-color: #f8f9fc; border-right: 1px solid #e3e6f0; }
        .detail-table td { padding: 0.75rem 1rem; }
        .detail-table tr { border-bottom: 1px solid #e3e6f0; }
        .detail-table tr:last-child { border-bottom: none; }
    </style>
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas {icon} text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">{Module}s</h1>
                        <p class="text-muted mb-0 font-weight-light">Gestión de {module}s del sistema</p>
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
                                <a href="{{ route('{module}.index') }}" class="link_right_header">
                                    <i class="fas {icon}"></i> {Module}s
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
            {{-- Alertas --}}
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
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('{module}.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle mr-2"></i>Detalle del {Module}
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table detail-table mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="py-3">Nombre</th>
                                            <td class="py-3">{{ $item->nombre }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Código</th>
                                            <td class="py-3">{{ $item->codigo ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Estado</th>
                                            <td class="py-3">
                                                <span class="status-badge {{ $item->status == 1 ? 'text-success' : 'text-danger' }}">
                                                    <i class="fas fa-circle mr-1" style="font-size: 8px;"></i>
                                                    {{ $item->status == 1 ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que crea</th>
                                            <td class="py-3 user-info">
                                                @if ($item->userCreate)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $item->userCreate->persona->primer_nombre ?? '' }}
                                                    {{ $item->userCreate->persona->primer_apellido ?? '' }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de creación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $item->created_at ? $item->created_at->diffForHumans() : 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Usuario que modifica</th>
                                            <td class="py-3 user-info">
                                                @if ($item->userEdit)
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $item->userEdit->persona->primer_nombre ?? '' }}
                                                    {{ $item->userEdit->persona->primer_apellido ?? '' }}
                                                @else
                                                    <span class="text-muted">Usuario no disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="py-3">Fecha de modificación</th>
                                            <td class="py-3 timestamp">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ $item->updated_at ? $item->updated_at->diffForHumans() : 'N/A' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-center gap-2">
                                @can('EDITAR {permission-prefix}')
                                    <a href="{{ route('{module}.edit', $item) }}"
                                        class="btn btn-outline-info btn-sm mx-1">
                                        <i class="fas fa-pencil-alt mr-1"></i> Editar
                                    </a>
                                @endcan
                                @can('ELIMINAR {permission-prefix}')
                                    <button type="button" class="btn btn-outline-danger btn-sm mx-1" 
                                        data-nombre="{{ $item->nombre }}" 
                                        data-url="{{ route('{module}.destroy', $item) }}"
                                        onclick="confirmarEliminacion(this.dataset.nombre, this.dataset.url)">
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
    @vite(['resources/js/pages/formularios-generico.js'])
@endsection

