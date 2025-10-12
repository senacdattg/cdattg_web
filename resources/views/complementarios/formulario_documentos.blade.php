<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Documentos - {{ $programa->nombre }} - SENA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="fas fa-file-upload me-2"></i>Subir Documento de Identidad</h1>
                <p class="text-muted mb-0">Programa: {{ $programa->nombre }}</p>
            </div>
            <div>
                <a href="{{ route('programas-complementarios.publicos') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> Volver a Programas
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif


        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Documento de Identidad</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Importante:</strong> Por favor suba una copia digital de su documento de identidad en formato PDF. 
                            El archivo debe ser legible y no debe superar los 5MB.
                        </div>

                        <form id="formDocumentos" method="POST" action="#">
                            @csrf
                            <input type="hidden" name="aspirante_id" value="{{ session('aspirante_id') }}">

                            <div class="mb-4">
                                <label for="documento_identidad" class="form-label">Documento de Identidad (PDF) *</label>
                                <input type="file" class="form-control" id="documento_identidad" name="documento_identidad" accept=".pdf" required>
                                <div class="form-text">
                                    Formatos aceptados: PDF. Tamaño máximo: 5MB.
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="acepto_privacidad" required>
                                <label class="form-check-label" for="acepto_privacidad">
                                    Autorizo el tratamiento de mis datos personales de acuerdo con la política de privacidad *
                                </label>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('programas-complementarios.inscripcion', $programa->id) }}" class="btn btn-outline-secondary me-md-2">
                                    <i class="fas fa-arrow-left me-1"></i> Volver Atrás
                                </a>
                                <button type="submit" class="btn btn-primary" disabled id="btnEnviar">
                                    <i class="fas fa-paper-plane me-1"></i> Enviar Documento
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card sticky-top shadow-sm" style="top:20px;">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Programa</h5>
                    </div>
                    <div class="card-body">
                        <h6>{{ $programa->nombre }}</h6>
                        <p class="small text-muted">{{ $programa->descripcion }}</p>
                        
                        <div class="mb-2">
                            <strong>Duración:</strong> {{ $programa->duracion }} horas
                        </div>
                        <div class="mb-2">
                            <strong>Modalidad:</strong> {{ $programa->modalidad->parametro->name ?? 'N/A' }}
                        </div>
                        <div class="mb-2">
                            <strong>Jornada:</strong> {{ $programa->jornada->jornada ?? 'N/A' }}
                        </div>
                        <div class="mb-2">
                            <strong>Cupos disponibles:</strong> {{ $programa->cupos }}
                        </div>
                    </div>
                </div>

                <div class="card mt-3 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Estado de la Inscripción</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle p-2 me-3">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div>
                                <strong>En Proceso</strong>
                                <p class="mb-0 small text-muted">Su inscripción está en proceso de revisión</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Habilitar botón de envío cuando se selecciona un archivo y se acepta la privacidad
        const documentoInput = document.getElementById('documento_identidad');
        const privacidadCheckbox = document.getElementById('acepto_privacidad');
        const btnEnviar = document.getElementById('btnEnviar');

        function actualizarBotonEnviar() {
            const archivoSeleccionado = documentoInput.files.length > 0;
            const privacidadAceptada = privacidadCheckbox.checked;
            
            btnEnviar.disabled = !(archivoSeleccionado && privacidadAceptada);
        }

        documentoInput.addEventListener('change', actualizarBotonEnviar);
        privacidadCheckbox.addEventListener('change', actualizarBotonEnviar);

        // Validar tipo de archivo
        documentoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const fileType = file.type;
                const fileSize = file.size;
                const maxSize = 5 * 1024 * 1024; // 5MB

                if (fileType !== 'application/pdf') {
                    alert('Solo se permiten archivos PDF.');
                    this.value = '';
                    actualizarBotonEnviar();
                    return;
                }

                if (fileSize > maxSize) {
                    alert('El archivo es demasiado grande. El tamaño máximo permitido es 5MB.');
                    this.value = '';
                    actualizarBotonEnviar();
                    return;
                }
            }
        });

        // Validación del formulario
        document.getElementById('formDocumentos').addEventListener('submit', function(e) {
            // Validar que se haya seleccionado un archivo
            if (!documentoInput.files.length) {
                e.preventDefault();
                alert('Debe seleccionar un archivo PDF.');
                return;
            }

            // Validar privacidad
            if (!privacidadCheckbox.checked) {
                e.preventDefault();
                alert('Debe aceptar la política de privacidad para continuar.');
                return;
            }

            // El formulario se enviará al servidor
        });
    </script>
</body>
</html>
