@extends('adminlte::page')

@section('title', 'Asignar Rol de Instructor')

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
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.85rem;
        }
        .specialty-item:hover {
            background: #e9ecef;
        }
        .specialty-item.selected {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .selected-person-info {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .selected-person-info h6 {
            color: #0c5460;
            margin-bottom: 0.5rem;
        }
        .selected-person-info .person-name {
            font-weight: 600;
            color: #0c5460;
        }
        .selected-person-info .person-details {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        
        .floating-save-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .back-btn {
            position: absolute;
            top: 1rem;
            left: 1rem;
            z-index: 10;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        
        .search-result-item {
            padding: 0.75rem;
            cursor: pointer;
            border-bottom: 1px solid #f8f9fa;
            transition: background-color 0.2s;
        }
        
        .search-result-item:hover {
            background-color: #f8f9fa;
        }
        
        .search-result-item:last-child {
            border-bottom: none;
        }
        
        .person-option {
            font-weight: 500;
            color: #2c3e50;
        }
        
        .person-details-small {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        
        .form-row {
            display: flex;
            gap: 1rem;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            .floating-save-btn {
                bottom: 1rem;
                right: 1rem;
                left: 1rem;
                width: auto;
            }
        }
    </style>
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
                        <h1 class="h3 mb-0 text-gray-800">Asignar Rol de Instructor</h1>
                        <p class="text-muted mb-0 font-weight-light">Asignar instructor a persona existente</p>
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
                                <a href="{{ route('instructor.index') }}" class="link_right_header">
                                    <i class="fas fa-chalkboard-teacher"></i> Instructores
                            </a>
                        </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-user-plus"></i> Asignar Instructor
                        </li>
                    </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <!-- Botón Volver -->
            <div class="back-btn">
                <a href="{{ route('instructor.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver
                </a>
            </div>

            <!-- Alertas -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($personas->count() > 0)
                        <form action="{{ route('instructor.store') }}" method="post" id="instructorForm">
                            @csrf
                            
                    <!-- Información de la Persona -->
                                <div class="form-section">
                        <h6><i class="fas fa-users"></i> Seleccionar Persona</h6>
                        <div class="row compact-form">
                                        <div class="col-md-6">
                                <div class="form-group search-box">
                                    <label for="persona_search" class="form-label required-field">Buscar Persona</label>
                                    <input type="text" 
                                        class="form-control" 
                                        id="persona_search" 
                                        placeholder="Escriba nombre, documento o email..."
                                        autocomplete="off">
                                    <div class="search-results" id="search-results"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                    <label for="persona_id" class="form-label required-field">Persona Seleccionada</label>
                                    <select name="persona_id" id="persona_id" class="form-control @error('persona_id') is-invalid @enderror" required>
                                        <option value="" disabled selected>Seleccione una persona</option>
                                        @foreach($personas as $persona)
                                            <option value="{{ $persona->id }}" 
                                                data-nombre="{{ $persona->primer_nombre }} {{ $persona->primer_apellido }}"
                                                data-documento="{{ $persona->numero_documento }}"
                                                data-email="{{ $persona->email }}"
                                                data-usuario="{{ $persona->user->email }}"
                                                {{ old('persona_id') == $persona->id ? 'selected' : '' }}>
                                                {{ $persona->primer_nombre }} {{ $persona->primer_apellido }} - {{ $persona->numero_documento }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('persona_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                        </div>
                                    </div>
                                </div>

                        <!-- Información de la persona seleccionada -->
                        <div class="selected-person-info" id="selected-person-info" style="display: none;">
                            <h6><i class="fas fa-user-check"></i> Información de la Persona</h6>
                            <div class="person-name" id="selected-person-name"></div>
                            <div class="person-details" id="selected-person-details"></div>
                                    </div>
                                </div>

                    <!-- Información del Instructor -->
                                <div class="form-section">
                        <h6><i class="fas fa-chalkboard-teacher"></i> Información del Instructor</h6>
                        <div class="row compact-form">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                    <label for="regional_id" class="form-label required-field">Regional</label>
                                    <select name="regional_id" id="regional_id" class="form-control @error('regional_id') is-invalid @enderror" required>
                                        <option value="" disabled selected>Seleccione una regional</option>
                                        @foreach($regionales as $regional)
                                            <option value="{{ $regional->id }}" {{ old('regional_id') == $regional->id ? 'selected' : '' }}>
                                                {{ $regional->regional }}
                                                        </option>
                                        @endforeach
                                                </select>
                                    @error('regional_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                    <label for="anos_experiencia" class="form-label">Años de Experiencia</label>
                                    <select name="anos_experiencia" id="anos_experiencia" class="form-control @error('anos_experiencia') is-invalid @enderror">
                                        <option value="" {{ old('anos_experiencia') == '' ? 'selected' : '' }}>Sin especificar</option>
                                        @for($i = 0; $i <= 50; $i++)
                                            <option value="{{ $i }}" {{ old('anos_experiencia') == $i ? 'selected' : '' }}>
                                                {{ $i }} {{ $i == 1 ? 'año' : 'años' }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('anos_experiencia')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                        <div class="row compact-form">
                            <div class="col-12">
                                            <div class="form-group">
                                    <label for="experiencia_laboral" class="form-label">Experiencia Laboral</label>
                                    <textarea class="form-control @error('experiencia_laboral') is-invalid @enderror"
                                        name="experiencia_laboral" 
                                        id="experiencia_laboral"
                                        rows="3"
                                        placeholder="Describa brevemente la experiencia laboral del instructor...">{{ old('experiencia_laboral') }}</textarea>
                                    @error('experiencia_laboral')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                    <!-- Especialidades -->
                                <div class="form-section">
                        <h6><i class="fas fa-graduation-cap"></i> Especialidades (Opcional)</h6>
                        @if($especialidades->count() > 0)
                            <div class="specialties-container">
                                @foreach($especialidades as $especialidad)
                                    <div class="specialty-item" data-specialty-id="{{ $especialidad->id }}">
                                        <i class="fas fa-plus-circle mr-1"></i>
                                        {{ $especialidad->nombre }}
                                    </div>
                                @endforeach
                                </div>
                            <div class="selected-specialties mt-3" style="display: none;">
                                <h6 class="text-muted mb-2">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Especialidades seleccionadas:
                                </h6>
                                <div id="selected-specialties-list"></div>
                            </div>
                            <input type="hidden" name="especialidades" id="especialidades" value="">
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                No hay especialidades disponibles para asignar.
                            </div>
                        @endif
                            </div>
                        </form>

                <!-- Botón Flotante de Guardar -->
                <button type="button" class="btn btn-success floating-save-btn" id="floating-save-btn">
                    <i class="fas fa-save mr-2"></i>
                    Asignar como Instructor
                </button>

            @else
                <!-- Sin Personas Disponibles -->
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay personas disponibles</h4>
                        <p class="text-muted mb-4">
                            No se encontraron personas que puedan ser asignadas como instructores.<br>
                            Las personas deben tener un usuario asociado y no ser instructores actualmente.
                        </p>
                        <a href="{{ route('instructor.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Volver a Instructores
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@section('js')
    <script>
        window.personasData = {!! json_encode($personas->map(function($persona) {
            return [
                'id' => $persona->id,
                'nombre' => $persona->primer_nombre . ' ' . $persona->primer_apellido,
                'documento' => $persona->numero_documento,
                'email' => $persona->email,
                'usuario' => $persona->user->email,
                'tipo_documento' => $persona->tipoDocumento->name ?? 'N/A'
            ];
        })) !!};
    </script>
    <script src="{{ asset('js/instructor-create.js') }}"></script>
@endsection