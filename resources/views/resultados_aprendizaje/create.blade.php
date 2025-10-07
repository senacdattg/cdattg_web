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
        .form-section { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e3e6f0; }
        .form-section:last-child { border-bottom: none; }
        .form-section-title { color: #4e73df; font-size: 1.1rem; margin-bottom: 1rem; font-weight: 600; }
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
                        <h1 class="h3 mb-0 text-gray-800">Resultados de Aprendizaje</h1>
                        <p class="text-muted mb-0 font-weight-light">Gestión de resultados de aprendizaje del SENA</p>
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
                                <a href="{{ route('resultados-aprendizaje.index') }}" class="link_right_header">
                                    <i class="fas fa-graduation-cap"></i> Resultados de Aprendizaje
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-plus"></i> Crear
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
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('resultados-aprendizaje.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-plus-circle mr-2"></i>Nuevo Resultado de Aprendizaje
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('resultados-aprendizaje.store') }}">
                                @csrf

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
                                                    value="{{ old('codigo') }}" placeholder="Ej: RAP-001" required>
                                                @error('codigo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Código único de identificación</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombre" class="form-label font-weight-bold">Nombre <span class="text-danger">*</span></label>
                                                <input type="text" name="nombre" id="nombre"
                                                    class="form-control @error('nombre') is-invalid @enderror"
                                                    value="{{ old('nombre') }}" placeholder="Nombre del resultado de aprendizaje" required>
                                                @error('nombre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Nombre descriptivo del RAP</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-clock mr-1"></i> Duración y Fechas
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="duracion" class="form-label font-weight-bold">Duración (Horas)</label>
                                                <input type="number" name="duracion" id="duracion"
                                                    class="form-control @error('duracion') is-invalid @enderror"
                                                    value="{{ old('duracion') }}" placeholder="Ej: 40" min="1" max="9999">
                                                @error('duracion')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="fecha_inicio" class="form-label font-weight-bold">Fecha Inicio</label>
                                                <input type="date" name="fecha_inicio" id="fecha_inicio"
                                                    class="form-control @error('fecha_inicio') is-invalid @enderror"
                                                    value="{{ old('fecha_inicio') }}">
                                                @error('fecha_inicio')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="fecha_fin" class="form-label font-weight-bold">Fecha Fin</label>
                                                <input type="date" name="fecha_fin" id="fecha_fin"
                                                    class="form-control @error('fecha_fin') is-invalid @enderror"
                                                    value="{{ old('fecha_fin') }}">
                                                @error('fecha_fin')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Debe ser igual o posterior a fecha inicio</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-award mr-1"></i> Competencia
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="competencia_id" class="form-label font-weight-bold">Competencia Asociada</label>
                                                <select name="competencia_id" id="competencia_id" class="form-control @error('competencia_id') is-invalid @enderror">
                                                    <option value="">Seleccione una competencia (opcional)</option>
                                                    @foreach($competencias as $competencia)
                                                        <option value="{{ $competencia->id }}" {{ old('competencia_id') == $competencia->id ? 'selected' : '' }}>
                                                            {{ $competencia->codigo ?? '' }} - {{ $competencia->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('competencia_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-toggle-on mr-1"></i> Estado
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status" class="form-label font-weight-bold">Estado</label>
                                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Activo</option>
                                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactivo</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-4">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('resultados-aprendizaje.index') }}" class="btn btn-light mr-2">
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Guardar Resultado
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
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Validar que fecha_fin sea mayor o igual a fecha_inicio
            $('#fecha_fin').on('change', function() {
                const fechaInicio = $('#fecha_inicio').val();
                const fechaFin = $(this).val();

                if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
                    $(this).addClass('is-invalid');
                    if (!$(this).siblings('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">La fecha fin debe ser igual o posterior a la fecha de inicio.</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').remove();
                }
            });
        });
    </script>
@endsection

