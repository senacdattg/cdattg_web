@extends('adminlte::page')

{{-- 
    PLANTILLA DE EDIT
    Reemplazar:
    - {module} = nombre del módulo (ej: usuarios, productos)
    - {Module} = nombre en singular capitalizado (ej: Usuario, Producto)
    - {icon} = icono FontAwesome (ej: fa-users, fa-box)
    - {permission-prefix} = prefijo del permiso (ej: USUARIO, PRODUCTO)
    - $item = elemento a editar
--}}

@section('css')
    @vite(['resources/css/{module}.css'])
    <style>
        .form-section { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e3e6f0; }
        .form-section:last-child { border-bottom: none; }
        .form-section-title { color: #4e73df; font-size: 1.1rem; margin-bottom: 1rem; font-weight: 600; }
        .info-badge { background-color: #e7f1ff; color: #004085; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1.5rem; }
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
                                <i class="fas fa-edit"></i> Editar
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

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar {Module}
                            </h5>
                        </div>
                        <div class="card-body">
                            {{-- Info Actual (Opcional) --}}
                            <div class="info-badge">
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Código actual</small>
                                        <strong>{{ $item->codigo ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Estado</small>
                                        <strong class="{{ $item->status == 1 ? 'text-success' : 'text-danger' }}">
                                            {{ $item->status == 1 ? 'Activo' : 'Inactivo' }}
                                        </strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Creado</small>
                                        <strong>{{ $item->created_at ? $item->created_at->format('d/m/Y') : 'N/A' }}</strong>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('{module}.update', $item) }}">
                                @csrf
                                @method('PUT')

                                {{-- Sección: Información Básica --}}
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-info-circle mr-1"></i> Información Básica
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombre" class="form-label font-weight-bold">Nombre <span class="text-danger">*</span></label>
                                                <input type="text" name="nombre" id="nombre"
                                                    class="form-control @error('nombre') is-invalid @enderror"
                                                    value="{{ old('nombre', $item->nombre) }}" required>
                                                @error('nombre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="codigo" class="form-label font-weight-bold">Código</label>
                                                <input type="text" name="codigo" id="codigo"
                                                    class="form-control @error('codigo') is-invalid @enderror"
                                                    value="{{ old('codigo', $item->codigo) }}">
                                                @error('codigo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Sección: Estado --}}
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-toggle-on mr-1"></i> Estado
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status" class="form-label font-weight-bold">Estado</label>
                                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                                    <option value="1" {{ old('status', $item->status) == '1' ? 'selected' : '' }}>Activo</option>
                                                    <option value="0" {{ old('status', $item->status) == '0' ? 'selected' : '' }}>Inactivo</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Botones de Acción --}}
                                <hr class="mt-4">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('{module}.index') }}" class="btn btn-light mr-2">
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Guardar Cambios
                                    </button>
                                </div>
                            </form>
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

