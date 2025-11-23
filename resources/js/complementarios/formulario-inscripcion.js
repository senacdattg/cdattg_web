// Funcionalidad para formularios de inscripción y registro

// Constantes de configuración
const CONFIG = {
    EDAD_MINIMA: 14,
    MENSAJE_EDAD_INVALIDA: 'Debe tener al menos 14 años para registrarse.',
    NINGUNA_LABEL: 'NINGUNA',
    CAMPOS_TEXTOS: ['primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido'],
    CAMPOS_NUMERICOS: ['numero_documento', 'telefono', 'celular'],
    CAMPOS_DIRECCION_NUMERICOS: ['numero_via', 'numero_casa']
};

// Función para detectar el contexto del formulario
function getFormContext() {
    const registroForm = document.getElementById('registroForm');
    const formInscripcion = document.getElementById('formInscripcion');

    if (registroForm) return 'registro';
    if (formInscripcion) return 'inscripcion';
    return 'unknown';
}

// Constantes de configuración para centralizar reglas de negocio
const CONFIG = {
    EDAD_MINIMA: 14,
    MENSAJE_EDAD_INVALIDA: 'Debe tener al menos 14 años para registrarse.',
    NINGUNA_LABEL: 'NINGUNA',
    CAMPOS_TEXTOS: ['primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido'],
    CAMPOS_NUMERICOS: ['numero_documento', 'telefono', 'celular'],
    CAMPOS_DIRECCION_NUMERICOS: ['numero_via', 'numero_casa']
};

// Detecta el contexto del formulario (inscripción vs registro) para activar lógica específica
function getFormContext() {
    const registroForm = document.getElementById('registroForm');
    const formInscripcion = document.getElementById('formInscripcion');

    if (registroForm) return 'registro';
    if (formInscripcion) return 'inscripcion';
    return 'unknown';
}

