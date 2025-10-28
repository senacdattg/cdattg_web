@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-user-graduate" 
        title="Aprendiz"
        subtitle="Edición del aprendiz"
        :breadcrumb="[['label' => 'Aprendices', 'url' => route('aprendices.index') , 'icon' => 'fa-user-graduate'], ['label' => 'Editar aprendiz', 'icon' => 'fa-edit', 'active' => true]]"
    />
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
    @vite(['resources/js/pages/formularios-generico.js'])
@endsection
