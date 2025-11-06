// Funcionalidad para formularios de inscripción y registro

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar municipios si hay datos guardados
    initializeMunicipios();

    // Configurar validaciones de formularios
    setupFormValidations();

    // Configurar conversión a mayúsculas
    setupUppercaseConversion();

    // Configurar validación de números
    setupNumberValidation();

    // Configurar carga dinámica de municipios
    setupMunicipioLoading();

    // Inicializar municipios dinámicos para formularios que lo necesiten
    if (typeof initializeMunicipiosDynamic === 'function') {
        initializeMunicipiosDynamic();
    }
});

// Inicializar selects con datos guardados
function initializeMunicipios() {
    const userData = getUserData();
    if (userData) {
        // Si hay país guardado, cargar departamentos
        if (userData.pais_id) {
            loadDepartamentosForPais(userData.pais_id, userData.departamento_id);
        }

        // Si hay departamento guardado, cargar municipios
        if (userData.departamento_id) {
            loadMunicipiosForDepartamento(userData.departamento_id, userData.municipio_id);
        }
    }
}

// Obtener datos de usuario de la sesión (si existen)
function getUserData() {
    // Intentar obtener datos de elementos ocultos o variables globales
    const userDataElement = document.getElementById('user-data');
    if (userDataElement) {
        try {
            return JSON.parse(userDataElement.textContent || userDataElement.innerText);
        } catch (e) {
            console.warn('Error parsing user data:', e);
        }
    }

    // Para registro, obtener datos de sesión
    const sessionDataElement = document.getElementById('session-data');
    if (sessionDataElement) {
        try {
            const sessionData = JSON.parse(sessionDataElement.textContent || sessionDataElement.innerText);
            return sessionData.registro_data || null;
        } catch (e) {
            console.warn('Error parsing session data:', e);
        }
    }

    return null;
}

// Configurar validaciones de formularios
function setupFormValidations() {
    const forms = document.querySelectorAll('#formInscripcion, #registroForm');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
}

// Validar formulario completo
function validateForm(form) {
    let isValid = true;

    // Validar nombres y apellidos (solo letras)
    const camposTexto = ['primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido'];
    camposTexto.forEach(campoId => {
        const campo = form.querySelector(`#${campoId}`);
        if (campo && campo.value && !/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/.test(campo.value)) {
            alert(`El campo ${campoId.replace('_', ' ')} solo puede contener letras, espacios y guiones.`);
            campo.focus();
            isValid = false;
        }
    });

    // Validar términos y condiciones si existe
    const terminosCheckbox = form.querySelector('#acepto_terminos');
    if (terminosCheckbox && !terminosCheckbox.checked) {
        alert('Debe aceptar los términos y condiciones para continuar.');
        isValid = false;
    }

    // Validar número de documento
    const numeroDocumento = form.querySelector('#numero_documento');
    if (numeroDocumento && numeroDocumento.value && !/^\d+$/.test(numeroDocumento.value)) {
        alert('El número de documento solo puede contener números.');
        numeroDocumento.focus();
        isValid = false;
    }

    // Validar teléfono fijo si tiene valor
    const telefono = form.querySelector('#telefono');
    if (telefono && telefono.value && !/^\d+$/.test(telefono.value)) {
        alert('El teléfono fijo solo puede contener números.');
        telefono.focus();
        isValid = false;
    }

    // Validar celular
    const celular = form.querySelector('#celular');
    if (celular && celular.value && !/^\d+$/.test(celular.value)) {
        alert('El celular solo puede contener números.');
        celular.focus();
        isValid = false;
    }

    return isValid;
}

// Configurar conversión a mayúsculas y validación de texto
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

// Configurar validación de solo números
function setupNumberValidation() {
    const camposNumericos = ['numero_documento', 'telefono', 'celular'];

    camposNumericos.forEach(campoId => {
        const campo = document.getElementById(campoId);
        if (campo) {
            campo.addEventListener('keypress', soloNumeros);
        }
    });
}

