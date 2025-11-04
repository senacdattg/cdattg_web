@extends('layout.master-layout-registro')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><i class="fas fa-user-plus mr-2"></i>Inscripción General</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                            <li class="breadcrumb-item active">Inscripción General</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10 col-xl-8">

                        <!-- Alertas -->
                        @if (session('success'))
<<<<<<< HEAD
                            <div class="alert alert-info alert-dismissible">
=======
                            <div class="alert alert-success alert-dismissible">
>>>>>>> develop
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">&times;</button>
                                <i class="icon fas fa-check"></i> {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">&times;</button>
                                <i class="icon fas fa-ban"></i> {{ session('error') }}
                            </div>
                        @endif

<<<<<<< HEAD
                        <div class="card">
                            <div class="card-header" style="background-color: #ffffff; color: #343a40; border-left: 4px solid #007bff;">
=======
                        <div class="card card-primary">
                            <div class="card-header">
>>>>>>> develop
                                <h3 class="card-title">Registro de datos personales y caracterización</h3>
                            </div>

                            <form action="{{ route('inscripcion.procesar') }}" method="post" id="inscripcionForm">
                                @csrf
                                <div class="card-body">

                                    <!-- Documento de Identidad -->
                                    <div class="row mb-4">
                                        <div class="col-12">
<<<<<<< HEAD
                                            <h4 class="text-dark mb-3">
=======
                                            <h4 class="text-primary mb-3">
>>>>>>> develop
                                                <i class="fas fa-id-card mr-2"></i>Documento de Identidad
                                            </h4>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="tipo_documento">Tipo de Documento *</label>
                                                <select class="form-control" name="tipo_documento" id="tipo_documento"
                                                    required>
                                                    <option value="1"
                                                        {{ old('tipo_documento') == '1' ? 'selected' : '' }}>Cédula de
                                                        Ciudadanía</option>
                                                    <option value="2"
                                                        {{ old('tipo_documento') == '2' ? 'selected' : '' }}>Tarjeta de
                                                        Identidad</option>
                                                    <option value="3"
                                                        {{ old('tipo_documento') == '3' ? 'selected' : '' }}>Cédula de
                                                        Extranjería</option>
                                                    <option value="4"
                                                        {{ old('tipo_documento') == '4' ? 'selected' : '' }}>Pasaporte
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="numero_documento">Número de Documento *</label>
                                                <input type="text" class="form-control" id="numero_documento"
                                                    value="{{ old('numero_documento') }}" name="numero_documento"
                                                    placeholder="Número de documento" required inputmode="numeric">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Nombres y Apellidos -->
                                    <div class="row mb-4">
                                        <div class="col-12">
<<<<<<< HEAD
                                            <h4 class="text-dark mb-3">
=======
                                            <h4 class="text-success mb-3">
>>>>>>> develop
                                                <i class="fas fa-user mr-2"></i>Nombres y Apellidos
                                            </h4>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="primer_nombre">Primer Nombre *</label>
                                                <input type="text" class="form-control" id="primer_nombre"
                                                    value="{{ old('primer_nombre') }}" placeholder="Primer Nombre"
                                                    name="primer_nombre" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="segundo_nombre">Segundo Nombre</label>
                                                <input type="text" class="form-control" id="segundo_nombre"
                                                    value="{{ old('segundo_nombre') }}" placeholder="Segundo Nombre"
                                                    name="segundo_nombre">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="primer_apellido">Primer Apellido *</label>
                                                <input type="text" class="form-control" id="primer_apellido"
                                                    value="{{ old('primer_apellido') }}" placeholder="Primer Apellido"
                                                    name="primer_apellido" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="segundo_apellido">Segundo Apellido</label>
                                                <input type="text" class="form-control" id="segundo_apellido"
                                                    value="{{ old('segundo_apellido') }}" placeholder="Segundo Apellido"
                                                    name="segundo_apellido">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Información Personal -->
                                    <div class="row mb-4">
                                        <div class="col-12">
<<<<<<< HEAD
                                            <h4 class="text-dark mb-3">
=======
                                            <h4 class="text-warning mb-3">
>>>>>>> develop
                                                <i class="fas fa-info-circle mr-2"></i>Información Personal
                                            </h4>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                                                <input type="date" class="form-control" id="fecha_nacimiento"
                                                    value="{{ old('fecha_nacimiento') }}" name="fecha_nacimiento"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="genero">Género *</label>
                                                <select class="form-control" id="genero" name="genero" required>
                                                    <option value="">Seleccione...</option>
                                                    <option value="1" {{ old('genero') == '1' ? 'selected' : '' }}>
                                                        Masculino</option>
                                                    <option value="2" {{ old('genero') == '2' ? 'selected' : '' }}>
                                                        Femenino</option>
                                                    <option value="3" {{ old('genero') == '3' ? 'selected' : '' }}>
                                                        Otro</option>
                                                    <option value="4" {{ old('genero') == '4' ? 'selected' : '' }}>
                                                        Prefiero no decir</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contacto -->
                                    <div class="row mb-4">
                                        <div class="col-12">
<<<<<<< HEAD
                                            <h4 class="text-dark mb-3">
=======
                                            <h4 class="text-danger mb-3">
>>>>>>> develop
                                                <i class="fas fa-phone mr-2"></i>Información de Contacto
                                            </h4>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="telefono">Teléfono Fijo</label>
                                                <input type="tel" class="form-control" id="telefono"
                                                    value="{{ old('telefono') }}" name="telefono"
                                                    placeholder="Teléfono fijo" inputmode="tel">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="celular">Celular *</label>
                                                <input type="tel" class="form-control" id="celular"
                                                    value="{{ old('celular') }}" name="celular"
                                                    placeholder="Número de celular" required inputmode="tel">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="email">Correo Electrónico *</label>
                                                <input type="email" class="form-control" id="email"
                                                    value="{{ old('email') }}" placeholder="Correo electrónico"
                                                    name="email" required inputmode="email">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ubicación -->
                                    <div class="row mb-4">
                                        <div class="col-12">
<<<<<<< HEAD
                                            <h4 class="text-dark mb-3">
=======
                                            <h4 class="text-secondary mb-3">
>>>>>>> develop
                                                <i class="fas fa-map-marker-alt mr-2"></i>Ubicación Actual
                                            </h4>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="pais_id">País *</label>
                                                <select class="form-control" name="pais_id" id="pais_id" required>
                                                    <option value="">Seleccione...</option>
                                                    @foreach ($paises as $pais)
                                                        <option value="{{ $pais->id }}"
                                                            {{ old('pais_id') == $pais->id ? 'selected' : '' }}>
                                                            {{ $pais->pais }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="departamento_id">Departamento *</label>
                                                <select class="form-control" name="departamento_id" id="departamento_id"
                                                    required>
                                                    <option value="">Seleccione...</option>
                                                    @foreach ($departamentos as $departamento)
                                                        <option value="{{ $departamento->id }}"
                                                            {{ old('departamento_id') == $departamento->id ? 'selected' : '' }}>
                                                            {{ $departamento->departamento }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-group">
                                                <label for="municipio_id">Municipio *</label>
                                                <select class="form-control" name="municipio_id" id="municipio_id"
                                                    required>
                                                    <option value="">Seleccione...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="direccion">Dirección *</label>
                                                <input type="text" class="form-control" id="direccion"
                                                    value="{{ old('direccion') }}" name="direccion"
                                                    placeholder="Dirección completa" required readonly>
                                                <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="toggleAddressForm" aria-expanded="false" aria-controls="addressForm">
                                                    <i class="fas fa-edit"></i> Ingresar Dirección
                                                </button>
                                            </div>
                                            <div id="addressForm" class="collapse mt-3" aria-labelledby="addressFormLabel">
                                                <div class="card card-outline-secondary">
                                                    <div class="card-header">
                                                        <h5 id="addressFormLabel" class="mb-0">Ingresar Dirección</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-12 col-md-6">
                                                                <div class="form-group">
                                                                    <label for="carrera">Carrera *</label>
                                                                    <input type="text" class="form-control address-field" id="carrera"
                                                                        placeholder="Ej: 1" data-required="true">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="calle">Calle *</label>
                                                                    <input type="text" class="form-control address-field" id="calle"
                                                                        placeholder="Ej: 2" data-required="true">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="numero_casa">Número Casa *</label>
                                                                    <input type="text" class="form-control address-field" id="numero_casa"
                                                                        placeholder="Ej: 3" data-required="true">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="numero_apartamento">Número Apartamento</label>
                                                                    <input type="text" class="form-control address-field" id="numero_apartamento"
                                                                        placeholder="Ej: 4 (opcional)">
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-md-6 d-flex align-items-center">
                                                                <div class="w-100">
                                                                    <p class="mb-2"><strong>Ejemplo de formato:</strong></p>
                                                                    <p class="text-muted">Carrera 1 Calle 2 #3 Apt 4</p>
                                                                    <p class="text-muted small">Los campos marcados con * son obligatorios.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
<<<<<<< HEAD
                                                                <button type="button" class="btn btn-primary btn-sm mr-2" id="saveAddress">
=======
                                                                <button type="button" class="btn btn-success btn-sm mr-2" id="saveAddress">
>>>>>>> develop
                                                                    <i class="fas fa-save"></i> Guardar Dirección
                                                                </button>
                                                                <button type="button" class="btn btn-secondary btn-sm" id="cancelAddress">
                                                                    <i class="fas fa-times"></i> Cancelar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Caracterización -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="text-dark mb-3">
                                                <i class="fas fa-tags mr-2"></i>Caracterización
                                            </h4>
                                            <p class="text-muted mb-3">Seleccione una categoría que corresponda a su
                                                situación (opcional):</p>
                                        </div>
                                        @foreach ($categoriasConHijos as $categoria)
                                            <div class="col-12 mb-4">
                                                <h5 class="text-primary mb-2">{{ $categoria['nombre'] }}</h5>
                                                @foreach ($categoria['hijos'] as $hijo)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="radio"
                                                            id="categoria_{{ $hijo->id }}" name="caracterizacion_id"
                                                            value="{{ $hijo->id }}"
                                                            {{ old('caracterizacion_id') == $hijo->id ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="categoria_{{ $hijo->id }}">
                                                            {{ $hijo->nombre }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Observaciones -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="text-muted mb-3">
                                                <i class="fas fa-comment mr-2"></i>Observaciones
                                            </h4>
                                            <div class="form-group">
                                                <label for="observaciones">Información Adicional</label>
                                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3"
                                                    placeholder="Información adicional que considere relevante...">{{ old('observaciones') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="card-footer">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="acepto_terminos" name="acepto_terminos" required>
                                        <label class="form-check-label" for="acepto_terminos">
                                            Acepto los <a href="#" data-toggle="modal" data-target="#modalTerminos">términos y condiciones</a> *
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save mr-2"></i>Registrar Datos
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
            const elemento = document.getElementsByName(campo)[0];
            if (elemento) {
                elemento.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            }
        });

        // Validar que solo contengan números
        function soloNumeros(event) {
            const key = event.key;
            if (event.ctrlKey || event.altKey || event.metaKey) {
                return true;
            }
            if (!/^\d$/.test(key)) {
                event.preventDefault();
                return false;
            }
            return true;
        }

        // Aplicar validación de solo números
        const camposNumericos = ['numero_documento', 'telefono', 'celular'];
        camposNumericos.forEach(campo => {
            const elemento = document.getElementsByName(campo)[0];
            if (elemento) {
                elemento.addEventListener('keypress', soloNumeros);
            }
        });

        // Funcionalidad del formulario de dirección estructurada
        document.getElementById('toggleAddressForm').addEventListener('click', function() {
            const addressForm = document.getElementById('addressForm');
            const isVisible = addressForm.classList.contains('show');
            const button = this;
            if (isVisible) {
                $('#addressForm').collapse('hide');
                button.setAttribute('aria-expanded', 'false');
            } else {
                $('#addressForm').collapse('show');
                button.setAttribute('aria-expanded', 'true');
            }
        });

        document.getElementById('saveAddress').addEventListener('click', function() {
            const carrera = document.getElementById('carrera').value.trim();
            const calle = document.getElementById('calle').value.trim();
            const numeroCasa = document.getElementById('numero_casa').value.trim();
            const numeroApartamento = document.getElementById('numero_apartamento').value.trim();

            // Validar campos obligatorios
            if (!carrera || !calle || !numeroCasa) {
                alert('Por favor complete todos los campos obligatorios: Carrera, Calle y Número Casa.');
                return;
            }

            // Construir la dirección
            let direccion = `Carrera ${carrera} Calle ${calle} #${numeroCasa}`;
            if (numeroApartamento) {
                direccion += ` Apt ${numeroApartamento}`;
            }

            // Asignar al campo principal
            document.getElementById('direccion').value = direccion;

            // Ocultar el formulario
            $('#addressForm').collapse('hide');

            // Limpiar campos
            document.querySelectorAll('.address-field').forEach(field => field.value = '');
        });

        document.getElementById('cancelAddress').addEventListener('click', function() {
            // Ocultar el formulario
            $('#addressForm').collapse('hide');

            // Limpiar campos
            document.querySelectorAll('.address-field').forEach(field => field.value = '');
        });

        // Validar solo números en campos de dirección
        const addressNumericFields = ['carrera', 'calle', 'numero_casa', 'numero_apartamento'];
        addressNumericFields.forEach(fieldId => {
            const element = document.getElementById(fieldId);
            if (element) {
                element.addEventListener('keypress', soloNumeros);
            }
        });
    </script>

    @include('components.modal-terminos')
@endsection
