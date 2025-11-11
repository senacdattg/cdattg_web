const CONFIG = {
    TEXT_FIELDS: ['primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido'],
    NUMERIC_FIELDS: ['numero_documento', 'telefono', 'celular'],
    ADDRESS_NUMERIC_FIELDS: ['numero_via', 'numero_casa'],
    NINGUNA_LABEL: 'NINGUNA',
    MODAL_SELECTOR: '#addressModal',
    TOGGLE_SELECTOR: '#toggleAddressForm',
    DIRECCION_SELECTOR: '#direccion',
    CARACTERIZACION_CHECKBOX_FIELD: 'input[name="caracterizacion_ids[]"]',
    SELECT_ALL_ACTION: '[data-action="caracterizacion-select-all"]',
    CLEAR_ACTION: '[data-action="caracterizacion-clear"]',
    GROUP_SELECTOR: '.caracterizacion-group',
    EDAD_MINIMA: 14,
    MENSAJE_EDAD_INVALIDA: 'Debe tener al menos 14 años para registrarse.'
};

const SELECTORS = {
    FORM_SELECTOR: 'form',
    REGISTRO_FORM: '#registroForm',
    FECHA_NACIMIENTO: '#fecha_nacimiento',
    PAIS: '#pais_id',
    DEPARTAMENTO: '#departamento_id',
    MUNICIPIO: '#municipio_id',
    PRELOADER_CLASS: 'preloader-active'
};

class DomFacade {
    static find(selector) {
        return document.querySelector(selector);
    }

    static findAll(selector) {
        return Array.from(document.querySelectorAll(selector));
    }
}

function bindOnce(element, event, handler, flag) {
    if (!element) {
        return;
    }

    const key = flag || `persona${event}`;
    if (element.dataset[key]) {
        return;
    }

    element.addEventListener(event, handler);
    element.dataset[key] = 'true';
}

function resolveField(form, fieldName) {
    return (
        form.querySelector(`#${fieldName}`) ||
        form.querySelector(`[name="${fieldName}"]`)
    );
}

class HttpClient {
    constructor(logger = console) {
        this.client = window.axios || null;
        this.logger = logger;
        this.defaultHeaders = {
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json'
        };
    }

    async get(url, config = {}) {
        if (!url) {
            throw new Error('URL requerida para la solicitud.');
        }

        const headers = { ...this.defaultHeaders, ...(config.headers || {}) };

        if (this.client) {
            const response = await this.client.get(url, { ...config, headers });
            return response.data;
        }

        const response = await fetch(url, { headers });
        if (!response.ok) {
            const error = new Error(`HTTP ${response.status}`);
            this.logger.error('Solicitud fallida:', error);
            throw error;
        }

        return response.json();
    }
}

class InputHandler {
    constructor(config) {
        this.config = config;
        this.onlyDigits = this.onlyDigits.bind(this);
    }

    attach(forms) {
        this.attachUppercase(forms);
        this.attachNumeric(forms);
    }

    attachUppercase(forms) {
        this.config.TEXT_FIELDS.forEach((fieldName) => {
            forms.forEach((form) => {
                const field = resolveField(form, fieldName);
                bindOnce(field, 'input', () => {
                    field.value = field.value.toUpperCase().replace(/\d/g, '');
                }, `personaUpper_${fieldName}`);
            });
        });
    }

    attachNumeric(forms) {
        const targets = [...this.config.NUMERIC_FIELDS, ...this.config.ADDRESS_NUMERIC_FIELDS];
        targets.forEach((fieldName) => {
            forms.forEach((form) => {
                const field = resolveField(form, fieldName);
                bindOnce(field, 'keypress', this.onlyDigits, `personaNumeric_${fieldName}`);
            });
        });
    }

    onlyDigits(event) {
        if (event.ctrlKey || event.altKey || event.metaKey) {
            return;
        }

        if (!/\d/.test(event.key)) {
            event.preventDefault();
        }
    }
}

class CaracterizacionHandler {
    constructor(config) {
        this.config = config;
    }

