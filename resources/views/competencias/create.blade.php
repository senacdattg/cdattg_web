@extends('adminlte::page')

@section('css')
    @vite(['resources/css/competencias.css'])
    <style>
        .form-section { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e3e6f0; }
        .form-section:last-child { border-bottom: none; }
        .form-section-title { color: #4e73df; font-size: 1.1rem; margin-bottom: 1rem; font-weight: 600; }
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
    <x-page-header 
        icon="fa-clipboard-list" 
        title="Competencias"
        subtitle="Gestión de competencias del SENA"
        :breadcrumb="[['label' => 'Competencias', 'url' => route('competencias.index') , 'icon' => 'fa-clipboard-list'], ['label' => 'Crear', 'icon' => 'fa-plus', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
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

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-plus-circle mr-2"></i>Nueva Competencia
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('competencias.store') }}">
                                @csrf

                                {{-- Sección: Información Básica --}}
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-info-circle mr-1"></i> Información Básica
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="codigo" class="form-label font-weight-bold">Código <span class="text-danger">*</span></label>
                                                <input type="text" name="codigo" id="codigo"
                                                    class="form-control @error('codigo') is-invalid @enderror"
                                                    value="{{ old('codigo') }}" placeholder="Ej: 38356" required>
                                                @error('codigo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Código único de la competencia</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="duracion" class="form-label font-weight-bold">Duración (horas) <span class="text-danger">*</span></label>
                                                <input type="number" name="duracion" id="duracion"
                                                    class="form-control @error('duracion') is-invalid @enderror"
                                                    value="{{ old('duracion') }}" placeholder="Ej: 144" min="1" step="0.01" required>
                                                @error('duracion')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="nombre" class="form-label font-weight-bold">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" name="nombre" id="nombre"
                                            class="form-control @error('nombre') is-invalid @enderror"
                                            value="{{ old('nombre') }}" placeholder="Ej: IMPLANTACIÓN DEL SOFTWARE" required>
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="descripcion" class="form-label font-weight-bold">Descripción</label>
                                        <textarea name="descripcion" id="descripcion" rows="3"
                                            class="form-control @error('descripcion') is-invalid @enderror"
                                            placeholder="Descripción detallada de la competencia...">{{ old('descripcion') }}</textarea>
                                        @error('descripcion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Máximo 1000 caracteres</small>
                                    </div>
                                </div>

                                {{-- Sección: Fechas de Vigencia --}}
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-calendar-alt mr-1"></i> Fechas de Vigencia
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fecha_inicio" class="form-label font-weight-bold">Fecha de Inicio <span class="text-danger">*</span></label>
                                                <input type="date" name="fecha_inicio" id="fecha_inicio"
                                                    class="form-control @error('fecha_inicio') is-invalid @enderror"
                                                    value="{{ old('fecha_inicio') }}" required>
                                                @error('fecha_inicio')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fecha_fin" class="form-label font-weight-bold">Fecha de Fin <span class="text-danger">*</span></label>
                                                <input type="date" name="fecha_fin" id="fecha_fin"
                                                    class="form-control @error('fecha_fin') is-invalid @enderror"
                                                    value="{{ old('fecha_fin') }}" required>
                                                @error('fecha_fin')
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
                                                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Activa</option>
                                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactiva</option>
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
                                    <a href="{{ route('competencias.index') }}" class="btn btn-light mr-2">
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Guardar
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
    @vite(['resources/js/pages/competencias-form.js'])
@endsection
