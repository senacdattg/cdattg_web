const CONFIG = {
    TEXT_FIELDS: ['primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'complementos', 'barrio'],
    NUMERIC_FIELDS: ['numero_documento', 'telefono', 'celular'],
    ADDRESS_NUMERIC_FIELDS: ['numero_via', 'numero_via_secundaria', 'numero_casa'],
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
        const fieldsAlfa = ['primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido'];
        fieldsAlfa.forEach((fieldName) => {
            forms.forEach((form) => {
                const field = resolveField(form, fieldName);
                bindOnce(field, 'input', () => {
                    field.value = field.value.toUpperCase().replace(/\d/g, '');
                }, `personaUpper_${fieldName}`);
            });
        });

        const fieldsUpper = this.config.TEXT_FIELDS.filter((name) => !fieldsAlfa.includes(name));
        fieldsUpper.forEach((fieldName) => {
            forms.forEach((form) => {
                const field = resolveField(form, fieldName);
                bindOnce(field, 'input', () => {
                    field.value = field.value.toUpperCase();
                }, `personaUpper_${fieldName}`);
            });
        });
        this.attachNumeric(forms);
    }

    attachUppercase(forms) {
        this.config.TEXT_FIELDS.forEach((fieldName) => {
            forms.forEach((form) => {
                const field = resolveField(form, fieldName);
                bindOnce(field, 'input', () => {
                    field.value = field.value.toUpperCase();
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
        this.handleFieldChange = this.handleFieldChange.bind(this);
        this.handleSave = this.handleSave.bind(this);
        this.handleCancel = this.handleCancel.bind(this);
        this.errorContainer = null;
        this.previewContainer = null;
        this.saveButton = null;
        this.direccionInput = null;
        this.openModal = null;
        this.closeModal = null;
        this.progressBar = null;
        this.progressLabel = null;
        this.missingList = null;
        this.previewCard = null;
        this.optionCache = {
            via: null,
            cardinal: null
        };
    }

    attach() {
        const modalInstance = window.jQuery ? window.jQuery(this.config.MODAL_SELECTOR) : null;

        if (!modalInstance || !modalInstance.length) {
            return;
        }

        const toggleButton = DomFacade.find(this.config.TOGGLE_SELECTOR);
        this.direccionInput = DomFacade.find(this.config.DIRECCION_SELECTOR);
        this.saveButton = document.getElementById('saveAddress');
        const cancelButton = document.getElementById('cancelAddress');
        this.errorContainer = document.getElementById('addressError');
        this.previewContainer = document.getElementById('addressPreview');
        this.progressBar = document.getElementById('addressProgress');
        this.progressLabel = document.getElementById('addressProgressLabel');
        this.missingList = document.getElementById('addressMissingList');
        this.previewCard = document.getElementById('addressPreviewCard');

        this.config.ADDRESS_NUMERIC_FIELDS.forEach((fieldId) => {
            const field = document.getElementById(fieldId);
            bindOnce(field, 'keypress', this.numericGuard, `personaAddressNumeric_${fieldId}`);
        });

        DomFacade.findAll('.address-field').forEach((field) => {
            bindOnce(field, 'input', this.handleFieldChange, `personaAddressInput_${field.id}`);
            if (field && field.tagName === 'SELECT') {
                bindOnce(field, 'change', this.handleFieldChange, `personaAddressChange_${field.id}`);
            }
        });

        const openModal = () => {
            modalInstance.modal('show');
            toggleButton?.setAttribute('aria-expanded', 'true');
        };

        const closeModal = () => {
            modalInstance.modal('hide');
            toggleButton?.setAttribute('aria-expanded', 'false');
        };

        this.openModal = openModal;
        this.closeModal = closeModal;

        bindOnce(toggleButton, 'click', openModal, 'personaAddressToggle');
        bindOnce(this.direccionInput, 'focus', openModal, 'personaAddressFocus');
        bindOnce(this.direccionInput, 'click', openModal, 'personaAddressClick');

        bindOnce(this.saveButton, 'click', this.handleSave, 'personaAddressSave');
        bindOnce(cancelButton, 'click', this.handleCancel, 'personaAddressCancel');

        modalInstance.on('shown.bs.modal', () => {
            this.populateAddressFromInput();
            this.updatePreview();
            this.renderMissingList([]);
            this.toggleSaveButton();
            this.focusFirstField();
        });
        modalInstance.on('hidden.bs.modal', () => this.clearAddressFields());

        this.clearValidationState();
        this.updatePreview();
        this.toggleSaveButton();
    }

    handleSave(event) {
        if (event) {
            event.preventDefault();
        }

        const { address, missing, components } = this.composeAddress(true);
        if (missing.length) {
            this.showValidationError(missing);
            this.highlightMissingFields(missing);
            this.renderMissingList(missing);
            this.toggleSaveButton();
            return;
        }

        this.clearValidationState();
        this.renderMissingList([]);

        if (this.direccionInput) {
            this.direccionInput.value = address;
            this.direccionInput.dispatchEvent(new Event('input', { bubbles: true }));
            this.setAddressPayload(components);
        }

        this.clearAddressFields();
        this.closeModal?.();
    }

    handleCancel(event) {
        if (event) {
            event.preventDefault();
        }

        this.clearAddressFields();
        this.renderMissingList([]);
        this.closeModal?.();
    }

    handleFieldChange(event) {
        if (event && event.currentTarget) {
            this.clearFieldError(event.currentTarget);
        }

        this.showValidationError([]);
        this.renderMissingList([]);
        this.updatePreview();
        this.toggleSaveButton();
    }

    composeAddress(requireMandatory = false) {
        const values = {
            tipoVia: this.getValue('tipo_via'),
            numeroVia: this.getValue('numero_via'),
            letraVia: this.getValue('letra_via'),
            bisVia: this.getValue('bis_via'),
            cardinalVia: this.getValue('cardinal_via'),
            viaSecundariaTipo: this.getValue('via_secundaria'),
            numeroViaSecundaria: this.getValue('numero_via_secundaria'),
            letraViaSecundaria: this.getValue('letra_via_secundaria'),
            bisViaSecundaria: this.getValue('bis_via_secundaria'),
            cardinalViaSecundaria: this.getValue('cardinal_via_secundaria'),
            numeroCasa: this.getValue('numero_casa'),
            complementos: this.getValue('complementos'),
            barrio: this.getValue('barrio')
        };

        const requiredMap = [
            { id: 'tipo_via', value: values.tipoVia },
            { id: 'numero_via', value: values.numeroVia },
            { id: 'numero_casa', value: values.numeroCasa }
        ];

        const missing = requiredMap.filter((field) => !field.value).map((field) => field.id);

        const segments = [];

        const principalParts = [];
        if (values.tipoVia) {
            principalParts.push(values.tipoVia);
        }
        if (values.numeroVia) {
            principalParts.push(values.numeroVia);
        }
        if (values.letraVia) {
            principalParts.push(values.letraVia);
        }
        if (values.bisVia) {
            principalParts.push(values.bisVia);
        }
        if (values.cardinalVia) {
            principalParts.push(values.cardinalVia);
        }

        const principal = principalParts.join(' ').trim();
        if (principal) {
            segments.push(principal);
        }

        const secondaryParts = [];
        if (values.viaSecundariaTipo) {
            secondaryParts.push(values.viaSecundariaTipo);
        }
        if (values.numeroViaSecundaria) {
            secondaryParts.push(values.numeroViaSecundaria);
        }
        if (values.letraViaSecundaria) {
            secondaryParts.push(values.letraViaSecundaria);
        }
        if (values.bisViaSecundaria) {
            secondaryParts.push(values.bisViaSecundaria);
        }
        if (values.cardinalViaSecundaria) {
            secondaryParts.push(values.cardinalViaSecundaria);
        }

        const secondary = secondaryParts.join(' ').trim();
        if (secondary) {
            segments.push(secondary);
        }

        if (values.numeroCasa) {
            segments.push(`#${values.numeroCasa}`);
        }

        if (values.complementos) {
            segments.push(values.complementos);
        }

        let direccion = segments.join(' ').trim();

        if (values.barrio) {
            direccion = direccion ? `${direccion}, ${values.barrio}` : values.barrio;
        }

        if (requireMandatory && missing.length) {
            return { address: '', missing, components: values };
        }

        return { address: direccion, missing, components: values };
    }

    getRequiredFields() {
        return DomFacade.findAll('.address-field[data-required="true"]');
    }

    getFieldLabel(id) {
        const element = document.getElementById(id);

        if (!element) {
            return id;
        }

        if (element.dataset.label) {
            return element.dataset.label;
        }

        const label = element.closest('.form-group')?.querySelector('label');
        return label ? label.textContent.trim() : id;
    }

    showValidationError(missing) {
        if (!this.errorContainer) {
            return;
        }

        if (!missing.length) {
            this.errorContainer.classList.add('d-none');
            this.errorContainer.innerHTML = '';
            return;
        }

        const labels = missing.map((id) => this.getFieldLabel(id));
        this.errorContainer.innerHTML =
            `<strong>Completa los campos obligatorios:</strong> ${labels.join(', ')}`;
        this.errorContainer.classList.remove('d-none');
        this.renderMissingList(missing);
    }

    highlightMissingFields(missing) {
        const requiredFields = this.getRequiredFields();

        requiredFields.forEach((field) => {
            if (!field) {
                return;
            }

            if (missing.includes(field.id)) {
                field.classList.add('is-invalid');
                field.setAttribute('aria-invalid', 'true');
            } else {
                field.classList.remove('is-invalid');
                field.removeAttribute('aria-invalid');
            }
        });
    }

    renderMissingList(missing) {
        if (!this.missingList) {
            return;
        }

        if (!missing || !missing.length) {
            this.missingList.classList.add('d-none');
            this.missingList.innerHTML = '';
            return;
        }

        this.missingList.classList.remove('d-none');
        this.missingList.innerHTML = '';

        missing.forEach((id) => {
            const item = document.createElement('li');
            item.className = 'list-group-item d-flex align-items-center py-2';
            const icon = document.createElement('i');
            icon.className = 'fas fa-exclamation-circle text-warning mr-2';
            const text = document.createElement('span');
            text.textContent = this.getFieldLabel(id);
            item.appendChild(icon);
            item.appendChild(text);
            this.missingList.appendChild(item);
        });
    }

    clearValidationState() {
        this.highlightMissingFields([]);
        this.showValidationError([]);
        this.renderMissingList([]);
    }

    toggleSaveButton() {
        if (!this.saveButton) {
            return;
        }

        const { missing } = this.composeAddress(true);
        this.saveButton.disabled = missing.length > 0;
        this.updateProgress();
    }

    updatePreview() {
        if (!this.previewContainer) {
            return;
        }

        const { address } = this.composeAddress(false);
        const hasAddress = Boolean(address);
        const defaultMessage =
            'Completa los campos obligatorios para ver la dirección estructurada.';

        this.previewContainer.textContent = hasAddress ? address : defaultMessage;
        this.previewContainer.classList.toggle('text-primary', hasAddress);
        this.previewContainer.classList.toggle('text-muted', !hasAddress);

        if (this.previewCard) {
            this.previewCard.classList.toggle('shadow', hasAddress);
            this.previewCard.classList.toggle('shadow-sm', !hasAddress);
            this.previewCard.classList.toggle('border', hasAddress);
            this.previewCard.classList.toggle('border-primary', hasAddress);
            this.previewCard.classList.toggle('border-0', !hasAddress);
        }
    }

    updateProgress() {
        if (!this.progressBar || !this.progressLabel) {
            return;
        }

        const requiredFields = this.getRequiredFields();
        const total = requiredFields.length || 1;
        const completed = requiredFields.filter((field) => field && field.value.trim()).length;
        const percentage = Math.round((completed / total) * 100);

        this.progressBar.style.width = `${percentage}%`;
        this.progressBar.setAttribute('aria-valuenow', String(percentage));
        this.progressBar.classList.toggle('bg-success', percentage === 100);
        this.progressBar.classList.toggle('bg-primary', percentage !== 100);
        this.progressLabel.textContent = `${percentage}% completado (${completed}/${total})`;
    }

    focusFirstField() {
        const firstField = document.getElementById('tipo_via');

        if (firstField && typeof firstField.focus === 'function') {
            window.requestAnimationFrame(() => firstField.focus());
        }
    }

    clearFieldError(field) {
        if (!field) {
            return;
        }

        field.classList.remove('is-invalid');
        field.removeAttribute('aria-invalid');
    }

    clearAddressFields() {
        DomFacade.findAll('.address-field').forEach((field) => {
            if (field.tagName === 'SELECT') {
                field.selectedIndex = 0;
            } else {
                field.value = '';
            }

            this.clearFieldError(field);
        });

        this.clearValidationState();
        this.updatePreview();
        this.updateProgress();
        this.toggleSaveButton();
    }

    getValue(id) {
        const element = document.getElementById(id);
        return element ? element.value.trim() : '';
    }

    setValue(id, value) {
        const element = document.getElementById(id);
        if (!element) {
            return;
        }

        if (element.tagName === 'SELECT') {
            element.value = value || '';
            if (!element.value && value) {
                const option = Array.from(element.options).find((opt) =>
                    this.normalizeToken(opt.textContent) === this.normalizeToken(value)
                );
                if (option) {
                    element.value = option.value;
                }
            }
            element.dispatchEvent(new Event('change', { bubbles: true }));
            return;
        }

        element.value = value || '';
        element.dispatchEvent(new Event('input', { bubbles: true }));
    }

    setAddressPayload(components) {
        if (!this.direccionInput) {
            return;
        }

        try {
            this.direccionInput.dataset.addressPayload = JSON.stringify(components);
        } catch (error) {
            console.warn('No fue posible serializar la dirección estructurada.', error);
        }
    }

    getAddressPayload() {
        if (!this.direccionInput || !this.direccionInput.dataset.addressPayload) {
            return null;
        }

        try {
            return JSON.parse(this.direccionInput.dataset.addressPayload);
        } catch (error) {
            console.warn('Payload de dirección inválido; se ignorará.', error);
            return null;
        }
    }

    populateAddressFromInput() {
        const payload = this.getAddressPayload();
        if (payload) {
            this.populateFieldsFromComponents(payload);
            return;
        }

        const parsed = this.parseAddressString(this.direccionInput?.value || '');
        if (
            parsed &&
            Object.values(parsed).some((value) => {
                if (typeof value === 'string') {
                    return value.trim() !== '';
                }
                return Boolean(value);
            })
        ) {
            this.populateFieldsFromComponents(parsed);
            this.setAddressPayload(parsed);
        }
    }

    populateFieldsFromComponents(components) {
        if (!components) {
            return;
        }

        const mapping = {
            tipoVia: 'tipo_via',
            numeroVia: 'numero_via',
            letraVia: 'letra_via',
            bisVia: 'bis_via',
            cardinalVia: 'cardinal_via',
            viaSecundariaTipo: 'via_secundaria',
            numeroViaSecundaria: 'numero_via_secundaria',
            letraViaSecundaria: 'letra_via_secundaria',
            bisViaSecundaria: 'bis_via_secundaria',
            cardinalViaSecundaria: 'cardinal_via_secundaria',
            numeroCasa: 'numero_casa',
            complementos: 'complementos',
            barrio: 'barrio'
        };

        Object.entries(mapping).forEach(([componentKey, fieldId]) => {
            if (Object.prototype.hasOwnProperty.call(components, componentKey)) {
                this.setValue(fieldId, components[componentKey]);
            }
        });
    }

    parseAddressString(address) {
        if (!address || typeof address !== 'string') {
            return null;
        }

        const components = {
            tipoVia: '',
            numeroVia: '',
            letraVia: '',
            bisVia: '',
            cardinalVia: '',
            viaSecundariaTipo: '',
            numeroViaSecundaria: '',
            letraViaSecundaria: '',
            bisViaSecundaria: '',
            cardinalViaSecundaria: '',
            numeroCasa: '',
            complementos: '',
            barrio: ''
        };

        const [rawMain, rawBarrio] = address.split(',', 2);

        if (rawBarrio && rawBarrio.trim()) {
            components.barrio = rawBarrio.trim();
        }

        if (!rawMain) {
            return components;
        }

        const tokens = rawMain
            .trim()
            .split(/\s+/)
            .filter(Boolean);

        if (!tokens.length) {
            return components;
        }

        const hashIndex = tokens.findIndex((token) => this.isHouseNumberMarker(token));
        let mainTokens = tokens;

        if (hashIndex !== -1) {
            const { numeroCasa, remainingTokens } = this.extractHouseNumber(
                tokens[hashIndex],
                tokens.slice(hashIndex + 1)
            );
            components.numeroCasa = numeroCasa;
            const complementTokens = remainingTokens;
            if (complementTokens.length) {
                components.complementos = complementTokens.join(' ');
            }
            mainTokens = tokens.slice(0, hashIndex);
        }

        if (!mainTokens.length) {
            return components;
        }

        const viaLabels = this.getViaLabels();
        let secondaryIndex = -1;

        for (let i = 1; i < mainTokens.length; i++) {
            const normalized = this.normalizeToken(mainTokens[i]);
            if (viaLabels.includes(normalized)) {
                secondaryIndex = i;
                break;
            }
        }

        const principalTokens =
            secondaryIndex === -1 ? mainTokens : mainTokens.slice(0, secondaryIndex);
        const secondaryTokens =
            secondaryIndex === -1 ? [] : mainTokens.slice(secondaryIndex);

        Object.assign(components, this.parsePrincipalTokens(principalTokens));
        Object.assign(components, this.parseSecondaryTokens(secondaryTokens));

        return components;
    }

    parsePrincipalTokens(tokens) {
        const result = {
            tipoVia: '',
            numeroVia: '',
            letraVia: '',
            bisVia: '',
            cardinalVia: ''
        };

        if (!tokens.length) {
            return result;
        }

        result.tipoVia = tokens[0];

        const remaining = tokens.slice(1);
        const cardinalLabels = this.getCardinalLabels();

        for (const token of remaining) {
            const normalized = this.normalizeToken(token);

            if (!result.numeroVia && /\d/.test(token)) {
                result.numeroVia = token;
                continue;
            }

            if (!result.letraVia && /^[A-Z]$/.test(normalized)) {
                result.letraVia = normalized;
                continue;
            }

            if (!result.bisVia && normalized === 'BIS') {
                result.bisVia = 'BIS';
                continue;
            }

            if (!result.cardinalVia && cardinalLabels.includes(normalized)) {
                result.cardinalVia = token;
            }
        }

        return result;
    }

    parseSecondaryTokens(tokens) {
        const result = {
            viaSecundariaTipo: '',
            numeroViaSecundaria: '',
            letraViaSecundaria: '',
            bisViaSecundaria: '',
            cardinalViaSecundaria: ''
        };

        if (!tokens.length) {
            return result;
        }

        result.viaSecundariaTipo = tokens[0];

        const remaining = tokens.slice(1);
        const cardinalLabels = this.getCardinalLabels();

        for (const token of remaining) {
            const normalized = this.normalizeToken(token);

            if (!result.numeroViaSecundaria && /\d/.test(token)) {
                result.numeroViaSecundaria = token;
                continue;
            }

            if (!result.letraViaSecundaria && /^[A-Z]$/.test(normalized)) {
                result.letraViaSecundaria = normalized;
                continue;
            }

            if (!result.bisViaSecundaria && normalized === 'BIS') {
                result.bisViaSecundaria = 'BIS';
                continue;
            }

            if (!result.cardinalViaSecundaria && cardinalLabels.includes(normalized)) {
                result.cardinalViaSecundaria = token;
            }
        }

        return result;
    }

    getViaLabels() {
        if (this.optionCache.via) {
            return this.optionCache.via;
        }

        const select = document.getElementById('tipo_via');
        if (!select) {
            this.optionCache.via = [];
            return this.optionCache.via;
        }

        this.optionCache.via = Array.from(select.options)
            .map((option) => this.normalizeToken(option.value || option.textContent || ''))
            .filter(Boolean);

        return this.optionCache.via;
    }

    getCardinalLabels() {
        if (this.optionCache.cardinal) {
            return this.optionCache.cardinal;
        }

        const select = document.getElementById('cardinal_via');
        if (!select) {
            this.optionCache.cardinal = [];
            return this.optionCache.cardinal;
        }

        this.optionCache.cardinal = Array.from(select.options)
            .map((option) => this.normalizeToken(option.value || option.textContent || ''))
            .filter(Boolean);

        return this.optionCache.cardinal;
    }

    normalizeToken(token) {
        return (token || '').toString().trim().toUpperCase();
    }

    isHouseNumberMarker(token) {
        if (!token) {
            return false;
        }

        const normalized = this.normalizeToken(token);

        if (token.startsWith('#')) {
            return true;
        }

        return ['#', 'NO', 'NO.', 'N°', 'NUMERO', 'NUMERO.'].includes(normalized);
    }

    extractHouseNumber(markerToken, followingTokens) {
        let numeroCasa = '';
        const remainingTokens = [...followingTokens];

        const normalizedMarker = this.normalizeToken(markerToken);

        if (markerToken.startsWith('#') && markerToken.length > 1) {
            numeroCasa = markerToken.slice(1);
        } else if (normalizedMarker === '#') {
            if (remainingTokens.length) {
                numeroCasa = remainingTokens.shift();
            }
        } else if (['NUMERO', 'NUMERO.'].includes(normalizedMarker)) {
            if (remainingTokens.length) {
                numeroCasa = remainingTokens.shift();
            }
        } else {
            numeroCasa = markerToken.replace(/^#/, '');
        }

        return {
            numeroCasa: numeroCasa || '',
            remainingTokens
        };
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

        let sanitizedBase = fallbackBase;
        while (sanitizedBase.endsWith('/')) {
            sanitizedBase = sanitizedBase.slice(0, -1);
        }
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

