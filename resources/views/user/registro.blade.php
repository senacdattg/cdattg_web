@extends('layout.master-layout-registro')
@section('content')
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                {{-- Bienvenida al login --}}
                {{-- <a href="{{ route('login') }}" class="h1"><b>registro de asistencias SENA </b></a> --}}
            </div>
            <div class="card-body">
                <p class="login-box-msg">Registrarme</p>

                <form id="registroForm" action="{{ route('registrarme') }}" method="post">
                    @csrf

                    @include('complementarios.components.form-datos-personales', [
                        'context' => 'registro',
                        'userData' => []
                    ])

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-user-plus mr-2"></i>Registrarme
                            </button>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-arrow-left mr-2"></i>Volver
                            </a>
                        </div>
                    </div>

                    <script>
                        // Mostrar preloader al enviar el formulario
                        document.getElementById('registroForm').addEventListener('submit', function() {
                            $('body').addClass('preloader-active');
                        });
                    </script>

            <script>
                // Funcionalidad inline para formulario de registro
                (function() {
                    'use strict';

                    // Constantes de configuración
                    const CONFIG = {
                        EDAD_MINIMA: 14,
                        MENSAJE_EDAD_INVALIDA: 'Debe tener al menos 14 años para registrarse.',
                        CAMPOS_TEXTOS: ['primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido'],
                        CAMPOS_NUMERICOS: ['numero_documento', 'telefono', 'celular'],
                        CAMPOS_DIRECCION_NUMERICOS: ['numero_via', 'numero_casa']
                    };

                    // Función para procesar respuestas de API de ubicaciones
                    function processLocationData(data, entityType) {
                        let items = [];

                        if (data.success && data.data) {
                            items = data.data;
                        } else if (Array.isArray(data)) {
                            items = data;
                        } else if (data && typeof data === 'object') {
                            items = data[entityType] || data.data || [];
                        }

                        return items;
                    }

                    // Función para poblar select con opciones de ubicación
                    function populateLocationSelect(selectElement, items, selectedId, entityType) {
                        if (!Array.isArray(items) || items.length === 0) {
                            console.error(`No se encontraron ${entityType} o estructura inválida:`, items);
                            selectElement.innerHTML = `<option value="">No hay ${entityType} disponibles</option>`;
                            return;
                        }

                        items.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.id;
                            const label = item.nombre ?? item[entityType] ?? item.name ?? item.label ?? '';
                            option.textContent = label || `ID ${item.id}`;
                            if (selectedId && item.id == selectedId) {
                                option.selected = true;
                            }
                            selectElement.appendChild(option);
                        });
                    }

                    // Función para permitir solo números
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

                    // Configurar conversión a mayúsculas
                    function setupUppercaseConversion() {
                        CONFIG.CAMPOS_TEXTOS.forEach(campoId => {
                            const campo = document.getElementById(campoId);
                            if (campo) {
                                campo.addEventListener('input', function() {
                                    this.value = this.value.toUpperCase().replace(/\d/g, '');
                                });

                                campo.addEventListener('keypress', function(e) {
                                    const char = String.fromCharCode(e.keyCode || e.which);
                                    if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]$/.test(char) && !e.ctrlKey && !e.altKey && !e.metaKey) {
                                        e.preventDefault();
                                        return false;
                                    }
                                    return true;
                                });
                            }
                        });
                    }

                    // Configurar validación de números
                    function setupNumberValidation() {
                        CONFIG.CAMPOS_NUMERICOS.forEach(campoId => {
                            const campo = document.getElementById(campoId);
                            if (campo) {
                                campo.addEventListener('keypress', soloNumeros);
                            }
                        });
                    }

                    // Cargar departamentos para un país
                    function loadDepartamentosForPais(paisId, selectedDepartamentoId = null) {
                        const departamentoSelect = document.getElementById('departamento_id');
                        if (!departamentoSelect) return;

                        departamentoSelect.innerHTML = '<option value="">Seleccione...</option>';

                        if (!paisId) return;

                        fetch(`/departamentos/${paisId}`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                const departamentos = processLocationData(data, 'departamentos');
                                populateLocationSelect(departamentoSelect, departamentos, selectedDepartamentoId, 'departamento');
                            })
                            .catch(error => {
                                console.error('Error cargando departamentos:', error);
                                departamentoSelect.innerHTML = '<option value="">Error cargando departamentos</option>';
                            });
                    }

                    // Cargar municipios para un departamento
                    function loadMunicipiosForDepartamento(departamentoId, municipioIdToSelect = null) {
                        const municipioSelect = document.getElementById('municipio_id');
                        if (!municipioSelect) return;

                        municipioSelect.innerHTML = '<option value="">Seleccione...</option>';

                        if (!departamentoId) return;

                        fetch(`/municipios/${departamentoId}`)
                            .then(response => response.json())
                            .then(data => {
                                const municipios = processLocationData(data, 'municipios');
                                populateLocationSelect(municipioSelect, municipios, municipioIdToSelect, 'municipio');
                            })
                            .catch(error => {
                                console.error('Error cargando municipios:', error);
                                municipioSelect.innerHTML = '<option value="">Error cargando municipios</option>';
                            });
                    }

                    // Configurar carga dinámica de municipios
                    function setupMunicipioLoading() {
                        const paisSelect = document.getElementById('pais_id');
                        const departamentoSelect = document.getElementById('departamento_id');

                        if (paisSelect) {
                            paisSelect.addEventListener('change', function() {
                                loadDepartamentosForPais(this.value);
                                const municipioSelect = document.getElementById('municipio_id');
                                if (municipioSelect) {
                                    municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
                                }
                            });
                        }

                        if (departamentoSelect) {
                            departamentoSelect.addEventListener('change', function() {
                                loadMunicipiosForDepartamento(this.value);
                            });
                        }
                    }

                    // Inicializar países dinámicamente para registro
                    function initializePaisesForRegistro() {
                        const paisSelect = document.getElementById('pais_id');
                        if (!paisSelect) return;

                        paisSelect.innerHTML = '<option value="">Seleccione...</option>';

                        fetch('/api/paises')
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                const paises = processLocationData(data, 'paises');
                                populateLocationSelect(paisSelect, paises, null, 'pais');
                            })
                            .catch(error => {
                                console.error('Error cargando países:', error);
                                paisSelect.innerHTML = '<option value="">Error cargando países</option>';
                            });
                    }

                    // Configurar dirección estructurada
                    function setupAddressForm() {
                        const toggleButton = document.getElementById('toggleAddressForm');
                        const saveButton = document.getElementById('saveAddress');
                        const cancelButton = document.getElementById('cancelAddress');

                        if (toggleButton) {
                            toggleButton.addEventListener('click', function() {
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
                        }

                        if (saveButton) {
                            saveButton.addEventListener('click', saveAddress);
                        }

                        if (cancelButton) {
                            cancelButton.addEventListener('click', cancelAddress);
                        }

                        CONFIG.CAMPOS_DIRECCION_NUMERICOS.forEach(fieldId => {
                            const element = document.getElementById(fieldId);
                            if (element) {
                                element.addEventListener('keypress', soloNumeros);
                            }
                        });
                    }

                    // Función auxiliar para obtener valor de campo
                    function getFieldValue(fieldId) {
                        const field = document.getElementById(fieldId);
                        return field ? field.value.trim() : '';
                    }

                    // Construir dirección con formato nuevo
                    function buildNewAddressFormat(tipoVia, numeroVia, letraVia, numeroCasa, viaSecundaria, complementos, barrio) {
                        let direccion = `${tipoVia} ${numeroVia}`;
                        if (letraVia) direccion += letraVia;
                        direccion += ` #${numeroCasa}`;
                        if (viaSecundaria) direccion += ` ${viaSecundaria}`;
                        if (complementos) direccion += ` ${complementos}`;
                        if (barrio) direccion += `, ${barrio}`;
                        return direccion;
                    }

                    // Construir dirección con formato antiguo
                    function buildOldAddressFormat(carrera, calle, numeroCasa, numeroApartamento) {
                        let direccion = `Carrera ${carrera} Calle ${calle} #${numeroCasa}`;
                        if (numeroApartamento) direccion += ` Apt ${numeroApartamento}`;
                        return direccion;
                    }

                    // Validar campos obligatorios de dirección
                    function validateAddressFields(tipoVia, numeroVia, numeroCasa, carrera, calle) {
                        return (tipoVia && numeroVia && numeroCasa) || (carrera && calle && numeroCasa);
                    }

                    // Limpiar campos de dirección
                    function clearAddressFields() {
                        document.querySelectorAll('.address-field').forEach(field => {
                            if (field.type === 'select-one') {
                                field.selectedIndex = 0;
                            } else {
                                field.value = '';
                            }
                        });

                        const oldFields = ['carrera', 'calle', 'numero_apartamento'];
                        oldFields.forEach(fieldId => {
                            const field = document.getElementById(fieldId);
                            if (field) {
                                if (field.type === 'select-one') {
                                    field.selectedIndex = 0;
                                } else {
                                    field.value = '';
                                }
                            }
                        });
                    }

                    // Guardar dirección estructurada
                    function saveAddress() {
                        const tipoVia = getFieldValue('tipo_via');
                        const numeroVia = getFieldValue('numero_via');
                        const letraVia = getFieldValue('letra_via');
                        const viaSecundaria = getFieldValue('via_secundaria');
                        const numeroCasa = getFieldValue('numero_casa');
                        const complementos = getFieldValue('complementos');
                        const barrio = getFieldValue('barrio');

                        const carrera = getFieldValue('carrera');
                        const calle = getFieldValue('calle');
                        const numeroApartamento = getFieldValue('numero_apartamento');

                        if (!validateAddressFields(tipoVia, numeroVia, numeroCasa, carrera, calle)) {
                            alert('Por favor complete todos los campos obligatorios: Tipo de vía, Número de vía y Número de casa.');
                            return;
                        }

                        const direccion = (tipoVia && numeroVia && numeroCasa)
                            ? buildNewAddressFormat(tipoVia, numeroVia, letraVia, numeroCasa, viaSecundaria, complementos, barrio)
                            : buildOldAddressFormat(carrera, calle, numeroCasa, numeroApartamento);

                        document.getElementById('direccion').value = direccion;
                        $('#addressForm').collapse('hide');
                        clearAddressFields();
                    }

                    // Cancelar edición de dirección
                    function cancelAddress() {
                        $('#addressForm').collapse('hide');
                        document.querySelectorAll('.address-field').forEach(field => field.value = '');
                    }

                    // Configurar validación de edad mínima
                    function setupEdadMinimaValidation() {
                        const fechaNacimientoInput = document.getElementById('fecha_nacimiento');
                        if (!fechaNacimientoInput) return;

                        if (!fechaNacimientoInput.getAttribute('max')) {
                            const fechaLimite = new Date();
                            fechaLimite.setFullYear(fechaLimite.getFullYear() - CONFIG.EDAD_MINIMA);
                            fechaNacimientoInput.setAttribute('max', fechaLimite.toISOString().split('T')[0]);
                        }

                        fechaNacimientoInput.addEventListener('change', function() {
                            const fechaSeleccionada = new Date(this.value);
                            const fechaLimite = new Date();
                            fechaLimite.setFullYear(fechaLimite.getFullYear() - CONFIG.EDAD_MINIMA);

                            if (fechaSeleccionada > fechaLimite) {
                                this.setCustomValidity(CONFIG.MENSAJE_EDAD_INVALIDA);
                                this.classList.add('is-invalid');

                                let errorMessage = this.parentElement.querySelector('.invalid-feedback');
                                if (!errorMessage) {
                                    errorMessage = document.createElement('div');
                                    errorMessage.className = 'invalid-feedback';
                                    this.parentElement.appendChild(errorMessage);
                                }
                                errorMessage.textContent = CONFIG.MENSAJE_EDAD_INVALIDA;
                            } else {
                                this.setCustomValidity('');
                                this.classList.remove('is-invalid');

                                const errorMessage = this.parentElement.querySelector('.invalid-feedback');
                                if (errorMessage) {
                                    errorMessage.remove();
                                }
                            }
                        });

                        const form = document.getElementById('registroForm');
                        if (form) {
                            form.addEventListener('submit', function(e) {
                                const fechaSeleccionada = new Date(fechaNacimientoInput.value);
                                const fechaLimite = new Date();
                                fechaLimite.setFullYear(fechaLimite.getFullYear() - CONFIG.EDAD_MINIMA);

                                if (fechaSeleccionada > fechaLimite) {
                                    e.preventDefault();
                                    fechaNacimientoInput.focus();
                                    fechaNacimientoInput.setCustomValidity(CONFIG.MENSAJE_EDAD_INVALIDA);
                                    fechaNacimientoInput.classList.add('is-invalid');

                                    let errorMessage = fechaNacimientoInput.parentElement.querySelector('.invalid-feedback');
                                    if (!errorMessage) {
                                        errorMessage = document.createElement('div');
                                        errorMessage.className = 'invalid-feedback';
                                        fechaNacimientoInput.parentElement.appendChild(errorMessage);
                                    }
                                    errorMessage.textContent = CONFIG.MENSAJE_EDAD_INVALIDA;

                                    alert(CONFIG.MENSAJE_EDAD_INVALIDA);
                                    return false;
                                }
                            });
                        }
                    }

                    // Inicializar todo cuando el DOM esté listo
                    document.addEventListener('DOMContentLoaded', function() {
                        setupUppercaseConversion();
                        setupNumberValidation();
                        setupMunicipioLoading();
                        setupAddressForm();
                        setupEdadMinimaValidation();
                        initializePaisesForRegistro();
                    });

                })();
            </script>

                </form>
                <hr>
                {{-- <a href="{{ route('login') }}" class="text-center">Ya tengo una cuenta</a> --}}
            </div>

        </div>
    </div>

@endsection
