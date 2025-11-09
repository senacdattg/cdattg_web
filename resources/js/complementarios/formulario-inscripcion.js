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
    CONFIG.CAMPOS_TEXTOS.forEach(campoId => {
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

// Función común para procesar respuestas de API de ubicaciones
function processLocationData(data, entityType) {
    let items = [];

    if (data.success && data.data) {
        // Estructura: {success: true, data: [...]}
        items = data.data;
    } else if (Array.isArray(data)) {
        // Estructura: [...]
        items = data;
    } else if (data && typeof data === 'object') {
        // Estructura: {departamentos: [...]} u otra variante
        items = data[entityType] || data.data || [];
    }

    return items;
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
        // Manejar diferentes nombres de campo
        const label = item.nombre ?? item[entityType] ?? item.name ?? item.label ?? '';
        option.textContent = label || `ID ${item.id}`;
        // Seleccionar el item si coincide con el ID proporcionado
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

    // Limpiar select y mostrar opción por defecto
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
    CONFIG.CAMPOS_DIRECCION_NUMERICOS.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element) {
            element.addEventListener('keypress', soloNumeros);
        }
    });
}

// Inicializar setupAddressForm cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    setupAddressForm();
});




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


// Función auxiliar para encontrar radio button de "NINGUNA"
function findNingunaRadio(radios) {
    for (const radio of radios) {
        const label = document.querySelector(`label[for="${radio.id}"]`);
        if (label && label.textContent.trim().toUpperCase() === CONFIG.NINGUNA_LABEL) {
            return radio;
        }
    }
    return null;
}

// Función auxiliar para manejar cambio de selección en radios
function handleRadioChange(radio, ningunaRadio, allRadios) {
    if (radio !== ningunaRadio && radio.checked) {
        // Si se selecciona cualquier opción que no sea "NINGUNA", deseleccionar "NINGUNA"
        ningunaRadio.checked = false;
    } else if (radio === ningunaRadio && radio.checked) {
        // Si se selecciona "NINGUNA", deseleccionar todas las demás opciones
        allRadios.forEach(otherRadio => {
            if (otherRadio !== ningunaRadio) {
                otherRadio.checked = false;
            }
        });
    }
}

// Función auxiliar para resetear selección a "NINGUNA"
function resetToNinguna(ningunaRadio, allRadios) {
    ningunaRadio.checked = true;
    allRadios.forEach(radio => {
        if (radio !== ningunaRadio) {
            radio.checked = false;
        }
    });
}

// Configurar manejo de caracterización (parámetros)
function setupCaracterizacionHandling() {
    const caracterizacionRadios = document.querySelectorAll('input[name="parametro_id"]');
    const ningunaRadio = findNingunaRadio(caracterizacionRadios);

    // Si no se encuentra "NINGUNA", salir
    if (!ningunaRadio) return;

    // Establecer "NINGUNA" como seleccionada por defecto
    ningunaRadio.checked = true;

    // Agregar event listeners a todos los radio buttons
    caracterizacionRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            handleRadioChange(radio, ningunaRadio, caracterizacionRadios);
        });
    });

    // Manejar el evento de reset del formulario
    const form = document.getElementById('formInscripcion');
    if (form) {
        form.addEventListener('reset', () => {
            setTimeout(() => resetToNinguna(ningunaRadio, caracterizacionRadios), 0);
        });
    }
}

// Función auxiliar para calcular fecha límite de edad mínima
function calcularFechaLimiteEdadMinima() {
    const fechaLimite = new Date();
    fechaLimite.setFullYear(fechaLimite.getFullYear() - CONFIG.EDAD_MINIMA);
    return fechaLimite;
}

// Función auxiliar para validar edad
function validarEdad(fechaNacimiento) {
    const fechaSeleccionada = new Date(fechaNacimiento);
    const fechaLimite = calcularFechaLimiteEdadMinima();
    return fechaSeleccionada <= fechaLimite;
}

// Función auxiliar para mostrar/ocultar mensaje de error de edad
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

// Configurar validación de edad mínima
function setupEdadMinimaValidation() {
    const fechaNacimientoInput = document.getElementById('fecha_nacimiento');
    if (!fechaNacimientoInput) return;

    // Establecer el atributo max si no está ya establecido
    if (!fechaNacimientoInput.getAttribute('max')) {
        const fechaMaximaStr = calcularFechaLimiteEdadMinima().toISOString().split('T')[0];
        fechaNacimientoInput.setAttribute('max', fechaMaximaStr);
    }

    // Validar cuando cambia la fecha
    fechaNacimientoInput.addEventListener('change', function() {
        const esValida = validarEdad(this.value);
        toggleMensajeErrorEdad(this, !esValida);
    });

    // Validar al enviar el formulario
    const form = document.getElementById('formInscripcion');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validarEdad(fechaNacimientoInput.value)) {
                e.preventDefault();
                fechaNacimientoInput.focus();
                toggleMensajeErrorEdad(fechaNacimientoInput, true);
                alert(MENSAJE_EDAD_INVALIDA);
                return false;
            }
        });
    }
}

// Exportar funciones globales para uso en HTML
window.setupAddressForm = setupAddressForm;
window.initializeMunicipiosDynamic = initializeMunicipiosDynamic;