// Función para permitir solo números
function soloNumeros(event) {
    const key = event.key;
    // Permitir teclas de control
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

// Configurar carga dinámica de municipios y departamentos
function setupMunicipioLoading() {
    const paisSelect = document.getElementById('pais_id');
    const departamentoSelect = document.getElementById('departamento_id');

    if (paisSelect) {
        paisSelect.addEventListener('change', function() {
            loadDepartamentosForPais(this.value);
            // Limpiar municipios cuando cambia el país
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

// Función para inicializar municipios dinámicos (llamada desde formularios)
function initializeMunicipiosDynamic() {
    // Configurar carga dinámica de municipios
    const departamentoSelect = document.getElementById('departamento_id');
    if (departamentoSelect) {
        departamentoSelect.addEventListener('change', function() {
            loadMunicipiosForDepartamento(this.value);
        });
    }

    // Configurar carga de departamentos si hay país seleccionado
    const paisSelect = document.getElementById('pais_id');
    if (paisSelect) {
        paisSelect.addEventListener('change', function() {
            loadDepartamentosForPais(this.value);
            // Limpiar municipios cuando cambia el país
            const municipioSelect = document.getElementById('municipio_id');
            if (municipioSelect) {
                municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
            }
        });
    }
}

// Cargar departamentos para un país
function loadDepartamentosForPais(paisId, selectedDepartamentoId = null) {
    const departamentoSelect = document.getElementById('departamento_id');
    if (!departamentoSelect) return;

    if (paisId) {
        fetch(`/departamentos/${paisId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                departamentoSelect.innerHTML = '<option value="">Seleccione...</option>';
                if (Array.isArray(data)) {
                    data.forEach(departamento => {
                        const option = document.createElement('option');
                        option.value = departamento.id;
                        option.textContent = departamento.departamento;
                        if (selectedDepartamentoId && departamento.id == selectedDepartamentoId) {
                            option.selected = true;
                        }
                        departamentoSelect.appendChild(option);
                    });
                } else {
                    console.error('Los datos de departamentos no son un array:', data);
                    departamentoSelect.innerHTML = '<option value="">Error en formato de datos</option>';
                }
            })
            .catch(error => {
                console.error('Error cargando departamentos:', error);
                departamentoSelect.innerHTML = '<option value="">Error cargando departamentos</option>';
            });
    } else {
        departamentoSelect.innerHTML = '<option value="">Seleccione...</option>';
    }
}

// Cargar municipios para un departamento
function loadMunicipiosForDepartamento(departamentoId, selectedMunicipioId = null) {
    const municipioSelect = document.getElementById('municipio_id');
    if (!municipioSelect) return;

    if (departamentoId) {
        fetch(`/municipios/${departamentoId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
                if (Array.isArray(data)) {
                    data.forEach(municipio => {
                        const option = document.createElement('option');
                        option.value = municipio.id;
                        option.textContent = municipio.municipio;
                        if (selectedMunicipioId && municipio.id == selectedMunicipioId) {
                            option.selected = true;
                        }
                        municipioSelect.appendChild(option);
                    });
                } else {
                    console.error('Los datos de municipios no son un array:', data);
                    municipioSelect.innerHTML = '<option value="">Error en formato de datos</option>';
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

// Funciones para el formulario de dirección estructurada (si existe)
function setupAddressForm() {
    const toggleButton = document.getElementById('toggleAddressForm');
    const saveButton = document.getElementById('saveAddress');
    const cancelButton = document.getElementById('cancelAddress');

    if (toggleButton) {
        toggleButton.addEventListener('click', function() {
            const addressForm = document.getElementById('addressForm');
            const isVisible = addressForm.classList.contains('show');
            if (isVisible) {
                $('#addressForm').collapse('hide');
            } else {
                $('#addressForm').collapse('show');
            }
        });
    }

    if (saveButton) {
        saveButton.addEventListener('click', saveAddress);
    }

    if (cancelButton) {
        cancelButton.addEventListener('click', cancelAddress);
    }

    // Validación de números en campos de dirección
    const addressNumericFields = ['numero_via', 'numero_casa'];
    addressNumericFields.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element) {
            element.addEventListener('keypress', soloNumeros);
        }
    });
}

// Guardar dirección estructurada
function saveAddress() {
    const tipoVia = document.getElementById('tipo_via') ? document.getElementById('tipo_via').value.trim() : '';
    const numeroVia = document.getElementById('numero_via') ? document.getElementById('numero_via').value.trim() : '';
    const letraVia = document.getElementById('letra_via') ? document.getElementById('letra_via').value.trim() : '';
    const viaSecundaria = document.getElementById('via_secundaria') ? document.getElementById('via_secundaria').value.trim() : '';
    const numeroCasa = document.getElementById('numero_casa') ? document.getElementById('numero_casa').value.trim() : '';
    const complementos = document.getElementById('complementos') ? document.getElementById('complementos').value.trim() : '';
    const barrio = document.getElementById('barrio') ? document.getElementById('barrio').value.trim() : '';

    // Verificar si estamos en un formulario con campos antiguos (carrera, calle)
    const carrera = document.getElementById('carrera') ? document.getElementById('carrera').value.trim() : '';
    const calle = document.getElementById('calle') ? document.getElementById('calle').value.trim() : '';
    const numeroApartamento = document.getElementById('numero_apartamento') ? document.getElementById('numero_apartamento').value.trim() : '';

    // Validar campos obligatorios - verificar si usar campos nuevos o antiguos
    let isValid = false;
    let direccion = '';

    if (tipoVia && numeroVia && numeroCasa) {
        // Usar campos nuevos (tipo_via, numero_via, etc.)
        isValid = true;

        // Construir la dirección con campos nuevos
        direccion = `${tipoVia} ${numeroVia}`;

        // Agregar letra de vía si existe
        if (letraVia) {
            direccion += letraVia;
        }

        // Agregar número de casa
        direccion += ` #${numeroCasa}`;

        // Agregar vía secundaria si existe
        if (viaSecundaria) {
            direccion += ` ${viaSecundaria}`;
        }

        // Agregar complementos si existen
        if (complementos) {
            direccion += ` ${complementos}`;
        }

        // Agregar barrio si existe
        if (barrio) {
            direccion += `, ${barrio}`;
        }
    } else if (carrera && calle && numeroCasa) {
        // Usar campos antiguos (carrera, calle, etc.)
        isValid = true;

        // Construir la dirección con campos antiguos
        direccion = `Carrera ${carrera} Calle ${calle} #${numeroCasa}`;
        if (numeroApartamento) {
            direccion += ` Apt ${numeroApartamento}`;
        }
    }

    if (!isValid) {
        alert('Por favor complete todos los campos obligatorios: Tipo de vía, Número de vía y Número de casa.');
        return;
    }

    // Asignar al campo principal
    document.getElementById('direccion').value = direccion;

    // Ocultar el formulario
    $('#addressForm').collapse('hide');

    // Limpiar campos - limpiar tanto campos nuevos como antiguos
    document.querySelectorAll('.address-field').forEach(field => {
        if (field.type === 'select-one') {
            field.selectedIndex = 0;
        } else {
            field.value = '';
        }
    });

    // También limpiar campos antiguos si existen
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

// Cancelar edición de dirección
function cancelAddress() {
    // Ocultar el formulario
    $('#addressForm').collapse('hide');

    // Limpiar campos
    document.querySelectorAll('.address-field').forEach(field => field.value = '');
}

// Exportar funciones globales para uso en HTML
window.setupAddressForm = setupAddressForm;
window.initializeMunicipiosDynamic = initializeMunicipiosDynamic;
