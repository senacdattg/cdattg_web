@extends('adminlte::page')

@section('title', 'Asignar Rol de Instructor')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
    <style>
        .person-card {
            border: 2px solid #e3e6f0;
            border-radius: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .person-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 8px rgba(0,123,255,0.2);
        }
        .person-card.selected {
            border-color: #007bff;
            background-color: #f8f9ff;
        }
        .person-info {
            padding: 15px;
        }
        .person-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .person-details {
            color: #6c757d;
            font-size: 0.9em;
        }
        .specialty-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 8px 12px;
            margin: 5px;
            display: inline-block;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .specialty-item:hover {
            background: #e9ecef;
        }
        .specialty-item.selected {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .form-section h5 {
            color: #007bff;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
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
                        <p class="text-muted mb-0 font-weight-light">Seleccione una persona y asígnele el rol de instructor</p>
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

            <form action="{{ route('instructor.store') }}" method="post" id="instructorForm">
                @csrf
                
                <!-- Paso 1: Selección de Persona -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-users text-primary mr-2"></i>
                            Paso 1: Seleccionar Persona
                        </h5>
                        <p class="text-muted mb-0 mt-1">Seleccione una persona de la lista para asignarle el rol de instructor</p>
                    </div>
                    <div class="card-body">
                        @if($personas->count() > 0)
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
                                                    <div><i class="fas fa-id-card mr-1"></i> {{ $persona->tipoDocumento->name ?? 'N/A' }}: {{ $persona->numero_documento }}</div>
                                                    <div><i class="fas fa-envelope mr-1"></i> {{ $persona->email }}</div>
                                                    <div><i class="fas fa-user mr-1"></i> Usuario: {{ $persona->user->email }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="persona_id" id="persona_id" required>
                            <div class="text-center mt-3">
                                <span class="text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Haga clic en una persona para seleccionarla
                                </span>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <h5>No hay personas disponibles</h5>
                                <p>No se encontraron personas que puedan ser asignadas como instructores.</p>
                                <p>Las personas deben tener un usuario asociado y no ser instructores actualmente.</p>
                            </div>
                        @endif
                    </div>
                </div>

                @if($personas->count() > 0)
                <!-- Paso 2: Información del Instructor -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chalkboard-teacher text-primary mr-2"></i>
                            Paso 2: Información del Instructor
                        </h5>
                        <p class="text-muted mb-0 mt-1">Complete la información específica del instructor</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="regional_id" class="required-field">Regional</label>
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
                                    <label for="anos_experiencia">Años de Experiencia</label>
                                    <input type="number" 
                                        class="form-control @error('anos_experiencia') is-invalid @enderror"
                                        name="anos_experiencia" 
                                        id="anos_experiencia"
                                        value="{{ old('anos_experiencia') }}"
                                        placeholder="Ingrese los años de experiencia"
                                        min="0" max="50">
                                    @error('anos_experiencia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="experiencia_laboral">Experiencia Laboral</label>
                                    <textarea class="form-control @error('experiencia_laboral') is-invalid @enderror"
                                        name="experiencia_laboral" 
                                        id="experiencia_laboral"
                                        rows="3"
                                        placeholder="Describa la experiencia laboral del instructor">{{ old('experiencia_laboral') }}</textarea>
                                    @error('experiencia_laboral')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 3: Especialidades -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-graduation-cap text-primary mr-2"></i>
                            Paso 3: Asignar Especialidades (Opcional)
                        </h5>
                        <p class="text-muted mb-0 mt-1">Seleccione las especialidades del instructor</p>
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
                                <h6 class="text-muted">Especialidades seleccionadas:</h6>
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

                <!-- Botones de Acción -->
                <div class="card shadow-sm">
                    <div class="card-footer bg-light text-center">
                        <a href="{{ route('instructor.index') }}" class="btn btn-secondary mr-2">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Volver
                        </a>
                        <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                            <i class="fas fa-user-plus mr-1"></i>
                            Asignar como Instructor
                        </button>
                    </div>
                </div>
                @endif
            </form>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            let selectedPersona = null;
            let selectedSpecialties = [];

            // Selección de persona
            $('.person-card').on('click', function() {
                $('.person-card').removeClass('selected');
                $(this).addClass('selected');
                
                selectedPersona = $(this).data('persona-id');
                $('#persona_id').val(selectedPersona);
                
                // Habilitar botón de envío
                $('#submitBtn').prop('disabled', false);
                
                // Mostrar mensaje de selección
                showAlert('Persona seleccionada correctamente', 'success');
            });

            // Selección de especialidades
            $('.specialty-item').on('click', function() {
                const specialtyId = $(this).data('specialty-id');
                const specialtyName = $(this).text().replace('+', '').trim();
                
                if ($(this).hasClass('selected')) {
                    // Deseleccionar
                    $(this).removeClass('selected');
                    selectedSpecialties = selectedSpecialties.filter(id => id != specialtyId);
                } else {
                    // Seleccionar
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

            // Validación del formulario
            $('#instructorForm').on('submit', function(e) {
                if (!selectedPersona) {
                    e.preventDefault();
                    showAlert('Debe seleccionar una persona', 'error');
                    return false;
                }
                
                // Confirmación antes de enviar
                e.preventDefault();
                
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
                        this.submit();
                    }
                });
            });

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
                
                // Auto-remove after 3 seconds
                setTimeout(() => {
                    alert.alert('close');
                }, 3000);
            }
        });
    </script>
@endsection