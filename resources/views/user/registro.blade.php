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
                        'userData' => [],
                        'paises' => \App\Models\Pais::all(),
                        'departamentos' => \App\Models\Departamento::all(),
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
                        document.addEventListener('DOMContentLoaded', function() {
                            // Configurar conversión a mayúsculas
                            setupUppercaseConversion();

                            // Configurar validación de números
                            setupNumberValidation();

                            // Configurar carga dinámica de municipios
                            setupMunicipioLoading();

                            // Configurar funcionalidad de dirección estructurada
                            setupAddressForm();

                            // Configurar validación de edad mínima
                            setupEdadMinimaValidation();
                        });

                        function setupUppercaseConversion() {
                            const camposTexto = [
                                'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido'
                            ];

                            camposTexto.forEach(campoId => {
                                const campo = document.getElementById(campoId);
                                if (campo) {
                                    campo.addEventListener('input', function() {
                                        // Convertir a mayúsculas y remover números
                                        this.value = this.value.toUpperCase().replace(/[0-9]/g, '');
                                    });

                                    // Validación en tiempo real - prevenir números durante escritura
                                    campo.addEventListener('keypress', function(e) {
                                        const char = String.fromCharCode(e.which);
                                        // Permitir letras, espacios, guiones, tildes y teclas de control
                                        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]$/.test(char) &&
                                            !e.ctrlKey && !e.altKey && !e.metaKey) {
                                            e.preventDefault();
                                            return false;
                                        }
                                        return true;
                                    });
                                }
                            });
                        }

                        function setupNumberValidation() {
                            const camposNumericos = ['numero_documento', 'telefono', 'celular'];

                            camposNumericos.forEach(campoId => {
                                const campo = document.getElementById(campoId);
                                if (campo) {
                                    campo.addEventListener('keypress', soloNumeros);
                                }
                            });
                        }

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

                        function setupMunicipioLoading() {
                            const departamentoSelect = document.getElementById('departamento_id');
                            if (departamentoSelect) {
                                departamentoSelect.addEventListener('change', function() {
                                    loadMunicipiosForDepartamento(this.value);
                                });
                                // Cargar municipios en carga inicial si ya hay un departamento seleccionado (por old())
                                const oldMunicipioId = "{{ old('municipio_id') }}";
                                if (departamentoSelect.value) {
                                    loadMunicipiosForDepartamento(departamentoSelect.value, oldMunicipioId || null);
                                }
                            }
                        }

                        function loadMunicipiosForDepartamento(departamentoId, selectedMunicipioId = null) {
                            const municipioSelect = document.getElementById('municipio_id');
                            if (!municipioSelect) return;

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
                                        // Preseleccionar municipio si viene de old() tras validación
                                        if (selectedMunicipioId) {
                                            municipioSelect.value = selectedMunicipioId;
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error cargando municipios:', error);
                                        municipioSelect.innerHTML = '<option value="">' +
                                            'Error cargando municipios</option>';
                                    });
                            } else {
                                municipioSelect.innerHTML = '<option value="">Seleccione...' +
                                    '</option>';
                            }
                        }

                        function setupAddressForm() {
                            const toggleButton = document.getElementById('toggleAddressForm');
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

                            const saveButton = document.getElementById('saveAddress');
                            if (saveButton) {
                                saveButton.addEventListener('click', function() {
                                    const tipoVia = document.getElementById('tipo_via') ?
                                        document.getElementById('tipo_via').value.trim() : '';
                                    const numeroVia = document.getElementById('numero_via') ?
                                        document.getElementById('numero_via').value.trim() : '';
                                    const letraVia = document.getElementById('letra_via') ?
                                        document.getElementById('letra_via').value.trim() : '';
                                    const viaSecundaria = document.getElementById('via_secundaria') ?
                                        document.getElementById('via_secundaria').value.trim() : '';
                                    const numeroCasa = document.getElementById('numero_casa') ?
                                        document.getElementById('numero_casa').value.trim() : '';
                                    const complementos = document.getElementById('complementos') ?
                                        document.getElementById('complementos').value.trim() : '';
                                    const barrio = document.getElementById('barrio') ?
                                        document.getElementById('barrio').value.trim() : '';

                                    // Validar campos obligatorios
                                    if (!tipoVia || !numeroVia || !numeroCasa) {
                                        alert('Por favor complete todos los campos obligatorios: ' +
                                            'Tipo de vía, Número de vía y Número de casa.');
                                        return;
                                    }

                                    // Construir la dirección
                                    let direccion = `${tipoVia} ${numeroVia}`;
                                    if (letraVia) {
                                        direccion += letraVia;
                                    }
                                    direccion += ` #${numeroCasa}`;
                                    if (viaSecundaria) {
                                        direccion += ` ${viaSecundaria}`;
                                    }
                                    if (complementos) {
                                        direccion += ` ${complementos}`;
                                    }
                                    if (barrio) {
                                        direccion += `, ${barrio}`;
                                    }

                                    // Asignar al campo principal
                                    document.getElementById('direccion').value = direccion;

                                    // Ocultar el formulario
                                    $('#addressForm').collapse('hide');

                                    // Limpiar campos
                                    document.querySelectorAll('.address-field').forEach(field => {
                                        if (field.type === 'select-one') {
                                            field.selectedIndex = 0;
                                        } else {
                                            field.value = '';
                                        }
                                    });
                                });
                            }

                            const cancelButton = document.getElementById('cancelAddress');
                            if (cancelButton) {
                                cancelButton.addEventListener('click', function() {
                                    // Ocultar el formulario
                                    $('#addressForm').collapse('hide');

                                    // Limpiar campos
                                    document.querySelectorAll('.address-field').forEach(field => field.value = '');
                                });
                            }

                            // Validar solo números en campos de dirección
                            const addressNumericFields = ['numero_via', 'numero_casa'];
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
                        }

                        function setupEdadMinimaValidation() {
                            const fechaNacimientoInput = document.getElementById('fecha_nacimiento');
                            if (!fechaNacimientoInput) return;

                            // Calcular la fecha máxima permitida (hace 14 años)
                            const hoy = new Date();
                            const fechaMaxima = new Date();
                            fechaMaxima.setFullYear(hoy.getFullYear() - 14);
                            
                            // Establecer el atributo max si no está ya establecido
                            if (!fechaNacimientoInput.getAttribute('max')) {
                                const fechaMaximaStr = fechaMaxima.toISOString().split('T')[0];
                                fechaNacimientoInput.setAttribute('max', fechaMaximaStr);
                            }

                            // Validar cuando cambia la fecha
                            fechaNacimientoInput.addEventListener('change', function() {
                                const fechaSeleccionada = new Date(this.value);
                                const edadMinima = new Date();
                                edadMinima.setFullYear(edadMinima.getFullYear() - 14);

                                if (fechaSeleccionada > edadMinima) {
                                    this.setCustomValidity('Debe tener al menos 14 años para registrarse.');
                                    this.classList.add('is-invalid');
                                    
                                    // Mostrar mensaje de error
                                    let errorMessage = this.parentElement.querySelector('.invalid-feedback');
                                    if (!errorMessage) {
                                        errorMessage = document.createElement('div');
                                        errorMessage.className = 'invalid-feedback';
                                        this.parentElement.appendChild(errorMessage);
                                    }
                                    errorMessage.textContent = 'Debe tener al menos 14 años para registrarse.';
                                } else {
                                    this.setCustomValidity('');
                                    this.classList.remove('is-invalid');
                                    
                                    // Remover mensaje de error
                                    const errorMessage = this.parentElement.querySelector('.invalid-feedback');
                                    if (errorMessage) {
                                        errorMessage.remove();
                                    }
                                }
                            });

                            // Validar al enviar el formulario
                            const form = document.getElementById('registroForm');
                            if (form) {
                                form.addEventListener('submit', function(e) {
                                    const fechaSeleccionada = new Date(fechaNacimientoInput.value);
                                    const edadMinima = new Date();
                                    edadMinima.setFullYear(edadMinima.getFullYear() - 14);

                                    if (fechaSeleccionada > edadMinima) {
                                        e.preventDefault();
                                        fechaNacimientoInput.focus();
                                        fechaNacimientoInput.setCustomValidity(
                                            'Debe tener al menos 14 años para registrarse.');
                                        fechaNacimientoInput.classList.add('is-invalid');
                                        
                                        // Mostrar mensaje de error
                                        let errorMessage =
                                            fechaNacimientoInput.parentElement.querySelector('.invalid-feedback');
                                        if (!errorMessage) {
                                            errorMessage = document.createElement('div');
                                            errorMessage.className = 'invalid-feedback';
                                            fechaNacimientoInput.parentElement.appendChild(errorMessage);
                                        }
                                        errorMessage.textContent = 'Debe tener al menos 14 años para registrarse.';
                                        
                                        // Mostrar alerta
                                        alert('Debe tener al menos 14 años para registrarse.');
                                        return false;
                                    }
                                });
                            }
                        }
                    </script>

                    
                </form>
                <hr>
                {{-- <a href="{{ route('login') }}" class="text-center">Ya tengo una cuenta</a> --}}
            </div>

        </div>
    </div>


@section('scripts')
    @vite(['resources/js/complementarios/formulario-inscripcion.js'])
@endsection
@endsection
