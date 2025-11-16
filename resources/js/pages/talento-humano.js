/**
 * Módulo de Talento Humano
 * Búsqueda en tiempo real, creación y actualización de personas
 */

class TalentoHumanoManager {

    static obtenerInstancia() {
        if (!TalentoHumanoManager.instancia) {
            TalentoHumanoManager.instancia = new TalentoHumanoManager();
        }
        return TalentoHumanoManager.instancia;
    }

    static inyectarEstilosToast() {
        if (TalentoHumanoManager.estilosToastInyectados) {
            return;
        }

        const style = document.createElement('style');
        style.id = 'th-toast-styles';
        const cssRules = [
            '.th-toast-container {',
            '    position: fixed;',
            '    z-index: 9999;',
            '    display: flex;',
            '    flex-direction: column;',
            '    gap: 12px;',
            '    max-width: 420px;',
            '    pointer-events: none;',
            '}',
            '.th-toast-container.top,',
            '.th-toast-container.bottom {',
            '    left: 50%;',
            '    transform: translateX(-50%);',
            '}',
            '.th-toast {',
            '    display: flex;',
            '    align-items: flex-start;',
            '    gap: 14px;',
            '    background: #ffffff;',
            '    border-radius: 10px;',
            '    padding: 14px 16px;',
            '    box-shadow: 0 12px 30px rgba(0,0,0,0.12);',
            '    border-left: 5px solid transparent;',
            '    opacity: 0;',
            '    transform: translateY(20px);',
            '    transition: opacity 0.25s ease, transform 0.25s ease;',
            '    pointer-events: auto;',
            '}',
            '.th-toast-show {',
            '    opacity: 1;',
            '    transform: translateY(0);',
            '}',
            '.th-toast-hide {',
            '    opacity: 0;',
            '    transform: translateY(10px);',
            '}',
            '.th-toast-icon {',
            '    flex-shrink: 0;',
            '    width: 44px;',
            '    height: 44px;',
            '    display: flex;',
            '    align-items: center;',
            '    justify-content: center;',
            '    border-radius: 50%;',
            '    background: rgba(33, 37, 41, 0.1);',
            '    font-size: 22px;',
            '}',
            '.th-toast-content {',
            '    flex: 1;',
            '}',
            '.th-toast-title {',
            '    font-weight: 600;',
            '    font-size: 16px;',
            '    color: #212529;',
            '    margin-bottom: 4px;',
            '}',
            '.th-toast-text {',
            '    font-size: 14px;',
            '    color: #495057;',
            '    line-height: 1.5;',
            '}',
            '.th-toast-close {',
            '    background: transparent;',
            '    border: none;',
            '    color: #6c757d;',
            '    font-size: 18px;',
            '    cursor: pointer;',
            '    padding: 0;',
            '    line-height: 1;',
            '}',
            '.th-toast-close:focus {',
            '    outline: none;',
            '}',
            '.th-toast-success { border-left-color: #28a745; }',
            '.th-toast-error { border-left-color: #dc3545; }',
            '.th-toast-warning { border-left-color: #ffc107; }',
            '.th-toast-info { border-left-color: #17a2b8; }',
            '.th-toast-success .th-toast-icon { color: #28a745; background: rgba(40,167,69,0.1); }',
            '.th-toast-error .th-toast-icon { color: #dc3545; background: rgba(220,53,69,0.1); }',
            '.th-toast-warning .th-toast-icon { color: #d39e00; background: rgba(255,193,7,0.15); }',
            '.th-toast-info .th-toast-icon { color: #17a2b8; background: rgba(23,162,184,0.15); }'
        ];
        style.textContent = cssRules.join('\n');

        document.head.appendChild(style);
        TalentoHumanoManager.estilosToastInyectados = true;
    }
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
            btnRegistrarEntrada: document.getElementById('btn-registrar-entrada'),
            btnRegistrarSalida: document.getElementById('btn-registrar-salida'),
            formContainer: document.getElementById('form-container'),
            formTitle: document.getElementById('form-title'),
            personaForm: document.getElementById('personaForm'),
            personaIdInput: document.getElementById('persona_id'),
            actionModeInput: document.getElementById('action_mode'),
            modalSede: $('#modalSede'),
            selectSedeModal: document.getElementById('select_sede_modal'),
            btnConfirmarSede: document.getElementById('btn-confirmar-sede')
        };

        this.accionPendiente = null; // 'entrada' o 'salida'

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
                }, 4000);
            } else if (documento.length === 0) {
                this.ocultarFormulario();
            }
        });

        this.elements.btnLimpiar.addEventListener('click', () => this.limpiarTodo());
        this.elements.btnEditar.addEventListener('click', () => this.habilitarEdicion());
        this.elements.btnGuardar.addEventListener('click', () => this.guardarPersona());
        this.elements.btnCancelar.addEventListener('click', () => this.cancelar());
        this.elements.btnRegistrarEntrada.addEventListener('click', () => this.abrirModalSede('entrada'));
        this.elements.btnRegistrarSalida.addEventListener('click', () => this.abrirModalSede('salida'));
        this.elements.btnConfirmarSede.addEventListener('click', () => this.ejecutarRegistro());

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
        this.elements.btnRegistrarEntrada.style.display = 'inline-block';
        this.elements.btnRegistrarSalida.style.display = 'inline-block';

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

            // Inicializar selects de ubicación con valores por defecto (Colombia)
            const paisSelect = document.getElementById('pais_id');
            if (paisSelect && !paisSelect.value) {
                // Buscar el ID de Colombia (generalmente es 1 o 'COLOMBIA')
                const colombiaOption = Array.from(paisSelect.options).find(
                    opt => opt.text.toUpperCase().includes('COLOMBIA')
                );
                if (colombiaOption) {
                    paisSelect.value = colombiaOption.value;
                    // Disparar evento change para cargar departamentos
                    paisSelect.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        }, 100);

        this.elements.btnEditar.style.display = 'none';
        this.elements.btnGuardar.style.display = 'inline-block';
        this.elements.btnRegistrarEntrada.style.display = 'none';
        this.elements.btnRegistrarSalida.style.display = 'none';

        this.mostrarAlerta('info', 'Nueva persona',
            'Complete los datos para registrar la nueva persona', { duracion: 4000 });
    }

    habilitarEdicion() {
        this.isEditing = true;

        // Forzar habilitación con delay para asegurar que se aplique
        setTimeout(() => {
            this.setFormularioSoloLectura(false);
        }, 50);

        this.elements.btnEditar.style.display = 'none';
        this.elements.btnGuardar.style.display = 'inline-block';
        this.elements.btnRegistrarEntrada.style.display = 'none';
        this.elements.btnRegistrarSalida.style.display = 'none';
        this.elements.formTitle.textContent = 'Editar Información';
    }

    async guardarPersona() {
        if (!this.validarFormulario()) {
            return;
        }

        const formData = new FormData(this.elements.personaForm);
        const isUpdate = this.elements.actionModeInput.value === 'update';
        const personaId = this.elements.personaIdInput.value;

        const btnGuardar = this.elements.btnGuardar;
        const textoOriginal = btnGuardar.innerHTML;
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';

        try {
            let response;
            if (isUpdate) {
                if (personaId) {
                    response = await axios.post(
                        `/personas/${personaId}`,
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                                'X-HTTP-Method-Override': 'PUT'
                            }
                        }
                    );
                } else {
                    throw new Error('ID de persona no encontrado');
                }
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
                } else if (isUpdate && response.data.data) {
                    // Actualizar el formulario con los nuevos datos
                    this.llenarFormulario(response.data.data);
                    this.setFormularioSoloLectura(true);
                    this.isEditing = false;
                    this.elements.btnEditar.style.display = 'inline-block';
                    this.elements.btnGuardar.style.display = 'none';
                    this.elements.btnRegistrarEntrada.style.display = 'inline-block';
                    this.elements.btnRegistrarSalida.style.display = 'inline-block';
                } else {
                    this.setFormularioSoloLectura(true);
                    this.isEditing = false;
                    this.elements.btnEditar.style.display = 'inline-block';
                    this.elements.btnGuardar.style.display = 'none';
                    this.elements.btnRegistrarEntrada.style.display = 'inline-block';
                    this.elements.btnRegistrarSalida.style.display = 'inline-block';
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
        // Validar campos que tienen el atributo 'required' en el HTML
        const camposRequeridos = [
            { id: 'tipo_documento', nombre: 'Tipo de Documento' },
            { id: 'numero_documento', nombre: 'Número de Documento' },
            { id: 'primer_nombre', nombre: 'Primer Nombre' },
            { id: 'primer_apellido', nombre: 'Primer Apellido' },
            { id: 'fecha_nacimiento', nombre: 'Fecha de Nacimiento' },
            { id: 'genero', nombre: 'Género' },
            { id: 'pais_id', nombre: 'País' },
            { id: 'departamento_id', nombre: 'Departamento' },
            { id: 'municipio_id', nombre: 'Municipio' }
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
                // Scroll suave al primer campo inválido
                primerCampoInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
        this.elements.btnRegistrarEntrada.style.display = 'none';
        this.elements.btnRegistrarSalida.style.display = 'none';
    }

    cancelar() {
        if (this.currentPersona) {
            this.llenarFormulario(this.currentPersona);
            this.setFormularioSoloLectura(true);
            this.isEditing = false;
            this.elements.btnEditar.style.display = 'inline-block';
            this.elements.btnGuardar.style.display = 'none';
            this.elements.btnRegistrarEntrada.style.display = 'inline-block';
            this.elements.btnRegistrarSalida.style.display = 'inline-block';
            this.elements.formTitle.textContent = 'Información de la Persona';
        } else {
            this.ocultarFormulario();
        }
    }

    abrirModalSede(accion) {
        if (!this.currentPersona || !this.currentPersona.id) {
            this.mostrarAlerta('warning', 'Atención', 'Debe buscar una persona primero');
            return;
        }

        this.accionPendiente = accion;
        this.elements.selectSedeModal.value = '';
        this.elements.modalSede.modal('show');
    }

    async ejecutarRegistro() {
        const sedeId = this.elements.selectSedeModal.value;

        if (!sedeId) {
            this.mostrarAlerta('warning', 'Atención', 'Debe seleccionar una sede');
            return;
        }

        if (!this.currentPersona || !this.currentPersona.id) {
            this.mostrarAlerta('error', 'Error', 'No hay una persona seleccionada');
            return;
        }

        const personaId = this.currentPersona.id;
        const accion = this.accionPendiente;

        // Cerrar modal
        this.elements.modalSede.modal('hide');

        // Deshabilitar botones
        const btnAccion = accion === 'entrada' ? this.elements.btnRegistrarEntrada : this.elements.btnRegistrarSalida;
        const textoOriginal = btnAccion.innerHTML;
        btnAccion.disabled = true;
        btnAccion.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Procesando...';

        try {
            const endpoint = accion === 'entrada'
                ? '/api/presencia/entrada'
                : '/api/presencia/salida';

            const data = {
                persona_id: personaId,
                sede_id: parseInt(sedeId)
            };

            const response = await axios.post(endpoint, data);

            if (response.data.success) {
                this.mostrarAlerta('success', '¡Éxito!',
                    accion === 'entrada'
                        ? 'Entrada registrada correctamente'
                        : 'Salida registrada correctamente');
            } else {
                throw new Error(response.data.message || 'Error al registrar');
            }
        } catch (error) {
            const mensaje = error.response?.data?.message || error.message || 'Error al procesar la solicitud';
            this.mostrarAlerta('error', 'Error', mensaje);
        } finally {
            btnAccion.disabled = false;
            btnAccion.innerHTML = textoOriginal;
            this.accionPendiente = null;
        }
    }

    mostrarAlerta(tipo, titulo, texto, opciones = {}) {
        const { duracion = 5000, posicion = 'bottom-end' } = opciones;
        const duracionMs = Number.isFinite(Number(duracion)) && Number(duracion) > 0
            ? Number(duracion)
            : 5000;

        const posiciones = {
            'top-start': { top: '20px', left: '20px', right: 'auto', bottom: 'auto' },
            'top-end': { top: '20px', right: '20px', left: 'auto', bottom: 'auto' },
            'bottom-start': { bottom: '20px', left: '20px', right: 'auto', top: 'auto' },
            'bottom-end': { bottom: '20px', right: '20px', left: 'auto', top: 'auto' }
        };

        TalentoHumanoManager.inyectarEstilosToast();

        if (!this.toastContainer || !document.body.contains(this.toastContainer)) {
            this.toastContainer = document.createElement('div');
            this.toastContainer.className = 'th-toast-container';
            document.body.appendChild(this.toastContainer);
        }

        Object.assign(
            this.toastContainer.style,
            posiciones[posicion] || posiciones['bottom-end']
        );

        const iconosFA = {
            success: 'fas fa-check-circle',
            error: 'fas fa-times-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };

        const toast = document.createElement('div');
        toast.className = `th-toast th-toast-${tipo}`;
        toast.innerHTML = `
            <div class="th-toast-icon">
                <i class="${iconosFA[tipo] || iconosFA.info}"></i>
            </div>
            <div class="th-toast-content">
                <div class="th-toast-title">${titulo || ''}</div>
                ${texto ? `<div class="th-toast-text">${texto}</div>` : ''}
            </div>
            <button class="th-toast-close" type="button" aria-label="Cerrar notificación">&times;</button>
        `;

        const cerrarToast = () => {
            if (!toast.classList.contains('th-toast-hide')) {
                toast.classList.remove('th-toast-show');
                toast.classList.add('th-toast-hide');
                window.clearTimeout(toast._timeoutId);
                window.setTimeout(() => {
                    toast.remove();
                    if (this.toastContainer && !this.toastContainer.children.length) {
                        this.toastContainer.remove();
                        this.toastContainer = null;
                    }
                }, 260);
            }
        };

        toast.querySelector('.th-toast-close').addEventListener('click', cerrarToast);
        toast.addEventListener('mouseenter', () => window.clearTimeout(toast._timeoutId));
        toast.addEventListener('mouseleave', () => {
            toast._timeoutId = window.setTimeout(cerrarToast, duracionMs);
        });

        this.toastContainer.appendChild(toast);

        // Reflow para activar transición
        window.getComputedStyle(toast).opacity;
        toast.classList.add('th-toast-show');
        toast._timeoutId = window.setTimeout(cerrarToast, duracionMs);
    }
}

TalentoHumanoManager.instancia = null;
TalentoHumanoManager.estilosToastInyectados = false;

// Inicializar
document.addEventListener('DOMContentLoaded', () => {
    const manager = TalentoHumanoManager.obtenerInstancia();
    window.talentoHumanoManager = manager;
});
