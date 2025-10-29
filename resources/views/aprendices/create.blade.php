@extends('adminlte::page')

@section('title', 'Crear Aprendiz')

@section('css')
    @vite(['resources/css/parametros.css'])
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
            padding: 0.375rem 0.75rem !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.25rem !important;
        }
        
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            padding: 0 !important;
            line-height: 1.5 !important;
        }
        
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
            color: #6c757d !important;
        }
        
        .form-control.select2 {
            height: calc(2.25rem + 2px) !important;
            padding: 0.375rem 0.75rem !important;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-user-plus" 
        title="Crear Aprendiz"
        subtitle="Registrar nuevo aprendiz"
        :breadcrumb="[]"
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
                            <i class="fas fa-user-plus mr-2"></i>Crear Aprendiz
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('aprendices.store') }}" class="row">
                            @csrf

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="persona_id" class="form-label font-weight-bold">Persona <span class="text-danger">*</span></label>
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
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Solo se muestran personas que no son aprendices
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ficha_caracterizacion_id" class="form-label font-weight-bold">Ficha de Caracterización <span class="text-danger">*</span></label>
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
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Ficha de caracterización a la que pertenecerá el aprendiz
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estado" class="form-label font-weight-bold">Estado <span class="text-danger">*</span></label>
                                    <select name="estado" class="form-control @error('estado') is-invalid @enderror" required>
                                        <option value="1" {{ old('estado', '1') == '1' ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <hr class="mt-4">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('aprendices.index') }}" class="btn btn-light mr-2">
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>Guardar Aprendiz
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
@endsection

@section('js')
    @vite(['resources/js/pages/formularios-generico.js'])
@endsection

