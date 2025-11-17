@extends('adminlte::page')

@section('title', 'Editar Instructor')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
    <style>
        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #007bff;
        }
        .form-section h6 {
            color: #007bff;
            margin-bottom: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        .form-section h6 i {
            margin-right: 0.5rem;
        }
        
        .compact-form .form-group {
            margin-bottom: 1rem;
        }
        .compact-form .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .compact-form .form-control {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }
        .compact-form .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        .specialty-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 20px;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            display: inline-block;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .specialty-item:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }
        .specialty-item.selected {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-chalkboard-teacher" 
        title="Instructor"
        subtitle="Edición del instructor"
        :breadcrumb="[['label' => 'Instructores', 'url' => route('instructor.index') , 'icon' => 'fa-chalkboard-teacher'], ['label' => 'Editar instructor', 'icon' => 'fa-edit', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <a class="btn btn-outline-secondary btn-sm mb-3" href="{{ route('instructor.index') }}">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-2"></i>Editar Instructor
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('instructor.update', $instructor->id) }}" class="row compact-form">
                                @csrf
                                @method('PUT')

                                <!-- Información Personal -->
                                <div class="col-md-12">
                                    <div class="form-section">
                                        <h6><i class="fas fa-user"></i> Información Personal</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Nombre Completo</label>
                                                    <input type="text" class="form-control" 
                                                           value="{{ $instructor->persona->primer_nombre }} {{ $instructor->persona->segundo_nombre }} {{ $instructor->persona->primer_apellido }} {{ $instructor->persona->segundo_apellido }}" 
                                                           readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Número de Documento</label>
                                                    <input type="text" class="form-control" 
                                                           value="{{ $instructor->persona->numero_documento }}" 
                                                           readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información Institucional -->
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <h6><i class="fas fa-building"></i> Información Institucional</h6>
                                        <div class="form-group">
                                            <label for="regional_id" class="form-label required-field">Regional</label>
                                            <select name="regional_id" id="regional_id" class="form-control @error('regional_id') is-invalid @enderror" required>
                                                <option value="">Seleccione una regional</option>
                                                @foreach($regionales as $regional)
                                                    <option value="{{ $regional->id }}" {{ old('regional_id', $instructor->regional_id) == $regional->id ? 'selected' : '' }}>
                                                        {{ $regional->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('regional_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="status" class="form-label required-field">Estado</label>
                                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                                <option value="1" {{ old('status', $instructor->status) == 1 ? 'selected' : '' }}>Activo</option>
                                                <option value="0" {{ old('status', $instructor->status) == 0 ? 'selected' : '' }}>Inactivo</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Experiencia -->
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <h6><i class="fas fa-briefcase"></i> Experiencia</h6>
                                        <div class="form-group">
                                            <label for="anos_experiencia" class="form-label">Años de Experiencia</label>
                                            <input type="number" name="anos_experiencia" id="anos_experiencia" 
                                                   class="form-control @error('anos_experiencia') is-invalid @enderror" 
                                                   value="{{ old('anos_experiencia', $instructor->anos_experiencia) }}" min="0" max="50">
                                            @error('anos_experiencia')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="experiencia_laboral" class="form-label">Descripción de Experiencia</label>
                                            <textarea name="experiencia_laboral" id="experiencia_laboral" rows="3" 
                                                      class="form-control @error('experiencia_laboral') is-invalid @enderror" 
                                                      placeholder="Describa la experiencia laboral del instructor...">{{ old('experiencia_laboral', $instructor->experiencia_laboral) }}</textarea>
                                            @error('experiencia_laboral')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Especialidades -->
                                <div class="col-md-12">
                                    <div class="form-section">
                                        <h6><i class="fas fa-graduation-cap"></i> Especialidades</h6>
                                        <div class="form-group">
                                            <label for="especialidades" class="form-label">Seleccionar Especialidades</label>
                                            <select name="especialidades[]" id="especialidades" class="form-control" multiple>
                                                @php
                                                    $especialidadesActuales = $instructor->especialidades ?? [];
                                                    $idsEspecialidades = [];
                                                    if (isset($especialidadesActuales['principal'])) {
                                                        // Buscar ID de la especialidad principal
                                                        $especialidadPrincipal = \App\Models\RedConocimiento::where('nombre', $especialidadesActuales['principal'])->first();
                                                        if ($especialidadPrincipal) {
                                                            $idsEspecialidades[] = $especialidadPrincipal->id;
                                                        }
                                                    }
                                                    if (isset($especialidadesActuales['secundarias'])) {
                                                        foreach ($especialidadesActuales['secundarias'] as $especialidadSecundaria) {
                                                            $especialidad = \App\Models\RedConocimiento::where('nombre', $especialidadSecundaria)->first();
                                                            if ($especialidad) {
                                                                $idsEspecialidades[] = $especialidad->id;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @foreach($especialidades as $especialidad)
                                                    <option value="{{ $especialidad->id }}" 
                                                        {{ in_array($especialidad->id, old('especialidades', $idsEspecialidades)) ? 'selected' : '' }}>
                                                        {{ $especialidad->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">
                                                Mantenga presionado Ctrl (Cmd en Mac) para seleccionar múltiples especialidades
                                            </small>
                                        </div>
                                        
                                        <div id="selected-specialties-display" class="mt-2">
                                            @if(isset($especialidadesActuales['principal']))
                                                <div class="d-inline-block px-2 py-1 rounded-pill bg-primary-light text-primary mr-1 mb-1 font-weight-medium">
                                                    <i class="fas fa-star mr-1"></i>{{ $especialidadesActuales['principal'] }}
                                                </div>
                                            @endif
                                            @if(isset($especialidadesActuales['secundarias']))
                                                @foreach($especialidadesActuales['secundarias'] as $especialidad)
                                                    <div class="d-inline-block px-2 py-1 rounded-pill bg-secondary-light text-secondary mr-1 mb-1 font-weight-medium">{{ $especialidad }}</div>
                                                @endforeach
                                            @endif
                                            @if(empty($especialidadesActuales))
                                                <span class="text-muted">Ninguna especialidad asignada</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="col-12">
                                    <hr class="mt-4">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('instructor.index') }}" class="btn btn-light mr-2">
                                            Cancelar
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
    @include('layouts.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/table-page.js'])
@endsection