    attach() {
        const checkboxes = DomFacade.findAll(this.config.CARACTERIZACION_CHECKBOX_FIELD);
        if (!checkboxes.length) {
            return;
        }

        const selectAllButton = document.querySelector(this.config.SELECT_ALL_ACTION);
        const clearButton = document.querySelector(this.config.CLEAR_ACTION);

        bindOnce(selectAllButton, 'click', (event) => {
            event.preventDefault();
            this.toggleCheckboxes(true);
        }, 'personaCaracterizacionSelectAll');

        bindOnce(clearButton, 'click', (event) => {
            event.preventDefault();
            this.toggleCheckboxes(false);
        }, 'personaCaracterizacionClear');

        const ningunaCheckbox = this.findNingunaCheckbox(checkboxes);

        checkboxes.forEach((checkbox) => {
            bindOnce(checkbox, 'change', () => {
                if (!ningunaCheckbox) {
                    return;
                }

                if (checkbox === ningunaCheckbox && checkbox.checked) {
                    checkboxes.forEach((other) => {
                        if (other !== ningunaCheckbox) {
                            other.checked = false;
                        }
                    });
                } else if (checkbox !== ningunaCheckbox && checkbox.checked) {
                    ningunaCheckbox.checked = false;
                }
            }, `personaCaracterizacion_${checkbox.id || checkbox.name}`);
        });
    }

    toggleCheckboxes(checked) {
        DomFacade.findAll(this.config.GROUP_SELECTOR).forEach((group) => {
            group.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
                checkbox.checked = checked;
            });
        });
    }

    findNingunaCheckbox(checkboxes) {
        return (
            checkboxes.find((checkbox) => {
                if (checkbox.dataset.caracterizacion === 'ninguna') {
                    return true;
                }

                const label = document.querySelector(`label[for="${checkbox.id}"]`);
                return label && label.textContent.trim().toUpperCase() === this.config.NINGUNA_LABEL;
            }) || null
        );
    }
}

class AddressHandler {
    constructor(config, numericGuard) {
        this.config = config;
        this.numericGuard = numericGuard;
    }

    attach() {
        const modalInstance = window.jQuery ? window.jQuery(this.config.MODAL_SELECTOR) : null;

        if (!modalInstance || !modalInstance.length) {
            return;
        }

        const toggleButton = DomFacade.find(this.config.TOGGLE_SELECTOR);
        const direccionInput = DomFacade.find(this.config.DIRECCION_SELECTOR);
        const saveButton = document.getElementById('saveAddress');
        const cancelButton = document.getElementById('cancelAddress');

        this.config.ADDRESS_NUMERIC_FIELDS.forEach((fieldId) => {
            const field = document.getElementById(fieldId);
            bindOnce(field, 'keypress', this.numericGuard, `personaAddressNumeric_${fieldId}`);
        });

        const openModal = () => {
            modalInstance.modal('show');
            toggleButton?.setAttribute('aria-expanded', 'true');
        };

        const closeModal = () => {
            modalInstance.modal('hide');
            toggleButton?.setAttribute('aria-expanded', 'false');
        };

        bindOnce(toggleButton, 'click', openModal, 'personaAddressToggle');
        bindOnce(direccionInput, 'focus', openModal, 'personaAddressFocus');
        bindOnce(direccionInput, 'click', openModal, 'personaAddressClick');

        bindOnce(saveButton, 'click', () => {
            const direccion = this.buildAddress();
            if (!direccion) {
                return;
            }

            if (direccionInput) {
                direccionInput.value = direccion;
                direccionInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            this.clearAddressFields();
            closeModal();
        }, 'personaAddressSave');

        bindOnce(cancelButton, 'click', () => {
            this.clearAddressFields();
            closeModal();
        }, 'personaAddressCancel');

        modalInstance.on('hidden.bs.modal', () => this.clearAddressFields());
    }

    buildAddress() {
        const tipoVia = this.getValue('tipo_via');
        const numeroVia = this.getValue('numero_via');
        const letraVia = this.getValue('letra_via');
        const viaSecundaria = this.getValue('via_secundaria');
        const numeroCasa = this.getValue('numero_casa');
        const complementos = this.getValue('complementos');
        const barrio = this.getValue('barrio');

        if (!tipoVia || !numeroVia || !numeroCasa) {
            alert('Completa Tipo de vía, Número de vía y Número de casa.');
            return null;
        }

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

        return direccion;
    }

    clearAddressFields() {
        DomFacade.findAll('.address-field').forEach((field) => {
            if (field.tagName === 'SELECT') {
                field.selectedIndex = 0;
            } else {
                field.value = '';
            }
        });
    }

    getValue(id) {
        const element = document.getElementById(id);
        return element ? element.value.trim() : '';
    }
}

class CollectionHelper {
    static normalize(data, key) {
        if (Array.isArray(data)) {
            return data;
        }

        if (!data || typeof data !== 'object') {
            return [];
        }

        if (data.data !== undefined) {
            return CollectionHelper.normalize(data.data, key);
        }

        if (key && data[key] !== undefined) {
            return CollectionHelper.normalize(data[key], key);
        }

        if (data.result !== undefined) {
            return CollectionHelper.normalize(data.result, key);
        }

        return Object.values(data);
    }

