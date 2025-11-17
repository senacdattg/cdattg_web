@extends('adminlte::page')

@section('css')
    @vite(['resources/css/guias_aprendizaje.css'])
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2-bootstrap4.min.css') }}">
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
        .form-section { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e3e6f0; }
        .form-section:last-child { border-bottom: none; }
        .form-section-title { color: #4e73df; font-size: 1.1rem; margin-bottom: 1rem; font-weight: 600; }
        .select2-container--default .select2-selection--multiple { border-color: #d1d3e2; min-height: 38px; }
        .info-badge { background-color: #e7f1ff; color: #004085; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1.5rem; }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-book-open" 
        title="Guías de Aprendizaje"
        subtitle="Gestión de guías de aprendizaje del SENA"
        :breadcrumb="[['label' => 'Guías de Aprendizaje', 'url' => route('guias-aprendizaje.index') , 'icon' => 'fa-book-open'], ['label' => 'Editar', 'icon' => 'fa-edit', 'active' => true]]"
    />
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

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Guía de Aprendizaje
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Info actual -->
                            <div class="info-badge">
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Código actual</small>
                                        <strong>{{ $guiaAprendizaje->codigo }}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Resultados asociados</small>
                                        <strong>{{ $guiaAprendizaje->resultadosAprendizaje->count() }}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Actividades</small>
                                        <strong>{{ $guiaAprendizaje->actividades->count() }}</strong>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('guias-aprendizaje.update', $guiaAprendizaje) }}">
                                @csrf
                                @method('PUT')

                                <!-- Información Básica -->
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
                                                    value="{{ old('codigo', $guiaAprendizaje->codigo) }}" required>
                                                @error('codigo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombre" class="form-label font-weight-bold">Nombre <span class="text-danger">*</span></label>
                                                <input type="text" name="nombre" id="nombre"
                                                    class="form-control @error('nombre') is-invalid @enderror"
                                                    value="{{ old('nombre', $guiaAprendizaje->nombre) }}" required>
                                                @error('nombre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Resultados de Aprendizaje -->
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-target mr-1"></i> Resultados de Aprendizaje
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="resultados_aprendizaje" class="form-label font-weight-bold">
                                                    Seleccionar Resultados <span class="text-danger">*</span>
                                                </label>
                                                <select name="resultados_aprendizaje[]" id="resultados_aprendizaje"
                                                    class="form-control @error('resultados_aprendizaje') is-invalid @enderror"
                                                    multiple="multiple" required>
                                                    @foreach($resultadosAprendizaje as $resultado)
                                                        <option value="{{ $resultado->id }}"
                                                            {{ in_array($resultado->id, old('resultados_aprendizaje', $guiaAprendizaje->resultadosAprendizaje->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                            {{ $resultado->codigo }} - {{ $resultado->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('resultados_aprendizaje')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Estado -->
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-toggle-on mr-1"></i> Estado
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status" class="form-label font-weight-bold">Estado</label>
                                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                                    <option value="1" {{ old('status', $guiaAprendizaje->status) == '1' ? 'selected' : '' }}>Activo</option>
                                                    <option value="0" {{ old('status', $guiaAprendizaje->status) == '0' ? 'selected' : '' }}>Inactivo</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de Acción -->
                                <hr class="mt-4">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('guias-aprendizaje.index') }}" class="btn btn-light mr-2">
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
    @include('layouts.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/guias-aprendizaje-form.js'])
@endsection
