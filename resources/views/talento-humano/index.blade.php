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
                <h3 class="card-title">Información de la Persona</h3>
            </div>
            <div class="card-body">
                <form id="personaForm">
                    @csrf

                    <!-- Documento de Identidad -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="text-primary mb-3">
                                <i class="fas fa-id-card mr-2"></i>Documento de Identidad
                            </h4>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="tipo_documento">Tipo de Documento</label>
                                <select class="form-control" name="tipo_documento" id="tipo_documento" disabled>
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
                                <label for="numero_documento">Número de Documento</label>
                                <input type="text" class="form-control" id="numero_documento" name="numero_documento" readonly>
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
                                <label for="primer_nombre">Primer Nombre</label>
                                <input type="text" class="form-control" id="primer_nombre" name="primer_nombre" readonly>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="segundo_nombre">Segundo Nombre</label>
                                <input type="text" class="form-control" id="segundo_nombre" name="segundo_nombre" readonly>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="primer_apellido">Primer Apellido</label>
                                <input type="text" class="form-control" id="primer_apellido" name="primer_apellido" readonly>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="segundo_apellido">Segundo Apellido</label>
                                <input type="text" class="form-control" id="segundo_apellido" name="segundo_apellido" readonly>
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
                                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" readonly>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="genero">Género</label>
                                <select class="form-control" id="genero" name="genero" disabled>
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
                                <input type="tel" class="form-control" id="telefono" name="telefono" readonly>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="celular">Celular</label>
                                <input type="tel" class="form-control" id="celular" name="celular" readonly>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="email">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" readonly>
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
                                <label for="pais_id">País</label>
                                <select class="form-control" name="pais_id" id="pais_id" disabled>
                                    <option value="">Seleccione...</option>
                                    @foreach ($paises ?? [] as $pais)
                                        <option value="{{ $pais->id }}">{{ $pais->pais }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="departamento_id">Departamento</label>
                                <select class="form-control" name="departamento_id" id="departamento_id" disabled>
                                    <option value="">Seleccione...</option>
                                    @foreach ($departamentos ?? [] as $departamento)
                                        <option value="{{ $departamento->id }}">{{ $departamento->departamento }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="municipio_id">Municipio</label>
                                <select class="form-control" name="municipio_id" id="municipio_id" disabled>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="direccion">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Caracterización -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="text-dark mb-3">
                                <i class="fas fa-tags mr-2"></i>Caracterización
                            </h4>
                            <p class="text-muted mb-3">Categoría de caracterización:</p>
                        </div>
                        @foreach ($categoriasConHijos ?? [] as $categoria)
                            <div class="col-12 mb-4">
                                <h5 class="text-primary mb-2">{{ $categoria->nombre }}</h5>
                                @foreach ($categoria->children as $hijo)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio"
                                               id="categoria_{{ $hijo->id }}" name="caracterizacion_id"
                                               value="{{ $hijo->id }}" disabled>
                                        <label class="form-check-label" for="categoria_{{ $hijo->id }}">
                                            {{ $hijo->nombre }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </form>
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
                    // Mostrar formulario y llenar datos
                    formContainer.style.display = 'block';
                    fillFormData(data.data);
                    showAlert('success', data.message);
                } else {
                    // Ocultar formulario si no se encuentra
                    formContainer.style.display = 'none';
                    showAlert('error', data.message);
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

        function showAlert(type, message) {
            // Crear elemento de alerta
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'danger'} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'exclamation-triangle'} me-2"></i>
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