    static resolveLabel(item, fallbackKey) {
        if (!item) {
            return '';
        }

        const candidates = ['nombre', 'name', 'label', fallbackKey, 'pais', 'departamento', 'municipio'];

        for (const candidate of candidates) {
            if (candidate && item[candidate]) {
                return String(item[candidate]);
            }
        }

        return item.id ?? '';
    }
}

class LocationService {
    constructor(selectors, httpClient) {
        this.selectors = selectors;
        this.http = httpClient;
        this.PLACEHOLDERS = {
            paises: 'Seleccione un país',
            departamentos: 'Seleccione un departamento',
            municipios: 'Seleccione un municipio'
        };
    }

    async init() {
        const paisSelect = DomFacade.find(this.selectors.PAIS);
        if (!paisSelect) {
            return;
        }

        const departamentoSelect = DomFacade.find(this.selectors.DEPARTAMENTO);
        const municipioSelect = DomFacade.find(this.selectors.MUNICIPIO);

        const paisInitial = paisSelect.dataset.initialValue || '';
        const departamentoInitial = departamentoSelect?.dataset.initialValue || '';
        const municipioInitial = municipioSelect?.dataset.initialValue || '';

        if (paisSelect.dataset.url && (paisSelect.options.length <= 1 || !paisSelect.value)) {
            await this.loadPaises(paisInitial || null);
        }

        this.attachListeners();

        if (paisInitial) {
            await this.loadDepartamentos(paisInitial, departamentoInitial || null);
            if (departamentoInitial) {
                await this.loadMunicipios(departamentoInitial, municipioInitial || null);
            }
        } else if (departamentoInitial) {
            await this.loadMunicipios(departamentoInitial, municipioInitial || null);
        }
    }

    attachListeners() {
        const paisSelect = DomFacade.find(this.selectors.PAIS);
        const departamentoSelect = DomFacade.find(this.selectors.DEPARTAMENTO);

        bindOnce(paisSelect, 'change', (event) => {
            const paisId = event.target.value;
            void this.loadDepartamentos(paisId);
        }, 'personaPaisListener');

        bindOnce(departamentoSelect, 'change', (event) => {
            const departamentoId = event.target.value;
            void this.loadMunicipios(departamentoId);
        }, 'personaDepartamentoListener');
    }

    async loadPaises(selectedId = null) {
        const paisSelect = DomFacade.find(this.selectors.PAIS);
        if (!paisSelect || !paisSelect.dataset.url) {
            return;
        }

        this.populateSelect(paisSelect, [], this.PLACEHOLDERS.paises, selectedId, 'pais');

        try {
            const data = await this.http.get(paisSelect.dataset.url);
            const collection = CollectionHelper.normalize(data, 'paises');

            if (!collection.length) {
                this.setMessage(paisSelect, 'No hay países disponibles');
                return;
            }

            this.populateSelect(paisSelect, collection, this.PLACEHOLDERS.paises, selectedId, 'pais');
        } catch (error) {
            console.error('Error cargando países:', error);
            this.setMessage(paisSelect, 'Error cargando países');
        }
    }

    async loadDepartamentos(paisId, selectedId = null) {
        const departamentoSelect = DomFacade.find(this.selectors.DEPARTAMENTO);
        if (!departamentoSelect) {
            return;
        }

        this.populateSelect(departamentoSelect, [], this.PLACEHOLDERS.departamentos, selectedId, 'departamento');

        const municipioSelect = DomFacade.find(this.selectors.MUNICIPIO);
        if (municipioSelect) {
            this.populateSelect(municipioSelect, [], this.PLACEHOLDERS.municipios, null, 'municipio');
        }

        const endpoint = this.resolveEndpoint(departamentoSelect, '/departamentos', paisId);
        if (!endpoint) {
            return;
        }

        try {
            const data = await this.http.get(endpoint);
            const collection = CollectionHelper.normalize(data, 'departamentos');

            if (!collection.length) {
                this.setMessage(departamentoSelect, 'No hay departamentos disponibles');
                return;
            }

            this.populateSelect(departamentoSelect, collection, this.PLACEHOLDERS.departamentos, selectedId, 'departamento');
        } catch (error) {
            console.error('Error cargando departamentos:', error);
            this.setMessage(departamentoSelect, 'Error cargando departamentos');
        }
    }

