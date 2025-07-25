@extends('adminlte::page')

@section('css')
    @vite(['resources/css/temas.css'])
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-clipboard-check text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Registro de Actividades</h1>
                        <p class="text-muted mb-0 font-weight-light">Crear nueva actividad</p>
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
                                <a href="#" class="link_right_header">
                                    <i class="fas fa-clipboard-list"></i> Actividades
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-plus-circle"></i> Nueva Actividad
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
                    <a href="#" class="btn btn-outline-secondary btn-sm mb-3">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card detail-card no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-plus-circle mr-2"></i>Nueva Actividad
                            </h5>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="#" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="form-group row mb-4">
                                    <label class="col-md-3 col-form-label font-weight-bold">
                                        <i class="fas fa-heading mr-2 text-primary"></i>Nombre de la Actividad
                                    </label>
                                    <div class="col-md-9">
                                        <input type="text" name="nombre" value="{{ old('nombre') }}" 
                                            class="form-control @error('nombre') is-invalid @enderror" 
                                            placeholder="Ingrese el nombre de la actividad" required>
                                        @error('nombre')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-md-3 col-form-label font-weight-bold">
                                        <i class="fas fa-align-left mr-2 text-primary"></i>Descripción
                                    </label>
                                    <div class="col-md-9">
                                        <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" 
                                            rows="3" placeholder="Ingrese una descripción" required>{{ old('descripcion') }}</textarea>
                                        @error('descripcion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-md-3 col-form-label font-weight-bold">
                                        <i class="fas fa-book mr-2 text-primary"></i>Guía de Aprendizaje
                                    </label>
                                    <div class="col-md-9">
                                        <select name="guia_aprendizaje_id" 
                                            class="form-control select2 @error('guia_aprendizaje_id') is-invalid @enderror" 
                                            required>
                                            <option value="">Seleccione una Guía de Aprendizaje</option>
                                            @if (isset($guias_aprendizaje))
                                                @foreach ($guias_aprendizaje as $guia_aprendizaje)
                                                    <option value="{{ $guia_aprendizaje->id }}" 
                                                        {{ old('guia_aprendizaje_id') == $guia_aprendizaje->id ? 'selected' : '' }}>
                                                        {{ $guia_aprendizaje->titulo }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('guia_aprendizaje_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-md-3 col-form-label font-weight-bold">
                                        <i class="fas fa-chalkboard-teacher mr-2 text-primary"></i>Docente
                                    </label>
                                    <div class="col-md-9">
                                        <select name="user_id" 
                                            class="form-control select2 @error('user_id') is-invalid @enderror" 
                                            required>
                                            <option value="">Seleccione un Docente</option>
                                            @if (isset($users))
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}" 
                                                        {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('user_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label class="col-md-3 col-form-label font-weight-bold">
                                        <i class="fas fa-paperclip mr-2 text-primary"></i>Archivo de Evidencia
                                    </label>
                                    <div class="col-md-9">
                                        <div class="custom-file">
                                            <input type="file" name="file" 
                                                class="custom-file-input @error('file') is-invalid @enderror" 
                                                id="customFile" required>
                                            <label class="custom-file-label" for="customFile">
                                                <i class="fas fa-upload mr-2"></i>Seleccionar archivo
                                            </label>
                                            <small class="form-text text-muted">
                                                Formatos permitidos: PDF, DOC, DOCX, JPG, PNG (Máx. 5MB)
                                            </small>
                                            @error('file')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mt-5">
                                    <div class="col-md-9 offset-md-3">
                                        <div class="d-flex justify-content-end">
                                            <a href="#" class="btn btn-outline-secondary mr-2">
                                                <i class="fas fa-times mr-1"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save mr-1"></i> Guardar Actividad
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

@push('js')
<script>
    $(document).ready(function() {
        // Inicializar select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Seleccione una opción',
            allowClear: true
        });

        // Actualizar el label del input file con el nombre del archivo seleccionado
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(
                '<i class="fas fa-paperclip mr-2"></i>' + fileName
            );
        });
    });
</script>
@endpush

@push('css')
<style>
    .form-control:focus, .custom-file-input:focus ~ .custom-file-label {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .custom-file-label::after {
        content: "Buscar";
        background-color: #e9ecef;
        border-left: 1px solid #ced4da;
    }
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(1.6em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
    }
</style>
@endpush
