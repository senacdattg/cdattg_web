@extends('complementarios.layout.master')
@section('title', 'Formulario de Inscripción | SENA')
@section('css')
    @vite(['resources/css/formulario_inscripcion.css'])
@endsection
@section('scripts')
    @vite(['resources/js/complementarios/formulario-inscripcion.js'])
@endsection
@section('content')

     <div class="container-fluid mt-4" style="background-color: #ebf1f4; min-height: 100vh;">
         @if(session('user_data'))
             <div class="alert alert-info alert-dismissible">
                 <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                 <h5><i class="icon fas fa-info"></i> Información Pre-llenada</h5>
                 Hemos completado algunos campos con la información de su cuenta.
                 Por favor, complete los campos faltantes y revise que toda la información sea correcta.
             </div>
         @endif
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="text-center mb-4">
                    <h2 class="text-dark">Formulario de Inscripción</h2>
                    <p class="text-muted">Complete sus datos para inscribirse</p>
                </div>
                <div class="card" style="background-color: #ffffff; border-color: #dee2e6;">
                    <div class="card-header card-header-primary">
                        <h3 class="card-title">
                            <i class="fas fa-user-plus mr-2"></i>Formulario de Inscripción
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('programas-complementarios.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a Programas
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h4 class="text-muted">{{ $programa->nombre }}</h4>
                        </div>

            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <div class="card" style="background-color: #ffffff; border-color: #dee2e6;">
                        <div class="card-header card-header-primary">
                            <h5 class="mb-0"><i class="fas fa-user mr-2"></i>Información Personal</h5>
                        </div>
                        <div class="card-body">
                            <form id="formInscripcion" method="POST"
                                action="{{ route('programas-complementarios.procesar-inscripcion', ['programa' => $programa->id]) }}">
                                @csrf
                                <input type="hidden" name="programa_id" value="{{ $programa->id }}">

                                @include('personas.partials.form', [
                                    'persona' => null,
                                    'documentos' => $documentos,
                                    'generos' => $generos,
                                    'caracterizaciones' => $caracterizaciones,
                                    'paises' => $paises,
                                    'departamentos' => $departamentos,
                                    'municipios' => $municipios,
                                    'vias' => $vias,
                                    'letras' => $letras,
                                    'cardinales' => $cardinales,
                                    'showCaracterizacion' => false,
                                ])

                                <hr class="my-5" style="border-color: #dee2e6;">
                                 <div class="card mb-4" style="background-color: #ffffff; border-color: #dee2e6;">
                                     <div class="card-header card-header-primary">
                                         <h5 class="mb-0"><i class="fas fa-id-card mr-2"></i>Documento de Identidad</h5>
                                     </div>
                                     <div class="card-body">
                                         <div class="alert alert-info">
                                             <i class="fas fa-info-circle mr-2"></i>
                                             <strong>Importante:</strong> Por favor suba una copia digital
                                             de su documento de identidad en formato PDF.
                                             El archivo debe ser legible y no debe superar los 5MB.
                                         </div>

                                         <div class="mb-4">
                                             <label for="documento_identidad" class="form-label">
                                                 Documento de Identidad (PDF) *
                                             </label>
                                             <input type="file" class="form-control" id="documento_identidad"
                                                 name="documento_identidad" accept=".pdf" required>
                                             <div class="form-text">
                                                 Formatos aceptados: PDF. Tamaño máximo: 5MB.
                                             </div>
                                         </div>

                                         <div class="form-check mb-4">
                                             <input class="form-check-input" type="checkbox"
                                                    id="acepto_privacidad" name="acepto_privacidad" required>
                                             <label class="form-check-label" for="acepto_privacidad">
                                                 Autorizo el tratamiento de mis datos personales de acuerdo
                                                 con la política de privacidad *
                                             </label>
                                         </div>
                                     </div>
                                 </div>

                                <div class="card mb-4" style="background-color: #ffffff; border-color: #dee2e6;">
                                    <div class="card-header card-header-primary">
                                        <h5 class="mb-0">
                                            <i class="fas fa-sticky-note mr-2"></i>Observaciones y Términos
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="observaciones" class="form-label" style="color: #343a40;">Observaciones</label>
                                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"
                                                placeholder="Información adicional que considere relevante...">{{ old('observaciones') }}</textarea>
                                        </div>

                                        <div class="form-check mb-4">
                                            <input class="form-check-input" type="checkbox"
                                                   id="acepto_terminos" name="acepto_terminos" required>
                                            <label class="form-check-label" for="acepto_terminos">
                                                Acepto los
                                                <a href="#" data-toggle="modal" data-target="#modalTerminos">
                                                    términos y condiciones
                                                </a>
                                                del proceso de inscripción *
                                            </label>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="reset" class="btn btn-outline-secondary me-md-2 mr-3">
                                                Limpiar
                                            </button>
                                            <button type="submit" class="btn btn-primary">Enviar Inscripción</button>
                                        </div>
                                        <script>
                                            // Validar tipo de archivo
                                            document.getElementById('documento_identidad')
                                                .addEventListener('change', function() {
                                                    const file = this.files[0];
                                                    if (file) {
                                                        const fileType = file.type;
                                                        const fileSize = file.size;
                                                        const maxSize = 5 * 1024 * 1024; // 5MB

                                                        if (fileType !== 'application/pdf') {
                                                            Swal.fire({
                                                                icon: 'error',
                                                                title: 'Archivo no válido',
                                                                text: 'Solo se permiten archivos PDF.',
                                                                confirmButtonText: 'Entendido'
                                                            });
                                                            this.value = '';
                                                            return;
                                                        }

                                                        if (fileSize > maxSize) {
                                                            Swal.fire({
                                                                icon: 'error',
                                                                title: 'Archivo demasiado grande',
                                                                text: 'El archivo es demasiado grande. ' +
                                                                      'El tamaño máximo permitido es 5MB.',
                                                                confirmButtonText: 'Entendido'
                                                            });
                                                            this.value = '';
                                                            return;
                                                        }
                                                    }
                                                    actualizarBotonEnviar();
                                                });

                                            // Habilitar botón de envío cuando se selecciona archivo
                                            // y se aceptan términos
                                            const documentoInput = document.getElementById('documento_identidad');
                                            const privacidadCheckbox = document.getElementById('acepto_privacidad');
                                            const terminosCheckbox = document.getElementById('acepto_terminos');
                                            const btnEnviar = document.querySelector('button[type="submit"]');

                                            function actualizarBotonEnviar() {
                                                const archivoSeleccionado = documentoInput.files.length > 0;
                                                const privacidadAceptada = privacidadCheckbox.checked;
                                                const terminosAceptados = terminosCheckbox.checked;

                                                btnEnviar.disabled = !(archivoSeleccionado &&
                                                                      privacidadAceptada &&
                                                                      terminosAceptados);
                                            }

                                            documentoInput.addEventListener('change', actualizarBotonEnviar);
                                            privacidadCheckbox.addEventListener('change', actualizarBotonEnviar);
                                            terminosCheckbox.addEventListener('change', actualizarBotonEnviar);

                                            // Mostrar preloader al enviar el formulario
                                            document.getElementById('formInscripcion')
                                                .addEventListener('submit', function() {
                                                    $('body').addClass('preloader-active');
                                                });
                                        </script>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('components.modal-terminos')
@endsection