document.addEventListener('DOMContentLoaded', function () {
    const context = getFormContext();

    // NO inicializar municipios aquí - el script form.js del partial lo maneja
    // initializeMunicipios();

    // Configurar validaciones de formularios
    setupFormValidations();

    // Configurar conversión a mayúsculas
    setupUppercaseConversion();

    // Configurar validación de números
    setupNumberValidation();

    // NO configurar carga dinámica de municipios aquí - el script form.js del partial lo maneja
    // setupMunicipioLoading();

    // Configurar manejo de caracterización solo para inscripción
    if (context === 'inscripcion') {
        setupCaracterizacionHandling();
    }

    // Configurar validación de edad mínima (aplica a ambos contextos si existe el campo)
    setupEdadMinimaValidation();

    // NO inicializar municipios dinámicos aquí - el script form.js del partial lo maneja
    // if (typeof initializeMunicipiosDynamic === 'function') {
    //     initializeMunicipiosDynamic();
    // }

    // Para registro, inicializar carga dinámica de países (solo si aplica)
    if (context === 'registro') {
        initializePaisesForRegistro();
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
        form.addEventListener('submit', function (e) {
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
    CONFIG.CAMPOS_TEXTOS.forEach(campoId => {
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
    CONFIG.CAMPOS_TEXTOS.forEach(campoId => {
        const campo = document.getElementById(campoId);
        if (campo) {
            campo.addEventListener('input', function () {
                // Convertir a mayúsculas y remover números
                this.value = this.value.toUpperCase().replace(/\d/g, '');
            });

            // Validación en tiempo real - prevenir números durante escritura
            campo.addEventListener('keypress', function (e) {
                const char = e.key;
                if (!char || char.length !== 1) {
                    return true;
                }
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

// Configurar validación de solo números
function setupNumberValidation() {
    CONFIG.CAMPOS_NUMERICOS.forEach(campoId => {
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
        paisSelect.addEventListener('change', function () {
            loadDepartamentosForPais(this.value);
            // Limpiar municipios cuando cambia el país
            const municipioSelect = document.getElementById('municipio_id');
            if (municipioSelect) {
                municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
            }
        });
    }

    if (departamentoSelect) {
        departamentoSelect.addEventListener('change', function () {
            loadMunicipiosForDepartamento(this.value);
        });
    }
}

// Función para inicializar municipios dinámicos (llamada desde formularios)
function initializeMunicipiosDynamic() {
    // Configurar carga dinámica de municipios
    const departamentoSelect = document.getElementById('departamento_id');
    if (departamentoSelect) {
        departamentoSelect.addEventListener('change', function () {
            loadMunicipiosForDepartamento(this.value);
        });
    }

    // Configurar carga de departamentos si hay país seleccionado
    const paisSelect = document.getElementById('pais_id');
    if (paisSelect) {
        paisSelect.addEventListener('change', function () {
            loadDepartamentosForPais(this.value);
            // Limpiar municipios cuando cambia el país
            const municipioSelect = document.getElementById('municipio_id');
            if (municipioSelect) {
                municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
            }
        });
    }
}

// Función común para procesar respuestas de API de ubicaciones
function processLocationData(data, entityType) {
    return extractItemsFromResponse(data, entityType);
}

// Extraer items de diferentes formatos de respuesta
function extractItemsFromResponse(data, entityType) {
    if (Array.isArray(data)) {
        return data;
    }

    if (!data || typeof data !== 'object') {
        return [];
    }

    const rawData = extractRawData(data, entityType);
    return convertToArray(rawData, entityType);
}

// Extraer datos crudos de diferentes formatos de respuesta
function extractRawData(data, entityType) {
    if (data.success !== undefined && data.data !== undefined) {
        return data.data;
    }

    if (data.data !== undefined) {
        return data.data;
    }

    if (data[entityType] !== undefined) {
        return data[entityType];
    }

    return data;
}

// Convertir datos crudos a array
function convertToArray(rawData, entityType) {
    if (rawData === null || rawData === undefined) {
        return [];
    }

    if (Array.isArray(rawData)) {
        return rawData;
    }

    if (typeof rawData === 'object') {
        try {
            return Object.values(rawData);
        } catch (e) {
            console.error(`Error convirtiendo datos de ${entityType} a array:`, e);
            return [];
        }
    }

    console.error(`Los datos de ${entityType} no son un array ni un objeto:`, rawData);
    return [];
}

// Función común para poblar select con opciones de ubicación
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
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const municipios = processLocationData(data, 'municipios');
            populateLocationSelect(municipioSelect, municipios, municipioIdToSelect, 'municipio');
        })
        .catch(error => {
            console.error('Error cargando municipios:', error);
            municipioSelect.innerHTML = '<option value="">Error cargando municipios</option>';
        });
}

// Funciones para el formulario de dirección estructurada (si existe)
function setupAddressForm() {
    const toggleButton = document.getElementById('toggleAddressForm');
    const saveButton = document.getElementById('saveAddress');
    const cancelButton = document.getElementById('cancelAddress');

    if (toggleButton) {
        toggleButton.addEventListener('click', function () {
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

// Inicializar setupAddressForm cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function () {
    setupAddressForm();
});

// Cancelar edición de dirección
function cancelAddress() {
    $('#addressForm').collapse('hide');
    document.querySelectorAll('.address-field').forEach(field => field.value = '');
}

function getFieldValue(fieldId) {
    const field = document.getElementById(fieldId);
    return field ? field.value.trim() : '';
}

function buildNewAddressFormat(tipoVia, numeroVia, letraVia, numeroCasa, viaSecundaria, complementos, barrio) {
    let direccion = `${tipoVia} ${numeroVia}`;
    if (letraVia) direccion += letraVia;
    direccion += ` #${numeroCasa}`;
    if (viaSecundaria) direccion += ` ${viaSecundaria}`;
    if (complementos) direccion += ` ${complementos}`;
    if (barrio) direccion += `, ${barrio}`;
    return direccion;
}

function buildOldAddressFormat(carrera, calle, numeroCasa, numeroApartamento) {
    let direccion = `Carrera ${carrera} Calle ${calle} #${numeroCasa}`;
    if (numeroApartamento) direccion += ` Apt ${numeroApartamento}`;
    return direccion;
}

function validateAddressFields(tipoVia, numeroVia, numeroCasa, carrera, calle) {
    return (tipoVia && numeroVia && numeroCasa) || (carrera && calle && numeroCasa);
}

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

function collectNewAddressFields() {
    return {
        tipoVia: getFieldValue('tipo_via'),
        numeroVia: getFieldValue('numero_via'),
        letraVia: getFieldValue('letra_via'),
        viaSecundaria: getFieldValue('via_secundaria'),
        numeroCasa: getFieldValue('numero_casa'),
        complementos: getFieldValue('complementos'),
        barrio: getFieldValue('barrio')
    };
}

function collectOldAddressFields() {
    return {
        carrera: getFieldValue('carrera'),
        calle: getFieldValue('calle'),
        numeroApartamento: getFieldValue('numero_apartamento')
    };
}

function resolveDireccion(newFields, oldFields) {
    const { tipoVia, numeroVia, letraVia, numeroCasa, viaSecundaria, complementos, barrio } = newFields;
    const { carrera, calle, numeroApartamento } = oldFields;

    if (validateAddressFields(tipoVia, numeroVia, numeroCasa, carrera, calle)) {
        if (tipoVia && numeroVia && numeroCasa) {
            return buildNewAddressFormat(tipoVia, numeroVia, letraVia, numeroCasa, viaSecundaria, complementos, barrio);
        }

        return buildOldAddressFormat(carrera, calle, numeroCasa, numeroApartamento);
    }

    return null;
}

function applyDireccion(direccion) {
    document.getElementById('direccion').value = direccion;
    $('#addressForm').collapse('hide');
    clearAddressFields();
}

// Guardar dirección estructurada
function saveAddress() {
    const newFields = collectNewAddressFields();
    const oldFields = collectOldAddressFields();

    const direccion = resolveDireccion(newFields, oldFields);
    if (!direccion) {
        alert('Por favor complete todos los campos obligatorios: Tipo de vía, Número de vía y Número de casa.');
        return;
    }

    applyDireccion(direccion);
}

function findNingunaRadio(radios) {
    for (const radio of radios) {
        const label = document.querySelector(`label[for="${radio.id}"]`);
        if (label && label.textContent.trim().toUpperCase() === CONFIG.NINGUNA_LABEL) {
            return radio;
        }
    }
    return null;
}

function handleRadioChange(radio, ningunaRadio, allRadios) {
    if (radio !== ningunaRadio && radio.checked) {
        ningunaRadio.checked = false;
    } else if (radio === ningunaRadio && radio.checked) {
        allRadios.forEach(otherRadio => {
            if (otherRadio !== ningunaRadio) {
                otherRadio.checked = false;
            }
        });
    }
}

function resetToNinguna(ningunaRadio, allRadios) {
    ningunaRadio.checked = true;
    allRadios.forEach(radio => {
        if (radio !== ningunaRadio) {
            radio.checked = false;
        }
    });
}

function setupCaracterizacionHandling() {
    const caracterizacionRadios = document.querySelectorAll('input[name="parametro_id"]');
    const ningunaRadio = findNingunaRadio(caracterizacionRadios);
    if (!ningunaRadio) return;

    ningunaRadio.checked = true;
    caracterizacionRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            handleRadioChange(radio, ningunaRadio, caracterizacionRadios);
        });
    });

    const forms = document.querySelectorAll('#formInscripcion, #registroForm');
    forms.forEach(form => {
        form.addEventListener('reset', () => {
            setTimeout(() => resetToNinguna(ningunaRadio, caracterizacionRadios), 0);
        });
    });
}

function calcularFechaLimiteEdadMinima() {
    const fechaLimite = new Date();
    fechaLimite.setFullYear(fechaLimite.getFullYear() - CONFIG.EDAD_MINIMA);
    return fechaLimite;
}

function validarEdad(fechaNacimiento) {
    const fechaSeleccionada = new Date(fechaNacimiento);
    const fechaLimite = calcularFechaLimiteEdadMinima();
    return fechaSeleccionada <= fechaLimite;
}

function toggleMensajeErrorEdad(inputElement, mostrar) {
    if (mostrar) {
        inputElement.setCustomValidity(CONFIG.MENSAJE_EDAD_INVALIDA);
        inputElement.classList.add('is-invalid');

        let errorMessage = inputElement.parentElement.querySelector('.invalid-feedback');
        if (!errorMessage) {
            errorMessage = document.createElement('div');
            errorMessage.className = 'invalid-feedback';
            inputElement.parentElement.appendChild(errorMessage);
        }
        errorMessage.textContent = CONFIG.MENSAJE_EDAD_INVALIDA;
    } else {
        inputElement.setCustomValidity('');
        inputElement.classList.remove('is-invalid');

        const errorMessage = inputElement.parentElement.querySelector('.invalid-feedback');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
}

function setupEdadMinimaValidation() {
    const fechaNacimientoInput = document.getElementById('fecha_nacimiento');
    if (!fechaNacimientoInput) return;

    if (!fechaNacimientoInput.getAttribute('max')) {
        const fechaMaximaStr = calcularFechaLimiteEdadMinima().toISOString().split('T')[0];
        fechaNacimientoInput.setAttribute('max', fechaMaximaStr);
    }

    fechaNacimientoInput.addEventListener('change', function () {
        const esValida = validarEdad(this.value);
        toggleMensajeErrorEdad(this, !esValida);
    });

    const forms = document.querySelectorAll('#formInscripcion, #registroForm');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!validarEdad(fechaNacimientoInput.value)) {
                e.preventDefault();
                fechaNacimientoInput.focus();
                toggleMensajeErrorEdad(fechaNacimientoInput, true);
                alert(CONFIG.MENSAJE_EDAD_INVALIDA);
                return false;
            }
        });
    });
}

function initializePaisesForRegistro() {
    const paisSelect = document.getElementById('pais_id');
    if (!paisSelect) return;

    paisSelect.innerHTML = '<option value="">Seleccione...</option>';

    fetch('/paises')
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

// Exportar funciones globales para uso en HTML
window.setupAddressForm = setupAddressForm;
window.initializeMunicipiosDynamic = initializeMunicipiosDynamic;
window.initializePaisesForRegistro = initializePaisesForRegistro;