    async loadMunicipios(departamentoId, selectedId = null) {
        const municipioSelect = DomFacade.find(this.selectors.MUNICIPIO);
        if (!municipioSelect) {
            return;
        }

        this.populateSelect(municipioSelect, [], this.PLACEHOLDERS.municipios, selectedId, 'municipio');

        const endpoint = this.resolveEndpoint(municipioSelect, '/municipios', departamentoId);
        if (!endpoint) {
            return;
        }

        try {
            const data = await this.http.get(endpoint);
            const collection = CollectionHelper.normalize(data, 'municipios');

            if (!collection.length) {
                this.setMessage(municipioSelect, 'No hay municipios disponibles');
                return;
            }

            this.populateSelect(municipioSelect, collection, this.PLACEHOLDERS.municipios, selectedId, 'municipio');
        } catch (error) {
            console.error('Error cargando municipios:', error);
            this.setMessage(municipioSelect, 'Error cargando municipios');
        }
    }

    populateSelect(select, items, placeholder, selectedId, fallbackKey) {
        if (!select) {
            return;
        }

        select.innerHTML = '';

        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = placeholder;
        if (!selectedId) {
            placeholderOption.selected = true;
        }
        select.appendChild(placeholderOption);

        if (!Array.isArray(items) || !items.length) {
            return;
        }

        items.forEach((item) => {
            const option = document.createElement('option');
            option.value = item.id ?? '';
            option.textContent = CollectionHelper.resolveLabel(item, fallbackKey);

            if (selectedId && String(item.id) === String(selectedId)) {
                option.selected = true;
                placeholderOption.selected = false;
            }

            select.appendChild(option);
        });
    }

    setMessage(select, message) {
        if (!select) {
            return;
        }

        select.innerHTML = '';
        const option = document.createElement('option');
        option.value = '';
        option.textContent = message;
        option.selected = true;
        select.appendChild(option);
    }

    resolveEndpoint(select, fallbackBase, value) {
        if (!select || !value) {
            return null;
        }

        const template = select.dataset.urlTemplate;
        if (template) {
            if (template.includes('__ID__')) {
                return template.replace('__ID__', value);
            }
            return template.replace(/:id\b/, value);
        }

        if (!fallbackBase) {
            return null;
        }

        const sanitizedBase = fallbackBase.replace(/\/+$/, '');
        return `${sanitizedBase}/${value}`;
    }
}

class AgeValidator {
    constructor(config, selectors) {
        this.config = config;
        this.selectors = selectors;
    }

    attach(forms) {
        const field = DomFacade.find(this.selectors.FECHA_NACIMIENTO);
        if (!field) {
            return;
        }

        const maxDate = this.getAgeLimit();
        field.setAttribute('max', maxDate.toISOString().split('T')[0]);

        bindOnce(field, 'change', () => this.validateAge(field), 'personaAgeChange');

        if (field.value) {
            this.validateAge(field);
        }

        forms.forEach((form) => {
            bindOnce(form, 'submit', (event) => {
                if (!this.validateAge(field)) {
                    event.preventDefault();
                    alert(this.config.MENSAJE_EDAD_INVALIDA);
                }
            }, 'personaAgeSubmit');
        });
    }

    getAgeLimit() {
        const limit = new Date();
        limit.setFullYear(limit.getFullYear() - this.config.EDAD_MINIMA);
        return limit;
    }

    validateAge(field) {
        if (!field.value) {
            this.clearAgeError(field);
            return true;
        }

        const isValid = new Date(field.value) <= this.getAgeLimit();
        if (isValid) {
            this.clearAgeError(field);
            return true;
        }

        this.applyAgeError(field);
        return false;
    }

    applyAgeError(field) {
        field.setCustomValidity(this.config.MENSAJE_EDAD_INVALIDA);
        field.classList.add('is-invalid');

        let feedback = field.parentElement.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentElement.appendChild(feedback);
        }
        feedback.textContent = this.config.MENSAJE_EDAD_INVALIDA;
    }

