@extends('complementarios.layout.master')
@section('title', 'Formulario de Inscripción | SENA')
@section('css')
    @vite(['resources/css/formulario_inscripcion.css'])
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar conversión a mayúsculas
    setupUppercaseConversion();

    // Configurar validación de números
    setupNumberValidation();

    // Configurar carga dinámica de municipios
    setupMunicipioLoading();

    // Configurar manejo de caracterización
    setupCaracterizacionHandling();

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
                if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]$/.test(char) && !e.ctrlKey && !e.altKey && !e.metaKey) {
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
        // Cargar municipios cuando cambia el departamento
        departamentoSelect.addEventListener('change', function() {
            loadMunicipiosForDepartamento(this.value);
        });
        
        // Cargar municipios automáticamente si hay un departamento seleccionado al cargar la página
        const departamentoId = departamentoSelect.value;
        if (departamentoId) {
            // Obtener el municipio_id del usuario si existe
            const municipioId = @json($userData['municipio_id'] ?? null);
            loadMunicipiosForDepartamento(departamentoId, municipioId);
        }
    }
}

function loadMunicipiosForDepartamento(departamentoId, municipioIdToSelect = null) {
    const municipioSelect = document.getElementById('municipio_id');
    if (!municipioSelect) return;

    if (departamentoId) {
        fetch(`/municipios/${departamentoId}`)
            .then(response => response.json())
            .then(data => {
                municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
                
                // Manejar diferentes estructuras de respuesta
                let municipios = [];
                
                if (data.success && data.data) {
                    // Estructura: {success: true, data: [...]}
                    municipios = data.data;
                } else if (Array.isArray(data)) {
                    // Estructura: [...]
                    municipios = data;
                } else if (data && typeof data === 'object') {
                    // Estructura: {municipios: [...]} u otra variante
                    municipios = data.municipios || data.data || [];
                }
                
                if (Array.isArray(municipios) && municipios.length > 0) {
                    municipios.forEach(municipio => {
                        const option = document.createElement('option');
                        option.value = municipio.id;
                        // Manejar diferentes nombres de campo para el municipio
                        const label = municipio.nombre ?? municipio.municipio ?? municipio.name ?? municipio.label ?? '';
                        option.textContent = label || `ID ${municipio.id}`;
                        // Seleccionar el municipio si coincide con el municipio_id del usuario
                        if (municipioIdToSelect && municipio.id == municipioIdToSelect) {
                            option.selected = true;
                        }
                        municipioSelect.appendChild(option);
                    });
                } else {
                    console.error('No se encontraron municipios o estructura inválida:', data);
                    municipioSelect.innerHTML = '<option value="">No hay municipios disponibles</option>';
                }
            })
            .catch(error => {
                console.error('Error cargando municipios:', error);
                municipioSelect.innerHTML = '<option value="">Error cargando municipios</option>';
            });
    } else {
        municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
    }
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
    const form = document.getElementById('formInscripcion');
    if (form) {
        form.addEventListener('submit', function(e) {
            const fechaSeleccionada = new Date(fechaNacimientoInput.value);
            const edadMinima = new Date();
            edadMinima.setFullYear(edadMinima.getFullYear() - 14);

            if (fechaSeleccionada > edadMinima) {
                e.preventDefault();
                fechaNacimientoInput.focus();
                fechaNacimientoInput.setCustomValidity('Debe tener al menos 14 años para registrarse.');
                fechaNacimientoInput.classList.add('is-invalid');
                
                // Mostrar mensaje de error
                let errorMessage = fechaNacimientoInput.parentElement.querySelector('.invalid-feedback');
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

function setupCaracterizacionHandling() {
    const caracterizacionRadios = document.querySelectorAll('input[name="caracterizacion_id"]');
    let ningunaRadio = null;
    
    // Buscar el radio button de "NINGUNA"
    caracterizacionRadios.forEach(radio => {
        const label = document.querySelector(`label[for="${radio.id}"]`);
        if (label && label.textContent.trim().toUpperCase() === 'NINGUNA') {
            ningunaRadio = radio;
        }
    });

    // Si no se encuentra "NINGUNA", salir
    if (!ningunaRadio) return;

    // Establecer "NINGUNA" como seleccionada por defecto
    ningunaRadio.checked = true;

    // Agregar event listeners a todos los radio buttons
    caracterizacionRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this !== ningunaRadio && this.checked) {
                // Si se selecciona cualquier opción que no sea "NINGUNA", deseleccionar "NINGUNA"
                ningunaRadio.checked = false;
            } else if (this === ningunaRadio && this.checked) {
                // Si se selecciona "NINGUNA", deseleccionar todas las demás opciones
                caracterizacionRadios.forEach(otherRadio => {
                    if (otherRadio !== ningunaRadio) {
                        otherRadio.checked = false;
                    }
                });
            }
        });
    });

    // Manejar el evento de reset del formulario
    const form = document.getElementById('formInscripcion');
    if (form) {
        form.addEventListener('reset', function() {
            // Después del reset, volver a seleccionar "NINGUNA"
            setTimeout(() => {
                ningunaRadio.checked = true;
                caracterizacionRadios.forEach(radio => {
                    if (radio !== ningunaRadio) {
                        radio.checked = false;
                    }
                });
            }, 0);
        });
    }
}

