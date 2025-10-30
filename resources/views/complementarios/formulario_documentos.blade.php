@extends('layout.master-layout-registro')
@extends('layout.alertas')
@section('css')
    @vite(['resources/css/formulario_inscripcion.css'])
@endsection
@section('content')
    @include('complementarios.components.header-programas-publicos')

    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="text-center mb-4">
                    <h2 class="text-success">Subir Documento de Identidad</h2>
                    <p class="text-muted">Complete el proceso de inscripción</p>
                </div>
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-upload mr-2"></i>Subir Documento de Identidad
                        </h3>
                        <div class="card-tools">
                            <a href="{{ url('/login') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-sign-in-alt mr-1"></i> Login
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h4 class="text-muted">{{ $programa->nombre }}</h4>
                        </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif


                        <div class="row">
                            <div class="col-md-8">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-id-card mr-2"></i>Documento de Identidad</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <strong>Importante:</strong> Por favor suba una copia digital de su documento de identidad
                                            en formato PDF.
                                            El archivo debe ser legible y no debe superar los 5MB.
                                        </div>


                                        <form id="formDocumentos" method="POST"
                                            action="{{ route('programas-complementarios.subir-documentos', ['id' => $programa->id]) }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="aspirante_id" value="{{ $aspirante_id }}">

                                            <div class="mb-4">
                                                <label for="documento_identidad" class="form-label">Documento de Identidad (PDF)
                                                    *</label>
                                                <input type="file" class="form-control" id="documento_identidad"
                                                    name="documento_identidad" accept=".pdf" required>
                                                <div class="form-text">
                                                    Formatos aceptados: PDF. Tamaño máximo: 5MB.
                                                </div>
                                            </div>

                                            <div class="form-check mb-4">
                                                <input class="form-check-input" type="checkbox" id="acepto_privacidad" required>
                                                <label class="form-check-label" for="acepto_privacidad">
                                                    Autorizo el tratamiento de mis datos personales de acuerdo con la política de
                                                    privacidad *
                                                </label>
                                            </div>
                                            <input type="hidden" name="acepto_privacidad" id="acepto_privacidad_hidden" value="0">

                                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                                <a href="{{ route('programas-complementarios.inscripcion', $programa->id) }}"
                                                    class="btn btn-outline-secondary me-md-2">
                                                    <i class="fas fa-arrow-left mr-1"></i> Volver Atrás
                                                </a>
                                                <button type="submit" class="btn btn-success" disabled id="btnEnviar">
                                                    <i class="fas fa-paper-plane mr-1"></i> Enviar Documento
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <div class="col-md-4">
                            <div class="card card-widget widget-user">
                                <div class="widget-user-header bg-success">
                                    <h3 class="widget-user-username">Información del Programa</h3>
                                    <h5 class="widget-user-desc">{{ $programa->nombre }}</h5>
                                </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="description-block">
                                        <span class="description-text">DESCRIPCIÓN</span>
                                        <p class="text-muted mb-3">{{ $programa->descripcion }}</p>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text">DURACIÓN</span>
                                                    <h5 class="description-header">{{ $programa->duracion }} horas</h5>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text">MODALIDAD</span>
                                                    <h5 class="description-header">
                                                        {{ $programa->modalidad->parametro->name ?? 'N/A' }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text">JORNADA</span>
                                                    <h5 class="description-header">
                                                        {{ $programa->jornada->jornada ?? 'N/A' }}</h5>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text">CUPO</span>
                                                    <h5 class="description-header">{{ $programa->cupos }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@include('layout.footer')
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
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo no válido',
                    text: 'Solo se permiten archivos PDF.',
                    confirmButtonText: 'Entendido'
                });
                this.value = '';
                actualizarBotonEnviar();
                return;
            }

            if (fileSize > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo demasiado grande',
                    text: 'El archivo es demasiado grande. El tamaño máximo permitido es 5MB.',
                    confirmButtonText: 'Entendido'
                });
                this.value = '';
                actualizarBotonEnviar();
                return;
            }
        }
    });

    // Actualizar el valor del campo hidden cuando cambia el checkbox
    privacidadCheckbox.addEventListener('change', function() {
        document.getElementById('acepto_privacidad_hidden').value = this.checked ? '1' : '0';
        console.log('Checkbox cambiado, valor hidden:', document.getElementById('acepto_privacidad_hidden')
            .value);
    });

    // Validación del formulario
    document.getElementById('formDocumentos').addEventListener('submit', function(e) {
        console.log('=== INICIO SUBIDA DOCUMENTO ===');
        console.log('Formulario enviado - Evento capturado');

        // Validar que se haya seleccionado un archivo
        if (!documentoInput.files.length) {
            console.log('ERROR: No se seleccionó archivo');
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Archivo requerido',
                text: 'Debe seleccionar un archivo PDF.',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        // Validar privacidad
        if (!privacidadCheckbox.checked) {
            console.log('ERROR: No se aceptó la privacidad');
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Política de privacidad',
                text: 'Debe aceptar la política de privacidad para continuar.',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        // Logs para debugging
        console.log('=== FORM SUBMIT DEBUG ===');
        console.log('aspirante_id:', document.querySelector('input[name="aspirante_id"]').value);
        console.log('documento_identidad files:', documentoInput.files);
        console.log('File selected:', documentoInput.files[0]);
        if (documentoInput.files[0]) {
            console.log('File name:', documentoInput.files[0].name);
            console.log('File size:', documentoInput.files[0].size);
            console.log('File type:', documentoInput.files[0].type);
        }
        console.log('Form action:', this.action);
        console.log('Form method:', this.method);
        console.log('Form enctype:', this.enctype);
        console.log('=======================');
        // Deshabilitar botón y mostrar estado mientras se envía
        btnEnviar.disabled = true;
        btnEnviar.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Enviando...';

        console.log('Botón deshabilitado, enviando formulario...');
        console.log('Aspirante ID:', document.querySelector('input[name="aspirante_id"]').value);
        console.log('URL de envío:', this.action);

        // No llamar preventDefault: permitir el envío al backend
        console.log('=== FIN SUBIDA DOCUMENTO - PERMITIENDO ENVÍO ===');
    });

    // Log adicional para verificar que el event listener está funcionando
    console.log('Event listener del formulario registrado correctamente');

    // Log cuando se carga la página
    console.log('=== PÁGINA DE SUBIDA DE DOCUMENTOS CARGADA ===');
    console.log('Programa ID:', {{ $programa->id }});
    console.log('Aspirante ID:', {{ $aspirante_id }});
    console.log('URL de envío:', '{{ route('programas-complementarios.subir-documentos', $programa->id) }}');

    // Mostrar SweetAlert si hay mensaje de éxito (cuenta creada)
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Cuenta creada exitosamente!',
            html: `
                <p>Se ha creado su cuenta de usuario.</p>
                <p><strong>Usuario:</strong> {{ $aspirante_id ? \App\Models\AspiranteComplementario::find($aspirante_id)->persona->email : 'N/A' }}</p>
                <p><strong>Contraseña:</strong> Tu contraseña es tu documento de identidad registrado</p>
                <p class="text-muted">Guarde esta información en un lugar seguro.</p>
            `,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#007bff'
        });
    @endif
</script>
@endsection
