@extends('adminlte::page')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .form-section h5 {
            color: #495057;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        .select2-container {
            width: 100% !important;
        }
        .card-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        .btn-custom {
            border-radius: 25px;
            padding: 10px 30px;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }
        .select2-container--default .select2-selection--multiple:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-plus text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Crear Guía de Aprendizaje</h1>
                        <p class="text-muted mb-0 font-weight-light">Complete la información para crear una nueva guía</p>
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
                                <a href="{{ route('guias-aprendizaje.index') }}" class="link_right_header">
                                    <i class="fas fa-book-open"></i> Guías de Aprendizaje
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
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title m-0">
                                <i class="fas fa-book-open mr-2"></i>Información de la Guía de Aprendizaje
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('guias-aprendizaje.store') }}" id="createGuiaForm">
                                @csrf
                                
                                <!-- Información Básica -->
                                <div class="form-section">
                                    <h5><i class="fas fa-info-circle mr-2"></i>Información Básica</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="codigo" class="form-label required-field">Código</label>
                                                <input type="text" 
                                                       class="form-control @error('codigo') is-invalid @enderror" 
                                                       id="codigo" 
                                                       name="codigo" 
                                                       value="{{ old('codigo') }}" 
                                                       placeholder="Ej: GA-001, GA-2024-001"
                                                       maxlength="50"
                                                       required>
                                                @error('codigo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    Código único para identificar la guía de aprendizaje
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombre" class="form-label required-field">Nombre</label>
                                                <input type="text" 
                                                       class="form-control @error('nombre') is-invalid @enderror" 
                                                       id="nombre" 
                                                       name="nombre" 
                                                       value="{{ old('nombre') }}" 
                                                       placeholder="Nombre descriptivo de la guía"
                                                       maxlength="255"
                                                       required>
                                                @error('nombre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    Nombre descriptivo de la guía de aprendizaje
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Resultados de Aprendizaje -->
                                <div class="form-section">
                                    <h5><i class="fas fa-target mr-2"></i>Resultados de Aprendizaje</h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="resultados_aprendizaje" class="form-label required-field">Seleccionar Resultados</label>
                                                <select class="form-control @error('resultados_aprendizaje') is-invalid @enderror" 
                                                        id="resultados_aprendizaje" 
                                                        name="resultados_aprendizaje[]" 
                                                        multiple="multiple" 
                                                        required>
                                                    @foreach($resultadosAprendizaje as $resultado)
                                                        <option value="{{ $resultado->id }}" 
                                                                {{ in_array($resultado->id, old('resultados_aprendizaje', [])) ? 'selected' : '' }}>
                                                            {{ $resultado->codigo }} - {{ $resultado->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('resultados_aprendizaje')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    Seleccione los resultados de aprendizaje que se trabajarán en esta guía
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                <strong>Consejo:</strong> Puede seleccionar múltiples resultados de aprendizaje. 
                                                Use Ctrl+Click (Windows) o Cmd+Click (Mac) para selecciones múltiples.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Configuración Adicional -->
                                <div class="form-section">
                                    <h5><i class="fas fa-cogs mr-2"></i>Configuración</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status" class="form-label">Estado</label>
                                                <select class="form-control @error('status') is-invalid @enderror" 
                                                        id="status" 
                                                        name="status">
                                                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Activo</option>
                                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactivo</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    Estado inicial de la guía de aprendizaje
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de Acción -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('guias-aprendizaje.index') }}" class="btn btn-secondary btn-custom">
                                                <i class="fas fa-arrow-left mr-2"></i>Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-success btn-custom">
                                                <i class="fas fa-save mr-2"></i>Crear Guía de Aprendizaje
                                            </button>
                                        </div>
                                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2 para resultados de aprendizaje
            $('#resultados_aprendizaje').select2({
                placeholder: 'Seleccione los resultados de aprendizaje',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });

            // Validación del formulario
            $('#createGuiaForm').on('submit', function(e) {
                const resultadosSeleccionados = $('#resultados_aprendizaje').val();
                
                if (!resultadosSeleccionados || resultadosSeleccionados.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Error de Validación',
                        text: 'Debe seleccionar al menos un resultado de aprendizaje',
                        icon: 'error',
                        confirmButtonText: 'Entendido'
                    });
                    return false;
                }

                // Mostrar loading
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Creando...');
                submitBtn.prop('disabled', true);

                // Re-habilitar botón después de 3 segundos (en caso de error)
                setTimeout(function() {
                    submitBtn.html(originalText);
                    submitBtn.prop('disabled', false);
                }, 3000);
            });

            // Auto-generar código sugerido
            $('#nombre').on('blur', function() {
                const nombre = $(this).val();
                const codigo = $('#codigo');
                
                if (nombre && !codigo.val()) {
                    const codigoSugerido = 'GA-' + new Date().getFullYear() + '-' + 
                                         nombre.substring(0, 3).toUpperCase().replace(/[^A-Z]/g, '');
                    codigo.val(codigoSugerido);
                }
            });

            // Contador de caracteres
            $('#codigo, #nombre').on('input', function() {
                const maxLength = $(this).attr('maxlength');
                const currentLength = $(this).val().length;
                const remaining = maxLength - currentLength;
                
                // Crear o actualizar contador
                let counter = $(this).siblings('.char-counter');
                if (counter.length === 0) {
                    counter = $('<small class="char-counter text-muted"></small>');
                    $(this).after(counter);
                }
                
                counter.text(`${currentLength}/${maxLength} caracteres`);
                
                if (remaining < 10) {
                    counter.addClass('text-warning');
                } else {
                    counter.removeClass('text-warning');
                }
            });
        });
    </script>
@endsection
