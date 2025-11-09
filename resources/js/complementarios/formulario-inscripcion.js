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

    // Configurar manejo de caracterización
    setupCaracterizacionHandling();

    // Configurar validación de edad mínima
    setupEdadMinimaValidation();

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
                this.value = this.value.toUpperCase().replace(/\d/g, '');
            });

            // Validación en tiempo real - prevenir números durante escritura
            campo.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.keyCode || e.which);
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

    // Limpiar select y mostrar opción por defecto
    departamentoSelect.innerHTML = '<option value="">Seleccione...</option>';

    if (!paisId) return;

    // Función auxiliar para manejar errores
    const handleError = (error) => {
        console.error('Error cargando departamentos:', error);
        departamentoSelect.innerHTML = '<option value="">Error cargando departamentos</option>';
    };

    // Función auxiliar para procesar datos
    const processData = (data) => {
        if (!Array.isArray(data)) {
            console.error('Los datos de departamentos no son un array:', data);
            departamentoSelect.innerHTML = '<option value="">Error en formato de datos</option>';
            return;
        }

        data.forEach(departamento => {
            const option = document.createElement('option');
            option.value = departamento.id;
            option.textContent = departamento.departamento;
            if (selectedDepartamentoId && departamento.id == selectedDepartamentoId) {
                option.selected = true;
            }
            departamentoSelect.appendChild(option);
        });
    };

    fetch(`/departamentos/${paisId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(processData)
        .catch(handleError);
}

// Cargar municipios para un departamento
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

// Funciones para el formulario de dirección estructurada (si existe)
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

    // Validación de números en campos de dirección
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




// Cancelar edición de dirección
function cancelAddress() {
    // Ocultar el formulario
    $('#addressForm').collapse('hide');

    // Limpiar campos
    document.querySelectorAll('.address-field').forEach(field => field.value = '');
}

// Función auxiliar para obtener valor de campo
function getFieldValue(fieldId) {
    const field = document.getElementById(fieldId);
    return field ? field.value.trim() : '';
}

// Función auxiliar para construir dirección con campos nuevos
function buildNewAddressFormat(tipoVia, numeroVia, letraVia, numeroCasa, viaSecundaria, complementos, barrio) {
    let direccion = `${tipoVia} ${numeroVia}`;
    if (letraVia) direccion += letraVia;
    direccion += ` #${numeroCasa}`;
    if (viaSecundaria) direccion += ` ${viaSecundaria}`;
    if (complementos) direccion += ` ${complementos}`;
    if (barrio) direccion += `, ${barrio}`;
    return direccion;
}

// Función auxiliar para construir dirección con campos antiguos
function buildOldAddressFormat(carrera, calle, numeroCasa, numeroApartamento) {
    let direccion = `Carrera ${carrera} Calle ${calle} #${numeroCasa}`;
    if (numeroApartamento) direccion += ` Apt ${numeroApartamento}`;
    return direccion;
}

// Función auxiliar para validar campos obligatorios
function validateAddressFields(tipoVia, numeroVia, numeroCasa, carrera, calle) {
    return (tipoVia && numeroVia && numeroCasa) || (carrera && calle && numeroCasa);
}

// Función auxiliar para limpiar campos
function clearAddressFields() {
    // Limpiar campos nuevos
    document.querySelectorAll('.address-field').forEach(field => {
        if (field.type === 'select-one') {
            field.selectedIndex = 0;
        } else {
            field.value = '';
        }
    });

    // Limpiar campos antiguos
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
    // Obtener valores de campos nuevos
    const tipoVia = getFieldValue('tipo_via');
    const numeroVia = getFieldValue('numero_via');
    const letraVia = getFieldValue('letra_via');
    const viaSecundaria = getFieldValue('via_secundaria');
    const numeroCasa = getFieldValue('numero_casa');
    const complementos = getFieldValue('complementos');
    const barrio = getFieldValue('barrio');

    // Obtener valores de campos antiguos
    const carrera = getFieldValue('carrera');
    const calle = getFieldValue('calle');
    const numeroApartamento = getFieldValue('numero_apartamento');

    // Validar campos obligatorios
    if (!validateAddressFields(tipoVia, numeroVia, numeroCasa, carrera, calle)) {
        alert('Por favor complete todos los campos obligatorios: Tipo de vía, Número de vía y Número de casa.');
        return;
    }

    // Construir dirección según el formato disponible
    const direccion = (tipoVia && numeroVia && numeroCasa)
        ? buildNewAddressFormat(tipoVia, numeroVia, letraVia, numeroCasa, viaSecundaria, complementos, barrio)
        : buildOldAddressFormat(carrera, calle, numeroCasa, numeroApartamento);

    // Asignar al campo principal y ocultar formulario
    document.getElementById('direccion').value = direccion;
    $('#addressForm').collapse('hide');

    // Limpiar campos
    clearAddressFields();
}


// Exportar funciones globales para uso en HTML
window.setupAddressForm = setupAddressForm;
window.initializeMunicipiosDynamic = initializeMunicipiosDynamic;
