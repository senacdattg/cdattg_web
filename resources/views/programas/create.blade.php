@extends('adminlte::page')

@section('title', 'Crear Programa de Formación')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-plus-circle"></i>
            Crear Programa de Formación
        </h1>
        <a href="{{ route('programa.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5><i class="fas fa-exclamation-triangle"></i> Error en el formulario</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-graduation-cap"></i> Información del Programa
                </h3>
            </div>
            <form action="{{ route('programa.store') }}" method="POST" id="createForm">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <!-- Código del Programa -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigo" class="required">
                                    <i class="fas fa-code"></i> Código del Programa
                                </label>
                                <input type="text" 
                                       name="codigo" 
                                       id="codigo" 
                                       class="form-control @error('codigo') is-invalid @enderror" 
                                       value="{{ old('codigo') }}"
                                       placeholder="Ej: ADSO-001"
                                       maxlength="50"
                                       required>
                                <small class="form-text text-muted">Máximo 50 caracteres</small>
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Nombre del Programa -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="required">
                                    <i class="fas fa-graduation-cap"></i> Nombre del Programa
                                </label>
                                <input type="text" 
                                       name="nombre" 
                                       id="nombre" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre') }}"
                                       placeholder="Ej: Análisis y Desarrollo de Software"
                                       maxlength="255"
                                       required>
                                <small class="form-text text-muted">Máximo 255 caracteres</small>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Red de Conocimiento -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="red_conocimiento_id" class="required">
                                    <i class="fas fa-network-wired"></i> Red de Conocimiento
                                </label>
                                <select name="red_conocimiento_id" 
                                        id="red_conocimiento_id" 
                                        class="form-control @error('red_conocimiento_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Seleccione una red de conocimiento</option>
                                    @foreach ($redesConocimiento as $red)
                                        <option value="{{ $red->id }}" {{ old('red_conocimiento_id') == $red->id ? 'selected' : '' }}>
                                            {{ $red->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('red_conocimiento_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Nivel de Formación -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nivel_formacion_id" class="required">
                                    <i class="fas fa-layer-group"></i> Nivel de Formación
                                </label>
                                <select name="nivel_formacion_id" 
                                        id="nivel_formacion_id" 
                                        class="form-control @error('nivel_formacion_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Seleccione un nivel de formación</option>
                                    @foreach ($nivelesFormacion as $nivel)
                                        <option value="{{ $nivel->id }}" {{ old('nivel_formacion_id') == $nivel->id ? 'selected' : '' }}>
                                            {{ $nivel->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('nivel_formacion_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('programa.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Programa
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Validación en tiempo real
        $('#createForm').on('submit', function(e) {
            let isValid = true;
            
            // Validar campos requeridos
            $('.required').each(function() {
                const input = $(this).next('input, select');
                if (!input.val()) {
                    input.addClass('is-invalid');
                    isValid = false;
                } else {
                    input.removeClass('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                toastr.error('Por favor, complete todos los campos requeridos.');
            }
        });

        // Limpiar validación al escribir
        $('input, select').on('input change', function() {
            $(this).removeClass('is-invalid');
        });
    });
</script>
@endsection

@section('css')
<style>
    .required::after {
        content: " *";
        color: red;
    }
</style>
@endsection