// Funcionalidad del formulario de dirección estructurada
document.addEventListener('DOMContentLoaded', function() {
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
            const tipoVia = document.getElementById('tipo_via') ? document.getElementById('tipo_via').value.trim() : '';
            const numeroVia = document.getElementById('numero_via') ? document.getElementById('numero_via').value.trim() : '';
            const letraVia = document.getElementById('letra_via') ? document.getElementById('letra_via').value.trim() : '';
            const viaSecundaria = document.getElementById('via_secundaria') ? document.getElementById('via_secundaria').value.trim() : '';
            const numeroCasa = document.getElementById('numero_casa') ? document.getElementById('numero_casa').value.trim() : '';
            const complementos = document.getElementById('complementos') ? document.getElementById('complementos').value.trim() : '';
            const barrio = document.getElementById('barrio') ? document.getElementById('barrio').value.trim() : '';

            // Validar campos obligatorios
            if (!tipoVia || !numeroVia || !numeroCasa) {
                alert('Por favor complete todos los campos obligatorios: Tipo de vía, Número de vía y Número de casa.');
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
});
</script>
@endsection
@section('content')

     <div class="container-fluid mt-4" style="background-color: #ebf1f4; min-height: 100vh;">
         @if(session('user_data'))
             <div class="alert alert-info alert-dismissible">
                 <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                 <h5><i class="icon fas fa-info"></i> Información Pre-llenada</h5>
                 Hemos completado algunos campos con la información de su cuenta. Por favor, complete los campos faltantes y revise que toda la información sea correcta.
             </div>
         @endif
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="text-center mb-4">
                    <h2 class="text-dark">Formulario de Inscripción</h2>
                    <p class="text-muted">Complete sus datos para inscribirse</p>
                </div>
                <div class="card" style="background-color: #ffffff; border-color: #dee2e6;">
                    <div class="card-header" style="background-color: #ffffff; color: #343a40; border-left: 4px solid #007bff;">
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
                <div class="col-md-8">
                    <div class="card" style="background-color: #ffffff; border-color: #dee2e6;">
                        <div class="card-header" style="background-color: #ffffff; color: #343a40; border-left: 4px solid #007bff;">
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
                                    <div class="card-header" style="background-color: #ffffff; color: #343a40; border-left: 4px solid #007bff;">
                                        <h5 class="mb-0"><i class="fas fa-tags mr-2"></i>Caracterización</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3">Seleccione una categoría que corresponda a su situación:</p>

                                        @foreach ($categoriasConHijos as $categoria)
                                            <div class="card card-outline mb-3" style="border-color: #dee2e6;">
                                                <div class="card-header" style="background-color: #f8f9fa; color: #343a40;">
                                                    <h6 class="mb-0">{{ $categoria['nombre'] }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        @foreach ($categoria['hijos'] as $hijo)
                                                            <div class="col-12 mb-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="caracterizacion_id" value="{{ $hijo->id }}"
                                                                        id="categoria_{{ $hijo->id }}">
                                                                    <label class="form-check-label"
                                                                        for="categoria_{{ $hijo->id }}">
                                                                        {{ $hijo->nombre }}
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

                                <div class="card mb-4" style="background-color: #ffffff; border-color: #dee2e6;">
                                    <div class="card-header" style="background-color: #ffffff; color: #343a40; border-left: 4px solid #007bff;">
                                        <h5 class="mb-0"><i class="fas fa-sticky-note mr-2"></i>Observaciones y Términos</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="observaciones" class="form-label" style="color: #343a40;">Observaciones</label>
                                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"
                                                placeholder="Información adicional que considere relevante...">{{ old('observaciones') }}</textarea>
                                        </div>

                                        <div class="form-check mb-4">
                                            <input class="form-check-input" type="checkbox" id="acepto_terminos" name="acepto_terminos" required>
                                            <label class="form-check-label" for="acepto_terminos" style="color: #343a40;">
                                                Acepto los <a href="#" data-toggle="modal" data-target="#modalTerminos" style="color: #007bff;">términos y condiciones</a> del proceso de inscripción *
                                            </label>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="reset" class="btn btn-outline-secondary me-md-2 mr-3">Limpiar</button>
                                            <button type="submit" class="btn btn-primary">Enviar Inscripción</button>
                                        </div>

                                        <script>
                                            // Mostrar preloader al enviar el formulario
                                            document.getElementById('formInscripcion').addEventListener('submit', function() {
                                                $('body').addClass('preloader-active');
                                            });
                                        </script>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-widget widget-user">
                        <div class="widget-user-header" style="background-color: #ffffff; color: #343a40; border-left: 4px solid #007bff;">
                            <h3 class="widget-user-username">Información del Programa</h3>
                            <h5 class="widget-user-desc">{{ $programa->nombre }}</h5>
                        </div>
                        <div class="card-footer" style="background-color: #ffffff;">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="description-block">
                                        <span class="description-text" style="color: #343a40;">DESCRIPCIÓN</span>
                                        <p class="text-muted mb-3">{{ $programa->descripcion }}</p>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text" style="color: #343a40;">DURACIÓN</span>
                                                    <h5 class="description-header" style="color: #007bff;">{{ formatear_horas($programa->duracion) }} horas</h5>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text" style="color: #343a40;">MODALIDAD</span>
                                                    <h5 class="description-header" style="color: #007bff;">
                                                        {{ $programa->modalidad->parametro->name ?? 'N/A' }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text" style="color: #343a40;">JORNADA</span>
                                                    <h5 class="description-header" style="color: #007bff;">
                                                        {{ $programa->jornada->jornada ?? 'N/A' }}</h5>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text" style="color: #343a40;">CUPO</span>
                                                    <h5 class="description-header" style="color: #007bff;">{{ $programa->cupos }}</h5>
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
@include('components.modal-terminos')
@endsection
