/**
 * Módulo de Talento Humano
 * Maneja la consulta y creación de personas
 */

class TalentoHumanoManager {
    constructor() {
        this.elements = {
            btnConsultar: document.getElementById('btn-consultar'),
            btnLimpiar: document.getElementById('btn-limpiar'),
            btnCrearPersona: document.getElementById('btn-crear-persona'),
            btnCancelar: document.getElementById('btn-cancelar'),
            cedulaInput: document.getElementById('cedula'),
            formContainer: document.getElementById('form-container'),
            formTitle: document.getElementById('form-title'),
            personaForm: document.getElementById('personaForm'),
            actionType: document.getElementById('action_type')
        };

        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.init();
    }

    init() {
        this.attachEventListeners();
    }

    attachEventListeners() {
        this.elements.btnConsultar.addEventListener('click', () => this.consultarPersona());
        this.elements.btnLimpiar.addEventListener('click', () => this.limpiarFormulario());
        this.elements.btnCrearPersona.addEventListener('click', () => this.crearPersona());
        this.elements.btnCancelar.addEventListener('click', () => this.cancelar());
    }

    async consultarPersona() {
        const cedula = this.elements.cedulaInput.value.trim();
        if (!cedula) {
            this.showAlert('warning', 'Por favor ingrese una cédula');
            return;
        }

        const originalText = this.elements.btnConsultar.innerHTML;
        this.setButtonLoading(this.elements.btnConsultar, 'Consultando...');

        try {
            const formData = new FormData();
            formData.append('cedula', cedula);

            const response = await fetch('/talento-humano/consultar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.mostrarPersonaExistente(data);
            } else if (data.show_form) {
                this.mostrarFormularioCreacion(cedula, data.message);
            } else {
                this.elements.formContainer.style.display = 'none';
                this.showAlert('error', data.message);
            }
        } catch (error) {
            this.showAlert('error', 'Error de conexión. Por favor, verifique su conexión e intente nuevamente.');
        } finally {
            this.restoreButton(this.elements.btnConsultar, originalText);
        }
    }

    async crearPersona() {
        if (!this.validateRequiredFields()) {
            return;
        }

        this.elements.actionType.value = 'crear';
        const formData = new FormData(this.elements.personaForm);

        const originalText = this.elements.btnCrearPersona.innerHTML;
        this.setButtonLoading(this.elements.btnCrearPersona, 'Creando...');

        try {
            const response = await fetch('/talento-humano/personas', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.setFormReadOnly(true);
                this.elements.formTitle.textContent = 'Información de la Persona';
                this.toggleActionButtons(false);
                this.showAlert('success', data.message);
            } else {
                this.showAlert('error', data.message);
            }
        } catch (error) {
            this.showAlert('error', 'Error de conexión. Por favor, verifique su conexión e intente nuevamente.');
        } finally {
            this.restoreButton(this.elements.btnCrearPersona, originalText);
        }
    }

    mostrarPersonaExistente(data) {
        this.elements.formContainer.style.display = 'block';
        this.elements.formTitle.textContent = 'Información de la Persona';
        this.setFormReadOnly(true);
        this.fillFormData(data.data);
        this.showAlert('success', data.message);
    }

    mostrarFormularioCreacion(cedula, message) {
        this.elements.formContainer.style.display = 'block';
        this.elements.formTitle.textContent = 'Crear Nueva Persona';
        this.setFormReadOnly(false);
        document.getElementById('numero_documento').value = cedula;
        this.toggleActionButtons(true);
        this.showAlert('info', message);
    }

    fillFormData(data) {
        const campos = [
            'tipo_documento', 'numero_documento', 'primer_nombre', 'segundo_nombre',
            'primer_apellido', 'segundo_apellido', 'fecha_nacimiento', 'genero',
            'telefono', 'celular', 'email', 'pais_id', 'direccion'
        ];

        campos.forEach(campo => {
            const elemento = document.getElementById(campo);
            if (elemento) {
                elemento.value = data[campo] || '';
            }
        });

        // Cargar departamento y municipio
        this.setLocationData(data);

        // Marcar caracterizaciones
        this.setCaracterizaciones(data.caracterizaciones);
    }

    setLocationData(data) {
        const departamentoSelect = document.getElementById('departamento_id');
        const municipioSelect = document.getElementById('municipio_id');

        if (data.departamento_id) {
            departamentoSelect.value = data.departamento_id;
            departamentoSelect.setAttribute('data-initial-value', data.departamento_id);
        }

        if (data.municipio_id) {
            municipioSelect.setAttribute('data-initial-value', data.municipio_id);
            departamentoSelect.dispatchEvent(new Event('change'));
        }
    }

    setCaracterizaciones(caracterizaciones) {
        document.querySelectorAll('input[name="caracterizacion_ids[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });

        if (caracterizaciones && Array.isArray(caracterizaciones)) {
            caracterizaciones.forEach(caracId => {
                const checkbox = document.querySelector(
                    `input[name="caracterizacion_ids[]"][value="${caracId}"]`
                );
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        }
    }

    setFormReadOnly(readOnly) {
        const form = this.elements.personaForm;
        const inputs = form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            if (readOnly) {
                input.setAttribute('readonly', 'readonly');
                input.setAttribute('disabled', 'disabled');
            } else {
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
            }
        });

        this.toggleActionButtons(!readOnly);
    }

    toggleActionButtons(show) {
        this.elements.btnCrearPersona.style.display = show ? 'inline-block' : 'none';
        this.elements.btnCancelar.style.display = show ? 'inline-block' : 'none';
        document.getElementById('btn-guardar-cambios').style.display = 'none';
    }

    clearFormData() {
        const form = this.elements.personaForm;
        const inputs = form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            if (input.type === 'radio' || input.type === 'checkbox') {
                input.checked = false;
            } else {
                input.value = '';
            }
        });

        this.toggleActionButtons(false);
    }

    limpiarFormulario() {
        this.elements.cedulaInput.value = '';
        this.elements.formContainer.style.display = 'none';
        this.clearFormData();
    }

    cancelar() {
        this.elements.formContainer.style.display = 'none';
        this.clearFormData();
    }

    validateRequiredFields() {
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
            this.showAlert('warning', 'Por favor complete todos los campos obligatorios marcados con *.');
            if (firstInvalidField) {
                firstInvalidField.focus();
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        return isValid;
    }

    setButtonLoading(button, text) {
        button.disabled = true;
        button.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i>${text}`;
    }

    restoreButton(button, originalText) {
        button.disabled = false;
        button.innerHTML = originalText;
    }

    showAlert(type, message) {
        const alertTypes = {
            success: { class: 'alert-success', icon: 'check-circle' },
            warning: { class: 'alert-warning', icon: 'exclamation-triangle' },
            info: { class: 'alert-info', icon: 'info-circle' },
            error: { class: 'alert-danger', icon: 'exclamation-triangle' }
        };

        const config = alertTypes[type] || alertTypes.error;

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${config.class} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            <i class="fas fa-${config.icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(alertDiv);

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new TalentoHumanoManager();
});

