@extends('adminlte::page')

@section('title', 'Crear Instructor')

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
        
        .person-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            display: none;
        }
        .person-info.show {
            display: block;
        }
        
        .floating-save-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-chalkboard-teacher" 
        title="Instructor"
        subtitle="Asignar rol de instructor a persona existente"
        :breadcrumb="[['label' => 'Instructores', 'url' => '{{ route('instructor.index') }}', 'icon' => 'fa-chalkboard-teacher'], ['label' => 'Crear instructor', 'icon' => 'fa-plus-circle', 'active' => true]]"
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
                                <i class="fas fa-plus-circle mr-2"></i>Crear Instructor
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('instructor.store') }}" class="row compact-form" id="instructorForm">
                                @csrf
                                
                                <!-- Selección de Persona -->
                                <div class="col-md-12">
                                    <div class="form-section">
                                        <h6><i class="fas fa-user"></i> Seleccionar Persona</h6>
                                        <div class="form-group">
                                            <label for="persona_search" class="form-label required-field">Buscar Persona</label>
                                            <input type="text" id="persona_search" class="form-control" 
                                                   placeholder="Escriba nombre o documento para buscar...">
                                            <select name="persona_id" id="persona_id" class="form-control @error('persona_id') is-invalid @enderror" required style="display: none;">
                                                <option value="">Seleccione una persona</option>
                                                @if(isset($personas))
                                                    @foreach($personas as $persona)
                                                    <option value="{{ $persona->id }}" 
                                                            data-nombre="{{ $persona->primer_nombre }} {{ $persona->segundo_nombre }} {{ $persona->primer_apellido }} {{ $persona->segundo_apellido }}"
                                                            data-documento="{{ $persona->numero_documento }}"
                                                            data-email="{{ $persona->email }}">
                                                        {{ $persona->primer_nombre }} {{ $persona->primer_apellido }} - {{ $persona->numero_documento }}
                                                    </option>
                                                    @endforeach
                                                @else
                                                    <option value="" disabled>No hay personas disponibles</option>
                                                @endif
                                            </select>
                                            @error('persona_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div id="search-results" class="mt-2"></div>
                                        
                                        <div id="selected-person-info" class="person-info">
                                            <h6 class="mb-2"><i class="fas fa-user-check text-success"></i> Persona Seleccionada</h6>
                                            <p class="mb-1"><strong>Nombre:</strong> <span id="selected-name"></span></p>
                                            <p class="mb-1"><strong>Documento:</strong> <span id="selected-document"></span></p>
                                            <p class="mb-0"><strong>Email:</strong> <span id="selected-email"></span></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información del Instructor -->
                                <div class="col-md-6">
                                    <div class="form-section">
                                        <h6><i class="fas fa-building"></i> Información Institucional</h6>
                                        <div class="form-group">
                                            <label for="regional_id" class="form-label required-field">Regional</label>
                                            <select name="regional_id" id="regional_id" class="form-control @error('regional_id') is-invalid @enderror" required>
                                                <option value="">Seleccione una regional</option>
                                                @foreach($regionales as $regional)
                                                    <option value="{{ $regional->id }}" {{ old('regional_id') == $regional->id ? 'selected' : '' }}>
                                                        {{ $regional->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('regional_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="anos_experiencia" class="form-label">Años de Experiencia</label>
                                            <input type="number" name="anos_experiencia" id="anos_experiencia" 
                                                   class="form-control @error('anos_experiencia') is-invalid @enderror" 
                                                   value="{{ old('anos_experiencia') }}" min="0" max="50">
                                            @error('anos_experiencia')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-section">
                                        <h6><i class="fas fa-briefcase"></i> Experiencia Laboral</h6>
                                        <div class="form-group">
                                            <label for="experiencia_laboral" class="form-label">Descripción de Experiencia</label>
                                            <textarea name="experiencia_laboral" id="experiencia_laboral" rows="4" 
                                                      class="form-control @error('experiencia_laboral') is-invalid @enderror" 
                                                      placeholder="Describa la experiencia laboral del instructor...">{{ old('experiencia_laboral') }}</textarea>
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
                                                @foreach($especialidades as $especialidad)
                                                    <option value="{{ $especialidad->id }}" 
                                                        {{ in_array($especialidad->id, old('especialidades', [])) ? 'selected' : '' }}>
                                                        {{ $especialidad->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">
                                                Mantenga presionado Ctrl (Cmd en Mac) para seleccionar múltiples especialidades
                                            </small>
                                        </div>
                                        
                                        <div id="selected-specialties-display" class="mt-2">
                                            <span class="text-muted">Ninguna especialidad seleccionada</span>
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
                                        <button type="submit" class="btn btn-primary" id="saveBtn">
                                            <i class="fas fa-save mr-1"></i>Crear Instructor
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
    <script src="{{ asset('js/instructor-create-data.js') }}"></script>
    <script src="{{ asset('js/instructor-create.js') }}"></script>
@endsection