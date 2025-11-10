/**
 * Módulo de Talento Humano
 * Búsqueda en tiempo real, creación y actualización de personas
 */

class TalentoHumanoManager {
    constructor() {
        this.searchTimeout = null;
        this.currentPersona = null;
        this.isEditing = false;

        this.elements = {
            searchInput: document.getElementById('numero_documento_buscar'),
            btnLimpiar: document.getElementById('btn-limpiar'),
            btnEditar: document.getElementById('btn-editar'),
            btnGuardar: document.getElementById('btn-guardar'),
            btnCancelar: document.getElementById('btn-cancelar'),
            formContainer: document.getElementById('form-container'),
            formTitle: document.getElementById('form-title'),
            personaForm: document.getElementById('personaForm'),
            personaIdInput: document.getElementById('persona_id'),
            actionModeInput: document.getElementById('action_mode')
        };

        this.init();
    }

    init() {
        this.attachEventListeners();
    }

    attachEventListeners() {
        // Búsqueda en tiempo real
        this.elements.searchInput.addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            const documento = e.target.value.trim();

            if (documento.length >= 3) {
                this.searchTimeout = setTimeout(() => {
                    this.buscarPersona(documento);
                }, 500);
            } else if (documento.length === 0) {
                this.ocultarFormulario();
            }
        });

        this.elements.btnLimpiar.addEventListener('click', () => this.limpiarTodo());
        this.elements.btnEditar.addEventListener('click', () => this.habilitarEdicion());
        this.elements.btnGuardar.addEventListener('click', () => this.guardarPersona());
        this.elements.btnCancelar.addEventListener('click', () => this.cancelar());

        // Validación en tiempo real
        this.elements.personaForm.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('blur', () => this.validarCampo(input));
        });
    }

    async buscarPersona(documento) {
        try {
            const response = await axios.post('/talento-humano/consultar', {
                cedula: documento
            });

            if (response.data.success) {
                this.mostrarPersonaExistente(response.data.data);
            } else if (response.data.show_form) {
                this.mostrarFormularioCreacion(documento);
            }
        } catch (error) {
            if (error.response?.status === 404) {
                this.mostrarFormularioCreacion(documento);
            } else {
                this.mostrarAlerta('error', 'Error al buscar persona',
                    'Ocurrió un error al realizar la búsqueda');
            }
        }
    }

    mostrarPersonaExistente(data) {
        this.currentPersona = data;
        this.isEditing = false;

        this.elements.formContainer.style.display = 'block';
        this.elements.formTitle.textContent = 'Información de la Persona';
        this.elements.actionModeInput.value = 'update';
        this.elements.personaIdInput.value = data.id || '';

        this.llenarFormulario(data);
        this.setFormularioSoloLectura(true);

        this.elements.btnEditar.style.display = 'inline-block';
        this.elements.btnGuardar.style.display = 'none';

        this.mostrarAlerta('success', 'Persona encontrada',
            `Se encontró la información de ${data.primer_nombre} ${data.primer_apellido}`);
    }

    mostrarFormularioCreacion(documento) {
        this.currentPersona = null;
        this.isEditing = true;

        this.elements.formContainer.style.display = 'block';
        this.elements.formTitle.textContent = 'Crear Nueva Persona';
        this.elements.actionModeInput.value = 'create';
        this.elements.personaIdInput.value = '';

        this.limpiarFormulario();

        // Pequeño delay para asegurar que el DOM esté listo
        setTimeout(() => {
            const numDocInput = document.getElementById('numero_documento');
            if (numDocInput) {
                numDocInput.value = documento;
            }
            this.setFormularioSoloLectura(false);
        }, 100);

        this.elements.btnEditar.style.display = 'none';
        this.elements.btnGuardar.style.display = 'inline-block';

        this.mostrarAlerta('info', 'Nueva persona',
            'Complete los datos para registrar la nueva persona');
    }

    habilitarEdicion() {
        this.isEditing = true;

        // Forzar habilitación con delay para asegurar que se aplique
        setTimeout(() => {
            this.setFormularioSoloLectura(false);
        }, 50);

        this.elements.btnEditar.style.display = 'none';
        this.elements.btnGuardar.style.display = 'inline-block';
        this.elements.formTitle.textContent = 'Editar Información';
    }

    async guardarPersona() {
        if (!this.validarFormulario()) {
            return;
        }

        const formData = new FormData(this.elements.personaForm);
        const isUpdate = this.elements.actionModeInput.value === 'update';

        const btnGuardar = this.elements.btnGuardar;
        const textoOriginal = btnGuardar.innerHTML;
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';

        try {
            let response;
            if (isUpdate && this.currentPersona) {
                response = await axios.post(
                    `/personas/${this.currentPersona.id}`,
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-HTTP-Method-Override': 'PUT'
                        }
                    }
                );
            } else {
                response = await axios.post('/talento-humano/personas', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
            }

            if (response.data.success) {
                this.mostrarAlerta('success', '¡Éxito!',
                    isUpdate ? 'Información actualizada correctamente' :
                        'Persona creada exitosamente');

                if (!isUpdate && response.data.data) {
                    this.mostrarPersonaExistente(response.data.data);
                } else {
                    this.setFormularioSoloLectura(true);
                    this.isEditing = false;
                    this.elements.btnEditar.style.display = 'inline-block';
                    this.elements.btnGuardar.style.display = 'none';
                }
            }
        } catch (error) {
            if (error.response?.status === 422) {
                this.mostrarErroresValidacion(error.response.data.errors);
            } else {
                this.mostrarAlerta('error', 'Error al guardar',
                    'No se pudo guardar la información. Por favor, intente nuevamente.');
            }
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = textoOriginal;
        }
    }

    llenarFormulario(data) {
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

        // Cargar ubicación con soporte dinámico
        const handler = window.selectDinamicoHandler;
        const paisSelect = $('#pais_id');
        const deptoSelect = $('#departamento_id');
        const muniSelect = $('#municipio_id');

        if (paisSelect.length && data.pais_id) {
            paisSelect.val(data.pais_id).attr('data-initial-value', data.pais_id);
        }

        if (deptoSelect.length && data.departamento_id) {
            deptoSelect.attr('data-initial-value', data.departamento_id);
        }

        if (muniSelect.length && data.municipio_id) {
            muniSelect.attr('data-initial-value', data.municipio_id);
        }

        if (handler && data.pais_id) {
            handler.loadDepartamentos(data.pais_id).then(() => {
                if (data.departamento_id) {
                    deptoSelect.val(data.departamento_id);
                    handler.loadMunicipios(data.departamento_id).then(() => {
                        if (data.municipio_id) {
                            muniSelect.val(data.municipio_id);
                        }
                    });
                }
            });
        }

        // Marcar caracterizaciones
        this.marcarCaracterizaciones(data.caracterizaciones || []);
    }

    marcarCaracterizaciones(caracterizaciones) {
        document.querySelectorAll('input[name="caracterizacion_ids[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });

        if (Array.isArray(caracterizaciones)) {
            caracterizaciones.forEach(id => {
                const checkbox = document.querySelector(
                    `input[name="caracterizacion_ids[]"][value="${id}"]`
                );
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        }
    }

    setFormularioSoloLectura(readonly) {
        const form = this.elements.personaForm;

        // Buscar todos los inputs, selects y textareas, incluyendo checkboxes
        const inputs = form.querySelectorAll(
            'input:not([type="hidden"]), select, textarea, input[type="checkbox"]'
        );

        inputs.forEach(input => {
            if (readonly) {
                input.setAttribute('readonly', 'readonly');
                input.setAttribute('disabled', 'disabled');
                input.classList.remove('is-invalid');
            } else {
                // Forzar habilitación removiendo múltiples veces por si acaso
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
                input.disabled = false;
                input.readOnly = false;
            }
        });

        // Habilitar/deshabilitar botones de caracterización
        const btnsCaracterizacion = form.querySelectorAll('[data-action]');
        btnsCaracterizacion.forEach(btn => {
            btn.disabled = readonly;
        });
    }

    validarFormulario() {
        const camposRequeridos = [
            { id: 'tipo_documento', nombre: 'Tipo de Documento' },
            { id: 'numero_documento', nombre: 'Número de Documento' },
            { id: 'primer_nombre', nombre: 'Primer Nombre' },
            { id: 'primer_apellido', nombre: 'Primer Apellido' },
            { id: 'fecha_nacimiento', nombre: 'Fecha de Nacimiento' },
            { id: 'genero', nombre: 'Género' },
            { id: 'celular', nombre: 'Celular' },
            { id: 'email', nombre: 'Correo Electrónico' },
            { id: 'pais_id', nombre: 'País' },
            { id: 'departamento_id', nombre: 'Departamento' },
            { id: 'municipio_id', nombre: 'Municipio' },
            { id: 'direccion', nombre: 'Dirección' }
        ];

        let valido = true;
        let primerCampoInvalido = null;

        camposRequeridos.forEach(campo => {
            const elemento = document.getElementById(campo.id);
            if (elemento && !elemento.value.trim()) {
                elemento.classList.add('is-invalid');
                if (!primerCampoInvalido) {
                    primerCampoInvalido = elemento;
                }
                valido = false;
            } else if (elemento) {
                elemento.classList.remove('is-invalid');
            }
        });

        if (!valido) {
            this.mostrarAlerta('warning', 'Campos requeridos',
                'Complete todos los campos obligatorios marcados con *');
            if (primerCampoInvalido) {
                primerCampoInvalido.focus();
            }
        }

        return valido;
    }

    validarCampo(input) {
        if (input.hasAttribute('required') || input.classList.contains('required')) {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        }
    }

    mostrarErroresValidacion(errores) {
        let mensaje = '<ul class="mb-0 text-left">';
        Object.values(errores).forEach(error => {
            if (Array.isArray(error)) {
                error.forEach(msg => {
                    mensaje += `<li>${msg}</li>`;
                });
            } else {
                mensaje += `<li>${error}</li>`;
            }
        });
        mensaje += '</ul>';

        Swal.fire({
            icon: 'error',
            title: 'Errores de validación',
            html: mensaje,
            confirmButtonText: 'Entendido'
        });
    }

    limpiarFormulario() {
        const form = this.elements.personaForm;
        form.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach(input => {
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = false;
            } else {
                input.value = '';
            }
            input.classList.remove('is-invalid');
            // Asegurar que no queden deshabilitados
            input.removeAttribute('readonly');
            input.removeAttribute('disabled');
            input.disabled = false;
            input.readOnly = false;
        });
    }

    limpiarTodo() {
        this.elements.searchInput.value = '';
        this.ocultarFormulario();
    }

    ocultarFormulario() {
        this.elements.formContainer.style.display = 'none';
        this.limpiarFormulario();
        this.currentPersona = null;
        this.isEditing = false;
    }

    cancelar() {
        if (this.currentPersona) {
            this.llenarFormulario(this.currentPersona);
            this.setFormularioSoloLectura(true);
            this.isEditing = false;
            this.elements.btnEditar.style.display = 'inline-block';
            this.elements.btnGuardar.style.display = 'none';
            this.elements.formTitle.textContent = 'Información de la Persona';
        } else {
            this.ocultarFormulario();
        }
    }

    mostrarAlerta(tipo, titulo, texto) {
        const iconos = {
            success: 'success',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };

        const SwalInstance = window.Swal;

        if (!SwalInstance) {
            window.alert([titulo, texto].filter(Boolean).join('\n'));
            return;
        }

        const opciones = {
            title: titulo,
            text: texto,
            confirmButtonText: 'Entendido',
            confirmButtonColor: tipo === 'success' ? '#28a745' : '#3085d6',
            allowOutsideClick: true
        };

        const version = SwalInstance.version || '';
        const usaTipo = version.startsWith('8.') || version.startsWith('7.') || version === '';

        if (usaTipo) {
            opciones.type = iconos[tipo] || 'info';
        } else {
            opciones.icon = iconos[tipo] || 'info';
        }

        SwalInstance.fire(opciones);
    }
}

// Inicializar
document.addEventListener('DOMContentLoaded', () => {
    new TalentoHumanoManager();
});
