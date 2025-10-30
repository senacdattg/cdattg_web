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
                    <h2 class="text-success">Formulario de Inscripción</h2>
                    <p class="text-muted">Complete sus datos para inscribirse</p>
                </div>
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-plus mr-2"></i>Formulario de Inscripción
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('programas-complementarios.publicos') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a Programas
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h4 class="text-muted">{{ $programa->nombre }}</h4>
                        </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card card-success">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user mr-2"></i>Información Personales</h5>
                        </div>
                        <div class="card-body">
                            <form id="formInscripcion" method="POST"
                                action="{{ route('programas-complementarios.procesar-inscripcion', $programa->id) }}">
                                @csrf
                                <input type="hidden" name="programa_id" value="{{ $programa->id }}">

                                <div class="card card-success mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-id-card mr-2"></i> Datos personales </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="tipo_documento" class="form-label">Tipo de Documento *</label>
                                                <select class="form-control" id="tipo_documento" name="tipo_documento" required>
                                                    <option value="">Seleccione...</option>
                                                    <option value="1">Cédula de Ciudadanía</option>
                                                    <option value="2">Tarjeta de Identidad</option>
                                                    <option value="3">Cédula de Extranjería</option>
                                                    <option value="4">Pasaporte</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="numero_documento" class="form-label">Número de Documento *</label>
                                                <input type="text" class="form-control" id="numero_documento"
                                                    name="numero_documento" required>
                                            </div>
                                        </div>
                                    </div>
                             
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="primer_nombre" class="form-label">Primer Nombre *</label>
                                                <input type="text" class="form-control" id="primer_nombre" name="primer_nombre"
                                                    required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="segundo_nombre" class="form-label">Segundo Nombre</label>
                                                <input type="text" class="form-control" id="segundo_nombre"
                                                    name="segundo_nombre">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="primer_apellido" class="form-label">Primer Apellido *</label>
                                                <input type="text" class="form-control" id="primer_apellido"
                                                    name="primer_apellido" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                                                <input type="text" class="form-control" id="segundo_apellido"
                                                    name="segundo_apellido">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-success mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-birthday-cake mr-2"></i>Información Personal</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento *</label>
                                                <input type="date" class="form-control" id="fecha_nacimiento"
                                                    name="fecha_nacimiento" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="genero" class="form-label">Género *</label>
                                                <select class="form-control" id="genero" name="genero" required>
                                                    <option value="">Seleccione...</option>
                                                    <option value="1">Masculino</option>
                                                    <option value="2">Femenino</option>
                                                    <option value="3">Otro</option>
                                                    <option value="4">Prefiero no decir</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="telefono" class="form-label">Teléfono Fijo</label>
                                                <input type="tel" class="form-control" id="telefono" name="telefono">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="celular" class="form-label">Celular *</label>
                                                <input type="tel" class="form-control" id="celular" name="celular"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Correo Electrónico *</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-success mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-map-marker-alt mr-2"></i>Ubicación</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="pais_id" class="form-label">País *</label>
                                                <select class="form-control" id="pais_id" name="pais_id" required>
                                                    <option value="">Seleccione...</option>
                                                    @foreach ($paises as $pais)
                                                        <option value="{{ $pais->id }}">{{ $pais->pais }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="departamento_id" class="form-label">Departamento *</label>
                                                <select class="form-control" id="departamento_id" name="departamento_id"
                                                    required>
                                                    <option value="">Seleccione...</option>
                                                    @foreach ($departamentos as $departamento)
                                                        <option value="{{ $departamento->id }}">{{ $departamento->departamento }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="municipio_id" class="form-label">Municipio *</label>
                                                <select class="form-control" id="municipio_id" name="municipio_id" required>
                                                    <option value="">Seleccione...</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="direccion" class="form-label">Dirección *</label>
                                            <input type="text" class="form-control" id="direccion" name="direccion" required>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="card card-success mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-tags mr-2"></i>Caracterización</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3">Seleccione las categorías que correspondan a su situación:</p>

                                        @foreach ($categoriasConHijos as $categoria)
                                            <div class="card card-outline card-success mb-3">
                                                <div class="card-header">
                                                    <h6 class="mb-0">{{ $categoria['nombre'] }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        @foreach ($categoria['hijos'] as $hijo)
                                                            <div class="col-12 mb-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="categorias[]" value="{{ $hijo->id }}"
                                                                        id="categoria_{{ $hijo->id }}">
                                                                    <label class="form-check-label"
                                                                        for="categoria_{{ $hijo->id }}">
                                                                        {{ ucwords(str_replace('_', ' ', $hijo->nombre)) }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="card card-success mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-sticky-note mr-2"></i>Observaciones y Términos</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="observaciones" class="form-label">Observaciones</label>
                                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"
                                                placeholder="Información adicional que considere relevante..."></textarea>
                                        </div>

                                        <div class="form-check mb-4">
                                            <input class="form-check-input" type="checkbox" id="acepto_terminos" required>
                                            <label class="form-check-label" for="acepto_terminos">
                                                Acepto los términos y condiciones del proceso de inscripción *
                                            </label>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="reset" class="btn btn-outline-secondary me-md-2">Limpiar</button>
                                            <button type="submit" class="btn btn-success">Enviar Inscripción</button>
                                        </div>
                                    </div>
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

<script>
    // Cargar municipios según departamento seleccionado
    document.getElementById('departamento_id').addEventListener('change', function() {
        const departamentoId = this.value;
        const municipioSelect = document.getElementById('municipio_id');

        if (departamentoId) {
            fetch(`/municipios/${departamentoId}`)
                .then(response => response.json())
                .then(data => {
                    municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
                    data.forEach(municipio => {
                        const option = document.createElement('option');
                        option.value = municipio.id;
                        option.textContent = municipio.municipio;
                        municipioSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        } else {
            municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
        }
    });

    // Convertir nombres y apellidos a mayúsculas
    const camposTexto = ['primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido'];
    camposTexto.forEach(campo => {
        document.getElementById(campo).addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });

    // Validar que solo contengan números
    function soloNumeros(event) {
        const key = event.key;
        // Permitir teclas de control (backspace, delete, tab, etc.)
        if (event.ctrlKey || event.altKey || event.metaKey) {
            return true;
        }
        // Permitir solo números
        if (!/^\d$/.test(key)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    // Aplicar validación de solo números a los campos
    document.getElementById('numero_documento').addEventListener('keypress', soloNumeros);
    document.getElementById('telefono').addEventListener('keypress', soloNumeros);
    document.getElementById('celular').addEventListener('keypress', soloNumeros);

    // Validación del formulario
    document.getElementById('formInscripcion').addEventListener('submit', function(e) {
        // Validar términos y condiciones
        if (!document.getElementById('acepto_terminos').checked) {
            alert('Debe aceptar los términos y condiciones para continuar.');
            e.preventDefault();
            return;
        }

        // Validar que el número de documento solo contenga números
        const numeroDocumento = document.getElementById('numero_documento').value;
        if (!/^\d+$/.test(numeroDocumento)) {
            alert('El número de documento solo puede contener números.');
            e.preventDefault();
            return;
        }

        // Validar que teléfono fijo solo contenga números (si está lleno)
        const telefono = document.getElementById('telefono').value;
        if (telefono && !/^\d+$/.test(telefono)) {
            alert('El teléfono fijo solo puede contener números.');
            e.preventDefault();
            return;
        }

        // Validar que celular solo contenga números
        const celular = document.getElementById('celular').value;
        if (!/^\d+$/.test(celular)) {
            alert('El celular solo puede contener números.');
            e.preventDefault();
            return;
        }

        // El formulario se enviará al servidor si todas las validaciones pasan
    });
</script>
@include('layout.footer')
@endsection

