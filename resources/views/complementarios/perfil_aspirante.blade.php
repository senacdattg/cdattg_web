<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Aspirante - SENA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-user-circle me-2"></i>Perfil del Aspirante</h1>
                <p class="text-muted mb-0">Información de la inscripción</p>
            </div>
            <div>
                <a href="{{ route('programas-complementarios.publicos') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> Volver a Programas
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Datos Personales</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Tipo de Documento:</strong><br>
                                @php
                                    $tipoDocumento = match($aspirante->persona->tipo_documento) {
                                        1 => 'Cédula de Ciudadanía',
                                        2 => 'Tarjeta de Identidad',
                                        3 => 'Cédula de Extranjería',
                                        4 => 'Pasaporte',
                                        default => 'No especificado'
                                    };
                                @endphp
                                {{ $tipoDocumento }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Número de Documento:</strong><br>
                                {{ $aspirante->persona->numero_documento }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Nombres:</strong><br>
                                {{ $aspirante->persona->primer_nombre }} 
                                {{ $aspirante->persona->segundo_nombre ?? '' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Apellidos:</strong><br>
                                {{ $aspirante->persona->primer_apellido }} 
                                {{ $aspirante->persona->segundo_apellido ?? '' }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Fecha de Nacimiento:</strong><br>
                                {{ \Carbon\Carbon::parse($aspirante->persona->fecha_nacimiento)->format('d/m/Y') }}
                                ({{ $aspirante->persona->edad }} años)
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Género:</strong><br>
                                @php
                                    $genero = match($aspirante->persona->genero) {
                                        1 => 'Masculino',
                                        2 => 'Femenino',
                                        3 => 'Otro',
                                        4 => 'Prefiero no decir',
                                        default => 'No especificado'
                                    };
                                @endphp
                                {{ $genero }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Teléfono Fijo:</strong><br>
                                {{ $aspirante->persona->telefono ?? 'No registrado' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Celular:</strong><br>
                                {{ $aspirante->persona->celular }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Correo Electrónico:</strong><br>
                            {{ $aspirante->persona->email }}
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <strong>País:</strong><br>
                                {{ $aspirante->persona->pais->pais ?? 'No especificado' }}
                            </div>
                            <div class="col-md-4 mb-3">
                                <strong>Departamento:</strong><br>
                                {{ $aspirante->persona->departamento->departamento ?? 'No especificado' }}
                            </div>
                            <div class="col-md-4 mb-3">
                                <strong>Municipio:</strong><br>
                                {{ $aspirante->persona->municipio->municipio ?? 'No especificado' }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Dirección:</strong><br>
                            {{ $aspirante->persona->direccion }}
                        </div>

                        @if($aspirante->observaciones)
                        <div class="mb-3">
                            <strong>Observaciones:</strong><br>
                            {{ $aspirante->observaciones }}
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial de la Inscripción</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Inscripción Completada</h6>
                                    <p class="mb-1 text-muted small">Datos personales registrados</p>
                                    <span class="text-muted small">{{ $aspirante->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Documento Subido</h6>
                                    <p class="mb-1 text-muted small">Documento de identidad enviado para revisión</p>
                                    <span class="text-muted small">{{ $aspirante->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">En Revisión</h6>
                                    <p class="mb-1 text-muted small">Su solicitud está siendo revisada por el equipo administrativo</p>
                                    <span class="text-muted small">En proceso</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card sticky-top shadow-sm" style="top:20px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Estado de la Inscripción</h5>
                    </div>
                    <div class="card-body text-center">
                        @php
                            $estadoClass = match($aspirante->estado) {
                                1 => 'bg-primary',
                                2 => 'bg-danger',
                                3 => 'bg-success',
                                default => 'bg-secondary'
                            };
                            
                            $estadoIcon = match($aspirante->estado) {
                                1 => 'fas fa-clock',
                                2 => 'fas fa-times-circle',
                                3 => 'fas fa-check-circle',
                                default => 'fas fa-question-circle'
                            };
                        @endphp
                        
                        <div class="{{ $estadoClass }} text-white rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="{{ $estadoIcon }} fa-2x"></i>
                        </div>
                        
                        <h4 class="mb-2">{{ $aspirante->estado_label }}</h4>
                        
                        @if($aspirante->estado == 1)
                            <p class="text-muted">Su inscripción está siendo revisada. Recibirá una notificación por correo electrónico cuando haya una actualización.</p>
                        @elseif($aspirante->estado == 2)
                            <p class="text-muted">Lamentablemente su solicitud no fue aprobada. Puede contactar al área administrativa para más información.</p>
                        @elseif($aspirante->estado == 3)
                            <p class="text-muted">¡Felicidades! Su inscripción ha sido aprobada. Pronto recibirá información sobre el inicio del programa.</p>
                        @endif
                        
                        <div class="mt-4">
                            <small class="text-muted">ID de Inscripción: {{ $aspirante->id }}</small><br>
                            <small class="text-muted">Fecha de registro: {{ $aspirante->created_at->format('d/m/Y') }}</small>
                        </div>
                    </div>
                </div>

                <div class="card mt-3 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-file-pdf me-2"></i>Documentos</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-id-card text-primary me-3 fa-lg"></i>
                            <div>
                                <strong>Documento de Identidad</strong>
                                <p class="mb-0 small text-muted">PDF subido</p>
                            </div>
                        </div>
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-2"></i>
                            Los documentos están siendo revisados por el equipo administrativo.
                        </div>
                    </div>
                </div>

                <div class="card mt-3 shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-phone me-2"></i>Contacto</h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <strong>Correo:</strong> administrativo@sena.edu.co
                        </p>
                        <p class="small mb-0">
                            <i class="fas fa-phone me-2"></i>
                            <strong>Teléfono:</strong> (601) 546 1500
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-marker {
            position: absolute;
            left: -30px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        
        .timeline-content {
            padding-left: 10px;
        }
        
        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 12px;
            bottom: -20px;
            width: 2px;
            background-color: #dee2e6;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
