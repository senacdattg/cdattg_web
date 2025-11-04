@extends('adminlte::page')

@section('title', 'Talento Humano')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="mb-0"><i class="fas fa-users me-2"></i>Talento Humano</h1>
            <p class="text-muted mb-0">Consulta información del talento humano</p>
        </div>
    </div>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <form class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Cédula</label>
                    <input type="text" class="form-control form-control-lg" id="cedula" placeholder="Ingrese la cédula">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary btn-lg w-100" id="btn-consultar">
                        <i class="fas fa-search me-1"></i>Consultar
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary btn-lg w-100" id="btn-limpiar">
                        <i class="fas fa-eraser me-1"></i>Limpiar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Formulario de datos personales (inicialmente oculto) -->
    <div id="form-container" class="mt-4" style="display: none;">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title" id="form-title">Información de la Persona</h3>
            </div>
            <div class="card-body">
                <form id="personaForm" action="{{ route('talento-humano.consultar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="action_type" name="action_type" value="consultar">

                    <!-- Documento de Identidad -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="text-primary mb-3">
                                <i class="fas fa-id-card mr-2"></i>Documento de Identidad
                            </h4>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="tipo_documento">Tipo de Documento *</label>
                                <select class="form-control" name="tipo_documento" id="tipo_documento" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($tiposDocumento ?? [] as $tipo)
                                        <option value="{{ $tipo->id }}">
                                            {{ ucwords(strtolower(str_replace('_', ' ', $tipo->name))) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="numero_documento">Número de Documento *</label>
                                <input type="text" class="form-control" id="numero_documento" name="numero_documento" required>
                            </div>
                        </div>
                    </div>

                    <!-- Nombres y Apellidos -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="text-success mb-3">
                                <i class="fas fa-user mr-2"></i>Nombres y Apellidos
                            </h4>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="primer_nombre">Primer Nombre *</label>
                                <input type="text" class="form-control" id="primer_nombre" name="primer_nombre" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="segundo_nombre">Segundo Nombre</label>
                                <input type="text" class="form-control" id="segundo_nombre" name="segundo_nombre">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="primer_apellido">Primer Apellido *</label>
                                <input type="text" class="form-control" id="primer_apellido" name="primer_apellido" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="segundo_apellido">Segundo Apellido</label>
                                <input type="text" class="form-control" id="segundo_apellido" name="segundo_apellido">
                            </div>
                        </div>
                    </div>

                    <!-- Información Personal -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="text-warning mb-3">
                                <i class="fas fa-info-circle mr-2"></i>Información Personal
                            </h4>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="genero">Género *</label>
                                <select class="form-control" id="genero" name="genero" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($generos ?? [] as $genero)
                                        <option value="{{ $genero->id }}">
                                            {{ ucwords(strtolower(str_replace('_', ' ', $genero->name))) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Contacto -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="text-danger mb-3">
                                <i class="fas fa-phone mr-2"></i>Información de Contacto
                            </h4>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="telefono">Teléfono Fijo</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="celular">Celular *</label>
                                <input type="tel" class="form-control" id="celular" name="celular" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="email">Correo Electrónico *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                    </div>

                    <!-- Ubicación -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="text-secondary mb-3">
                                <i class="fas fa-map-marker-alt mr-2"></i>Ubicación Actual
                            </h4>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="pais_id">País *</label>
                                <select class="form-control" name="pais_id" id="pais_id" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($paises ?? [] as $pais)
                                        <option value="{{ $pais->id }}">{{ $pais->pais }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="departamento_id">Departamento *</label>
                                <select class="form-control" name="departamento_id" id="departamento_id" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($departamentos ?? [] as $departamento)
                                        <option value="{{ $departamento->id }}">{{ $departamento->departamento }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="municipio_id">Municipio *</label>
                                <select class="form-control" name="municipio_id" id="municipio_id" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="direccion">Dirección *</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección completa" required>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="toggleAddressForm" aria-expanded="false" aria-controls="addressForm">
                                        <i class="fas fa-edit"></i> Ingresar Dirección Estructurada
                                    </button>
                                    <small class="text-muted ms-2">O ingrese la dirección libremente arriba</small>
                                </div>
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
                                                <button type="button" class="btn btn-success btn-sm mr-2" id="saveAddress">
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
                            <p class="text-muted mb-3">Seleccione una categoría que corresponda a su situación (opcional):</p>
                        </div>
                        @foreach ($categoriasConHijos ?? [] as $categoria)
                            <div class="col-12 mb-4">
                                <h5 class="text-primary mb-2">{{ $categoria->nombre }}</h5>
                                @foreach ($categoria->children as $hijo)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio"
                                               id="categoria_{{ $hijo->id }}" name="caracterizacion_id"
                                               value="{{ $hijo->id }}">
                                        <label class="form-check-label" for="categoria_{{ $hijo->id }}">
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
                                    placeholder="Información adicional que considere relevante..."></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-secondary" id="btn-cancelar" style="display: none;">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btn-crear-persona" style="display: none;">
                    <i class="fas fa-save me-1"></i>Crear Persona
                </button>
                <button type="button" class="btn btn-success" id="btn-guardar-cambios" style="display: none;">
                    <i class="fas fa-save me-1"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .card {
        border: none;
        border-radius: 0.375rem;
    }
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .form-control.is-invalid {
        border-color: #dc3545;
    }
    .form-control.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    .btn {
        border-radius: 0.375rem;
    }
</style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnConsultar = document.getElementById('btn-consultar');
        const btnLimpiar = document.getElementById('btn-limpiar');
        const cedulaInput = document.getElementById('cedula');
        const formContainer = document.getElementById('form-container');

        btnConsultar.addEventListener('click', async function() {
            const cedula = cedulaInput.value.trim();
            if (!cedula) {
                showAlert('warning', 'Por favor ingrese una cédula');
                return;
            }

            // Deshabilitar botón mientras consulta
            const originalText = btnConsultar.innerHTML;
            btnConsultar.disabled = true;
            btnConsultar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Consultando...';

            try {
                const response = await fetch('/talento-humano/consultar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({
                        cedula: cedula
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Mostrar formulario en modo solo lectura
                    formContainer.style.display = 'block';
                    document.getElementById('form-title').textContent = 'Información de la Persona';
                    setFormReadOnly(true);
                    fillFormData(data.data);
                    showAlert('success', data.message);
                } else {
                    if (data.show_form) {
                        // Mostrar formulario para crear nueva persona
                        formContainer.style.display = 'block';
                        document.getElementById('form-title').textContent = 'Crear Nueva Persona';
                        setFormReadOnly(false);
                        // Pre-llenar el número de documento
                        document.getElementById('numero_documento').value = cedula;
                        // Mostrar botones de acción
                        document.getElementById('btn-crear-persona').style.display = 'inline-block';
                        document.getElementById('btn-cancelar').style.display = 'inline-block';
                        showAlert('info', data.message);
                    } else {
                        // Ocultar formulario
                        formContainer.style.display = 'none';
                        showAlert('error', data.message);
                    }
                }

            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Error de conexión. Intente nuevamente.');
            } finally {
                // Restaurar botón
                btnConsultar.disabled = false;
                btnConsultar.innerHTML = originalText;
            }
        });

        btnLimpiar.addEventListener('click', function() {
            cedulaInput.value = '';
            formContainer.style.display = 'none';
            clearFormData();
            console.log('Campos limpiados');
        });

        function fillFormData(data) {
            // Llenar campos del formulario
            document.getElementById('tipo_documento').value = data.tipo_documento || '';
            document.getElementById('numero_documento').value = data.numero_documento || '';
            document.getElementById('primer_nombre').value = data.primer_nombre || '';
            document.getElementById('segundo_nombre').value = data.segundo_nombre || '';
            document.getElementById('primer_apellido').value = data.primer_apellido || '';
            document.getElementById('segundo_apellido').value = data.segundo_apellido || '';
            document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento || '';
            document.getElementById('genero').value = data.genero || '';
            document.getElementById('telefono').value = data.telefono || '';
            document.getElementById('celular').value = data.celular || '';
            document.getElementById('email').value = data.email || '';
            document.getElementById('pais_id').value = data.pais_id || '';
            document.getElementById('departamento_id').value = data.departamento_id || '';
            document.getElementById('direccion').value = data.direccion || '';

            // Cargar municipios si hay departamento
            if (data.departamento_id) {
                loadMunicipios(data.departamento_id, data.municipio_id);
            }

            // Marcar caracterización si existe
            if (data.caracterizacion_id) {
                const caracterizacionRadio = document.querySelector(`input[name="caracterizacion_id"][value="${data.caracterizacion_id}"]`);
                if (caracterizacionRadio) {
                    caracterizacionRadio.checked = true;
                }
            }
        }

        function setFormReadOnly(readOnly) {
            const form = document.getElementById('personaForm');
            const inputs = form.querySelectorAll('input, select, textarea');
            const radios = form.querySelectorAll('input[type="radio"]');

            inputs.forEach(input => {
                if (readOnly) {
                    input.setAttribute('readonly', 'readonly');
                    input.setAttribute('disabled', 'disabled');
                } else {
                    input.removeAttribute('readonly');
                    input.removeAttribute('disabled');
                }
            });

            radios.forEach(radio => {
                if (readOnly) {
                    radio.setAttribute('disabled', 'disabled');
                } else {
                    radio.removeAttribute('disabled');
                }
            });

            // Ocultar/mostrar botones según el modo
            document.getElementById('btn-crear-persona').style.display = readOnly ? 'none' : 'inline-block';
            document.getElementById('btn-guardar-cambios').style.display = 'none';
            document.getElementById('btn-cancelar').style.display = readOnly ? 'none' : 'inline-block';
        }

        function clearFormData() {
            // Limpiar todos los campos del formulario
            const form = document.getElementById('personaForm');
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.type === 'radio' || input.type === 'checkbox') {
                    input.checked = false;
                } else {
                    input.value = '';
                }
            });

            // Ocultar botones de acción
            document.getElementById('btn-crear-persona').style.display = 'none';
            document.getElementById('btn-guardar-cambios').style.display = 'none';
            document.getElementById('btn-cancelar').style.display = 'none';
        }

        function loadMunicipios(departamentoId, selectedMunicipioId = null) {
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
                            if (selectedMunicipioId && municipio.id == selectedMunicipioId) {
                                option.selected = true;
                            }
                            municipioSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error cargando municipios:', error));
            } else {
                municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
            }
        }

        // Cargar municipios según departamento seleccionado
        document.getElementById('departamento_id').addEventListener('change', function() {
            const departamentoId = this.value;
            loadMunicipios(departamentoId);
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
                showAlert('warning', 'Por favor complete todos los campos obligatorios: Carrera, Calle y Número Casa.');
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
                element.addEventListener('keypress', function(event) {
                    const key = event.key;
                    if (event.ctrlKey || event.altKey || event.metaKey) {
                        return true;
                    }
                    if (!/^\d$/.test(key)) {
                        event.preventDefault();
                        return false;
                    }
                    return true;
                });
            }
        });

        // Event listeners para botones de acción
        document.getElementById('btn-crear-persona').addEventListener('click', async function() {
            const form = document.getElementById('personaForm');

            // Validar campos obligatorios antes de enviar
            if (!validateRequiredFields()) {
                return;
            }

            const formData = new FormData(form);

            // Deshabilitar botón
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Creando...';

            try {
                const response = await fetch('/talento-humano/consultar', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: formData
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                    throw new Error(`HTTP ${response.status}: ${errorText}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Cambiar a modo solo lectura
                    setFormReadOnly(true);
                    document.getElementById('form-title').textContent = 'Información de la Persona';
                    // Ocultar botones de acción
                    document.getElementById('btn-crear-persona').style.display = 'none';
                    document.getElementById('btn-cancelar').style.display = 'none';
                    showAlert('success', data.message);
                } else {
                    showAlert('error', data.message);
                }

            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Error de conexión. Intente nuevamente.');
            } finally {
                // Restaurar botón
                this.disabled = false;
                this.innerHTML = originalText;
            }
        });

        // Función para validar campos obligatorios
        function validateRequiredFields() {
            const requiredFields = [
                { id: 'tipo_documento', name: 'Tipo de Documento' },
                { id: 'numero_documento', name: 'Número de Documento' },
                { id: 'primer_nombre', name: 'Primer Nombre' },
                { id: 'primer_apellido', name: 'Primer Apellido' },
                { id: 'fecha_nacimiento', name: 'Fecha de Nacimiento' },
                { id: 'genero', name: 'Género' },
                { id: 'celular', name: 'Celular' },
                { id: 'email', name: 'Correo Electrónico' },
                { id: 'pais_id', name: 'País' },
                { id: 'departamento_id', name: 'Departamento' },
                { id: 'municipio_id', name: 'Municipio' },
                { id: 'direccion', name: 'Dirección' }
            ];

            let isValid = true;
            let firstInvalidField = null;

            requiredFields.forEach(field => {
                const element = document.getElementById(field.id);
                if (!element || !element.value.trim()) {
                    element.classList.add('is-invalid');
                    if (!firstInvalidField) {
                        firstInvalidField = element;
                    }
                    isValid = false;
                } else {
                    element.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                showAlert('warning', 'Por favor complete todos los campos obligatorios marcados con *.');
                if (firstInvalidField) {
                    firstInvalidField.focus();
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }

            return true;
        }

        document.getElementById('btn-cancelar').addEventListener('click', function() {
            formContainer.style.display = 'none';
            clearFormData();
        });

        function showAlert(type, message) {
            // Crear elemento de alerta
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : type === 'info' ? 'info' : 'danger'} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : type === 'info' ? 'info-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            // Agregar al DOM
            document.body.appendChild(alertDiv);

            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    });
</script>

<!-- CSRF Token para AJAX -->
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop