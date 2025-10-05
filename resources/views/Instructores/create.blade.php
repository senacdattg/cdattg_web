@extends('adminlte::page')

@section('title', 'Asignar Rol de Instructor')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
    <style>
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            display: flex;
            align-items: center;
            margin: 0 1rem;
        }
        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }
        .step.active .step-number {
            background: #007bff;
            color: white;
        }
        .step.completed .step-number {
            background: #28a745;
            color: white;
        }
        .step-number:not(.active):not(.completed) {
            background: #e9ecef;
            color: #6c757d;
        }
        .step-title {
            font-size: 0.9rem;
            font-weight: 500;
            color: #495057;
        }
        .step.active .step-title {
            color: #007bff;
        }
        .step.completed .step-title {
            color: #28a745;
        }
        
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
        
        .person-card {
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-bottom: 1rem;
        }
        .person-card:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.15);
        }
        .person-card.selected {
            border-color: #007bff;
            background-color: #f8f9ff;
        }
        .person-info {
            padding: 1rem;
        }
        .person-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }
        .person-details {
            color: #6c757d;
            font-size: 0.85rem;
            line-height: 1.4;
        }
        .person-details div {
            margin-bottom: 0.25rem;
        }
        .person-details i {
            width: 16px;
            margin-right: 0.5rem;
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
        
        .floating-save-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
        
        .progress-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .back-btn {
            position: absolute;
            top: 1rem;
            left: 1rem;
            z-index: 10;
        }
        
        .step-content {
            display: none;
        }
        .step-content.active {
            display: block;
        }
        
        @media (max-width: 768px) {
            .step-indicator {
                flex-direction: column;
                align-items: center;
            }
            .step {
                margin: 0.5rem 0;
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
                        <p class="text-muted mb-0 font-weight-light">Proceso rápido para asignar instructor a persona existente</p>
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
                <!-- Indicador de Pasos -->
                <div class="step-indicator">
                    <div class="step active" id="step-1-indicator">
                        <div class="step-number">1</div>
                        <div class="step-title">Seleccionar Persona</div>
                    </div>
                    <div class="step" id="step-2-indicator">
                        <div class="step-number">2</div>
                        <div class="step-title">Información</div>
                    </div>
                    <div class="step" id="step-3-indicator">
                        <div class="step-number">3</div>
                        <div class="step-title">Especialidades</div>
                    </div>
                </div>

                <form action="{{ route('instructor.store') }}" method="post" id="instructorForm">
                    @csrf
                    
                    <!-- Paso 1: Selección de Persona -->
                    <div class="step-content active" id="step-1">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-users text-primary mr-2"></i>
                                    Seleccionar Persona
                                </h5>
                                <p class="text-muted mb-0 mt-1">Seleccione una persona de la lista para asignarle el rol de instructor</p>
                            </div>
                            <div class="card-body">
                                <div class="row" id="personas-container">
                                    @foreach($personas as $persona)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="person-card" data-persona-id="{{ $persona->id }}">
                                                <div class="person-info">
                                                    <div class="person-name">
                                                        {{ $persona->primer_nombre }} {{ $persona->primer_apellido }}
                                                        @if($persona->segundo_nombre)
                                                            {{ $persona->segundo_nombre }}
                                                        @endif
                                                        @if($persona->segundo_apellido)
                                                            {{ $persona->segundo_apellido }}
                                                        @endif
                                                    </div>
                                                    <div class="person-details">
                                                        <div><i class="fas fa-id-card"></i> {{ $persona->tipoDocumento->name ?? 'N/A' }}: {{ $persona->numero_documento }}</div>
                                                        <div><i class="fas fa-envelope"></i> {{ $persona->email }}</div>
                                                        <div><i class="fas fa-user"></i> Usuario: {{ $persona->user->email }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="persona_id" id="persona_id" required>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 2: Información del Instructor -->
                    <div class="step-content" id="step-2">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-chalkboard-teacher text-primary mr-2"></i>
                                    Información del Instructor
                                </h5>
                                <p class="text-muted mb-0 mt-1">Complete la información específica del instructor</p>
                            </div>
                            <div class="card-body">
                                <!-- Persona Seleccionada -->
                                <div class="selected-person-info" id="selected-person-info" style="display: none;">
                                    <h6><i class="fas fa-user-check mr-1"></i> Persona Seleccionada</h6>
                                    <div class="person-name" id="selected-person-name"></div>
                                    <div class="person-details" id="selected-person-details"></div>
                                </div>

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
                                            <input type="number" 
                                                class="form-control @error('anos_experiencia') is-invalid @enderror"
                                                name="anos_experiencia" 
                                                id="anos_experiencia"
                                                value="{{ old('anos_experiencia') }}"
                                                placeholder="Ej: 5"
                                                min="0" max="50">
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
                        </div>
                    </div>

                    <!-- Paso 3: Especialidades -->
                    <div class="step-content" id="step-3">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-graduation-cap text-primary mr-2"></i>
                                    Asignar Especialidades
                                </h5>
                                <p class="text-muted mb-0 mt-1">Seleccione las especialidades del instructor (opcional)</p>
                            </div>
                            <div class="card-body">
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
                        </div>
                    </div>
                </form>

                <!-- Botón Flotante de Guardar -->
                <button type="button" class="btn btn-success floating-save-btn" id="floating-save-btn" disabled>
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
        $(document).ready(function() {
            let selectedPersona = null;
            let selectedSpecialties = [];
            let currentStep = 1;
            const totalSteps = 3;

            // Navegación entre pasos
            function showStep(step) {
                $('.step-content').removeClass('active');
                $(`#step-${step}`).addClass('active');
                
                $('.step').removeClass('active completed');
                for (let i = 1; i <= step; i++) {
                    if (i < step) {
                        $(`#step-${i}-indicator`).addClass('completed');
                    } else {
                        $(`#step-${i}-indicator`).addClass('active');
                    }
                }
                
                currentStep = step;
                updateFloatingButton();
            }

            function updateFloatingButton() {
                const btn = $('#floating-save-btn');
                
                if (currentStep === 1) {
                    btn.text('Continuar').prop('disabled', !selectedPersona);
                } else if (currentStep === 2) {
                    btn.text('Continuar').prop('disabled', false);
                } else if (currentStep === 3) {
                    btn.html('<i class="fas fa-save mr-2"></i>Asignar como Instructor').prop('disabled', false);
                }
            }

            // Selección de persona
            $('.person-card').on('click', function() {
                $('.person-card').removeClass('selected');
                $(this).addClass('selected');
                
                selectedPersona = $(this).data('persona-id');
                $('#persona_id').val(selectedPersona);
                
                // Actualizar info de persona seleccionada
                const personaName = $(this).find('.person-name').text();
                const personaDetails = $(this).find('.person-details').html();
                
                $('#selected-person-name').text(personaName);
                $('#selected-person-details').html(personaDetails);
                $('#selected-person-info').show();
                
                updateFloatingButton();
                showAlert('Persona seleccionada correctamente', 'success');
            });

            // Selección de especialidades
            $('.specialty-item').on('click', function() {
                const specialtyId = $(this).data('specialty-id');
                const specialtyName = $(this).text().replace('+', '').trim();
                
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                    selectedSpecialties = selectedSpecialties.filter(id => id != specialtyId);
                } else {
                    $(this).addClass('selected');
                    selectedSpecialties.push(specialtyId);
                }
                
                updateSelectedSpecialties();
                $('#especialidades').val(JSON.stringify(selectedSpecialties));
            });

            function updateSelectedSpecialties() {
                const container = $('#selected-specialties-list');
                container.empty();
                
                if (selectedSpecialties.length > 0) {
                    $('.selected-specialties').show();
                    
                    selectedSpecialties.forEach(id => {
                        const specialtyElement = $(`.specialty-item[data-specialty-id="${id}"]`);
                        const specialtyName = specialtyElement.text().replace('+', '').trim();
                        
                        container.append(`
                            <span class="badge badge-primary mr-1 mb-1">
                                ${specialtyName}
                                <i class="fas fa-times ml-1" style="cursor: pointer;" onclick="removeSpecialty(${id})"></i>
                            </span>
                        `);
                    });
                } else {
                    $('.selected-specialties').hide();
                }
            }

            // Función global para remover especialidad
            window.removeSpecialty = function(id) {
                selectedSpecialties = selectedSpecialties.filter(specialtyId => specialtyId != id);
                $(`.specialty-item[data-specialty-id="${id}"]`).removeClass('selected');
                updateSelectedSpecialties();
                $('#especialidades').val(JSON.stringify(selectedSpecialties));
            };

            // Botón flotante
            $('#floating-save-btn').on('click', function() {
                if (currentStep < totalSteps) {
                    // Validar paso actual
                    if (currentStep === 1 && !selectedPersona) {
                        showAlert('Debe seleccionar una persona', 'error');
                        return;
                    }
                    if (currentStep === 2) {
                        const regional = $('#regional_id').val();
                        if (!regional) {
                            showAlert('Debe seleccionar una regional', 'error');
                            return;
                        }
                    }
                    
                    showStep(currentStep + 1);
                } else {
                    // Enviar formulario
                    submitForm();
                }
            });

            // Validación del formulario
            function submitForm() {
                if (!selectedPersona) {
                    showAlert('Debe seleccionar una persona', 'error');
                    showStep(1);
                    return false;
                }
                
                const regional = $('#regional_id').val();
                if (!regional) {
                    showAlert('Debe seleccionar una regional', 'error');
                    showStep(2);
                    return false;
                }
                
                Swal.fire({
                    title: '¿Asignar como Instructor?',
                    text: '¿Está seguro de que desea asignar el rol de instructor a esta persona?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Sí, asignar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#instructorForm').submit();
                    }
                });
            }

            // Función para mostrar alertas
            function showAlert(message, type) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
                
                const alert = $(`
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        <i class="fas ${icon} mr-2"></i>
                        ${message}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `);
                
                $('.content').prepend(alert);
                
                setTimeout(() => {
                    alert.alert('close');
                }, 3000);
            }

            // Inicializar
            updateFloatingButton();
        });
    </script>
@endsection