    clearAgeError(field) {
        field.setCustomValidity('');
        field.classList.remove('is-invalid');
        const feedback = field.parentElement.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    }
}

class ValidationHandler {
    constructor(config) {
        this.config = config;
        this.textPattern = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s-]+$/;
    }

    attach(forms) {
        forms.forEach((form) => {
            bindOnce(form, 'submit', (event) => {
                if (!this.validate(form)) {
                    event.preventDefault();
                }
            }, 'personaValidation');
        });
    }

    validate(form) {
        return (
            this.validateTextFields(form) &&
            this.validateNumericField(form, '#numero_documento', 'El número de documento solo puede contener números.') &&
            this.validateNumericField(form, '#telefono', 'El teléfono fijo solo puede contener números.') &&
            this.validateNumericField(form, '#celular', 'El celular solo puede contener números.')
        );
    }

    validateTextFields(form) {
        return this.config.TEXT_FIELDS.every((fieldName) => {
            const field = resolveField(form, fieldName);

            if (!field || !field.value) {
                return true;
            }

            if (this.textPattern.test(field.value)) {
                return true;
            }

            alert(`El campo ${fieldName.replace('_', ' ')} solo puede contener letras, espacios y guiones.`);
            field.focus();
            return false;
        });
    }

    validateNumericField(form, selector, message) {
        const field = form.querySelector(selector);
        if (!field || !field.value) {
            return true;
        }

        if (/^\d+$/.test(field.value)) {
            return true;
        }

        alert(message);
        field.focus();
        return false;
    }
}

class PreloaderHandler {
    constructor(selectors) {
        this.selectors = selectors;
    }

    attach(forms) {
        const registroForm = forms.find((form) => form.id === 'registroForm');
        if (!registroForm) {
            return;
        }

        bindOnce(registroForm, 'submit', () => {
            document.body.classList.add(this.selectors.PRELOADER_CLASS);
        }, 'personaPreloader');
    }
}

class PersonaFormManager {
    constructor(config, selectors) {
        this.config = config;
        this.selectors = selectors;
        this.state = { initialized: false };

        this.input = new InputHandler(config);
        this.caracterizacion = new CaracterizacionHandler(config);
        this.address = new AddressHandler(config, this.input.onlyDigits);
        this.location = new LocationService(selectors, new HttpClient());
        this.age = new AgeValidator(config, selectors);
        this.validation = new ValidationHandler(config);
        this.preloader = new PreloaderHandler(selectors);
    }

    getTargetForms() {
        return DomFacade.findAll(this.selectors.FORM_SELECTOR).filter((form) => {
            return (
                form.id === 'registroForm' ||
                form.id === 'formInscripcion' ||
                form.querySelector('[name="primer_nombre"]')
            );
        });
    }

    async initialize(force = false) {
        const forms = this.getTargetForms();
        if (!forms.length) {
            return;
        }

        if (this.state.initialized && !force) {
            return;
        }

        this.input.attach(forms);
        this.caracterizacion.attach();
        this.address.attach();

        try {
            await this.location.init();
        } catch (error) {
            console.error('Error inicializando ubicaciones:', error);
        }

        this.age.attach(forms);
        this.validation.attach(forms);
        this.preloader.attach(forms);

        this.state.initialized = true;
    }

    async init(force = true) {
        await this.initialize(force);
    }

    async loadPaises(selectedId = null) {
        await this.location.loadPaises(selectedId);
    }

    async loadDepartamentos(paisId, selectedId = null) {
        await this.location.loadDepartamentos(paisId, selectedId);
    }

    async loadMunicipios(departamentoId, selectedId = null) {
        await this.location.loadMunicipios(departamentoId, selectedId);
    }

    attachListeners() {
        this.location.attachListeners();
    }
}

const personaFormManager = new PersonaFormManager(CONFIG, SELECTORS);

document.addEventListener('DOMContentLoaded', () => {
    void personaFormManager.initialize(false);
});

window.PersonaForm = {
    init: (force = true) => personaFormManager.init(force),
    loadPaises: (selectedId = null) => personaFormManager.loadPaises(selectedId),
    loadDepartamentos: (paisId, selectedId = null) => personaFormManager.loadDepartamentos(paisId, selectedId),
    loadMunicipios: (departamentoId, selectedId = null) => personaFormManager.loadMunicipios(departamentoId, selectedId),
    attachListeners: () => personaFormManager.attachListeners()
};

