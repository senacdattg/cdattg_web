@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-user-graduate text-white fa-lg"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Aprendiz</h1>
                        <p class="text-muted mb-0 font-weight-light">Edición del aprendiz</p>
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
                                <a href="{{ route('aprendices.index') }}" class="link_right_header">
                                    <i class="fas fa-user-graduate"></i> Aprendices
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-edit"></i> Editar aprendiz
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
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('aprendices.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Aprendiz
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('aprendices.update', $aprendiz->id) }}" class="row">
                                @csrf
                                @method('PUT')

                                <!-- Información de la Persona (Solo lectura) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="persona_info" class="form-label font-weight-bold">
                                            Persona Asociada
                                        </label>
                                        <input type="text" id="persona_info" 
                                            class="form-control bg-light" 
                                            value="{{ $aprendiz->persona->nombre_completo }} - {{ $aprendiz->persona->numero_documento }}"
                                            readonly>
                                        <input type="hidden" name="persona_id" value="{{ $aprendiz->persona_id }}">
                                        <small class="form-text text-muted">
                                            La persona no se puede cambiar en la edición
                                        </small>
                                    </div>
                                </div>

                                <!-- Ficha de Caracterización -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ficha_caracterizacion_id" class="form-label font-weight-bold">
                                            Ficha de Caracterización <span class="text-danger">*</span>
                                        </label>
                                        <select name="ficha_caracterizacion_id" id="ficha_caracterizacion_id" 
                                            class="form-control select2 @error('ficha_caracterizacion_id') is-invalid @enderror" required>
                                            <option value="">Seleccione una ficha</option>
                                            @foreach ($fichas as $ficha)
                                                <option value="{{ $ficha->id }}" 
                                                    {{ (old('ficha_caracterizacion_id', $aprendiz->ficha_caracterizacion_id) == $ficha->id) ? 'selected' : '' }}>
                                                    {{ $ficha->ficha }} - {{ $ficha->programaFormacion->nombre ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('ficha_caracterizacion_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Ficha de caracterización a la que pertenece el aprendiz
                                        </small>
                                    </div>
                                </div>

                                <!-- Estado -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estado" class="form-label font-weight-bold">Estado</label>
                                        <select name="estado" id="estado" 
                                            class="form-control @error('estado') is-invalid @enderror" required>
                                            <option value="1" {{ old('estado', $aprendiz->estado) == 1 ? 'selected' : '' }}>
                                                Activo
                                            </option>
                                            <option value="0" {{ old('estado', $aprendiz->estado) == 0 ? 'selected' : '' }}>
                                                Inactivo
                                            </option>
                                        </select>
                                        @error('estado')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12">
                                    <hr class="mt-4">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('aprendices.index') }}" class="btn btn-light mr-2">
                                            <i class="fas fa-times mr-1"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i>Guardar Cambios
                                        </button>
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
    @include('layout.alertas')
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2 para todos los select
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Seleccione una opción',
                allowClear: true
            });
        });
    </script>
@endsection
