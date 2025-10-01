@extends('adminlte::page')

@section('title', 'Crear Aprendiz')

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
                    <i class="fas fa-user-plus text-white fa-lg"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Crear Aprendiz</h1>
                    <p class="text-muted mb-0 font-weight-light">Registrar nuevo aprendiz</p>
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
                            Crear
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
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Registro de Aprendiz</h3>
                        </div>

                        <form method="POST" action="{{ route('aprendices.store') }}">
                            @csrf
                            
                            <div class="card-body">
                                <!-- Selector de Persona -->
                                <div class="form-group">
                                    <label for="persona_id">Persona <span class="text-danger">*</span></label>
                                    <select name="persona_id" id="persona_id" 
                                        class="form-control select2 @error('persona_id') is-invalid @enderror">
                                        <option value="" selected disabled>Seleccione una persona</option>
                                        @foreach ($personas as $persona)
                                            <option value="{{ $persona->id }}" {{ old('persona_id') == $persona->id ? 'selected' : '' }}>
                                                {{ $persona->nombre_completo }} - {{ $persona->numero_documento }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('persona_id')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Solo se muestran personas que no son aprendices
                                    </small>
                                </div>

                                <!-- Ficha de Caracterización Principal -->
                                <div class="form-group">
                                    <label for="ficha_caracterizacion_id">Ficha de Caracterización Principal <span class="text-danger">*</span></label>
                                    <select name="ficha_caracterizacion_id" id="ficha_caracterizacion_id" 
                                        class="form-control select2 @error('ficha_caracterizacion_id') is-invalid @enderror">
                                        <option value="" selected disabled>Seleccione una ficha</option>
                                        @foreach ($fichas as $ficha)
                                            <option value="{{ $ficha->id }}" {{ old('ficha_caracterizacion_id') == $ficha->id ? 'selected' : '' }}>
                                                {{ $ficha->ficha }} - {{ $ficha->programaFormacion->nombre ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ficha_caracterizacion_id')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Fichas Adicionales (Many-to-Many) -->
                                <div class="form-group">
                                    <label for="fichas">Fichas Adicionales (Opcional)</label>
                                    <select name="fichas[]" id="fichas" 
                                        class="form-control select2" multiple="multiple">
                                        @foreach ($fichas as $ficha)
                                            <option value="{{ $ficha->id }}">
                                                {{ $ficha->ficha }} - {{ $ficha->programaFormacion->nombre ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Puede seleccionar múltiples fichas si el aprendiz pertenece a varios programas
                                    </small>
                                </div>

                                <!-- Estado -->
                                <div class="form-group">
                                    <label for="estado">Estado <span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" id="estado_activo" 
                                            name="estado" value="1" {{ old('estado', '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="estado_activo">
                                            <span class="badge badge-success">ACTIVO</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" id="estado_inactivo" 
                                            name="estado" value="0" {{ old('estado') == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="estado_inactivo">
                                            <span class="badge badge-danger">INACTIVO</span>
                                        </label>
                                    </div>
                                    @error('estado')
                                        <span class="text-danger d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <hr>
                                <p class="text-muted">
                                    <small><span class="text-danger">*</span> Campos obligatorios</small>
                                </p>
                            </div>

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="{{ route('aprendices.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Cancelar
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Guardar Aprendiz
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
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
            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Seleccione una opción',
                allowClear: true
            });
        });
    </script>
@endsection

