/**
 * Módulo para manejo de selects dinámicos (Países/Departamentos/Municipios)
 * Reutilizable en múltiples formularios
 */
import { AlertHandler } from './alert-handler.js';

export class SelectDinamicoHandler {
    constructor(options = {}) {
        this.options = {
            paisSelector: '#pais_id',
            departamentoSelector: '#departamento_id',
            municipioSelector: '#municipio_id',
            sedeSelector: '#sede_id',
            ambienteSelector: '#ambiente_id',
            programaSelector: '#programa_formacion_id',
            departamentosTemplate: null,
            municipiosTemplate: null,
            ...options
        };
        
        this.alertHandler = new AlertHandler({
            autoHide: true,
            hideDelay: 5000,
            alertSelector: '.alert'
        });
        
        this.init();
    }

    init() {
        const departamentoSelect = $(this.options.departamentoSelector);
        const municipioSelect = $(this.options.municipioSelector);

        this.options.departamentosTemplate = departamentoSelect.data('url-template') || this.options.departamentosTemplate || '/departamentos/__ID__';
        this.options.municipiosTemplate = municipioSelect.data('url-template') || this.options.municipiosTemplate || '/municipios/__ID__';

        this.initPaisChange();
        this.initDepartamentoChange();
        this.initProgramaChange();
        this.initSedeChange();
        this.loadInitialValues();
    }

    /**
     * Manejar cambio de país
     */
    initPaisChange() {
        $(this.options.paisSelector).on('change', (e) => {
            const paisId = $(e.target).val();
            this.loadDepartamentos(paisId);
        });
    }

    /**
     * Manejar cambio de departamento
     */
    initDepartamentoChange() {
        $(this.options.departamentoSelector).on('change', (e) => {
            const departamentoId = $(e.target).val();
            this.loadMunicipios(departamentoId);
        });
    }

    /**
     * Manejar cambio de programa de formación
     */
    initProgramaChange() {
        $(this.options.programaSelector).on('change', (e) => {
            const programaId = $(e.target).val();
            if (programaId) {
                const sedeId = $(e.target).find('option:selected').data('sede');
                if (sedeId) {
                    $(this.options.sedeSelector).val(sedeId);
                    this.loadAmbientesPorSede(sedeId);
                }
            }
        });
    }

    /**
     * Manejar cambio de sede
     */
    initSedeChange() {
        $(this.options.sedeSelector).on('change', (e) => {
            const sedeId = $(e.target).val();
            this.loadAmbientesPorSede(sedeId);
        });
    }

    /**
     * Cargar departamentos por país
     */
    loadDepartamentos(paisId) {
        if (!paisId) {
            this.clearSelect(this.options.departamentoSelector, 'Seleccione un departamento...');
            this.clearSelect(this.options.municipioSelector, 'Primero seleccione un departamento...');
            return Promise.resolve();
        }

        const departamentoSelect = $(this.options.departamentoSelector);
        this.setLoadingState(departamentoSelect, 'Cargando departamentos...');

        return new Promise((resolve, reject) => {
            $.ajax({
                url: this.buildUrl(this.options.departamentosTemplate, paisId),
                method: 'GET',
                success: (response) => {
                    if (response.success && response.data) {
                        this.populateSelect(departamentoSelect, response.data, 'Seleccione un departamento...');
                        this.clearSelect(this.options.municipioSelector, 'Primero seleccione un departamento...');
                        resolve(response);
                    } else {
                        this.setErrorState(departamentoSelect, 'Error al cargar departamentos');
                        reject(new Error('Respuesta inválida'));
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Error al cargar departamentos:', error);
                    this.setErrorState(departamentoSelect, 'Error al cargar departamentos');
                    this.alertHandler.showError('Error al cargar los departamentos. Intente nuevamente.');
                    reject(error);
                }
            });
        });
    }

    /**
     * Cargar municipios por departamento
     */
    loadMunicipios(departamentoId) {
        if (!departamentoId) {
            this.clearSelect(this.options.municipioSelector, 'Seleccione un municipio...');
            return Promise.resolve();
        }

        const municipioSelect = $(this.options.municipioSelector);
        this.setLoadingState(municipioSelect, 'Cargando municipios...');

        return new Promise((resolve, reject) => {
            $.ajax({
                url: this.buildUrl(this.options.municipiosTemplate, departamentoId),
                method: 'GET',
                success: (response) => {
                    if (response.success && response.data) {
                        this.populateSelect(municipioSelect, response.data, 'Seleccione un municipio...');
                        resolve(response);
                    } else {
                        this.setErrorState(municipioSelect, 'Error al cargar municipios');
                        reject(new Error('Respuesta inválida'));
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Error al cargar municipios:', error);
                    this.setErrorState(municipioSelect, 'Error al cargar municipios');
                    this.alertHandler.showError('Error al cargar los municipios. Intente nuevamente.');
                    reject(error);
                }
            });
        });
    }

    /**
     * Cargar ambientes por sede
     */
    loadAmbientesPorSede(sedeId) {
        if (!sedeId) {
            this.clearSelect(this.options.ambienteSelector, 'Seleccione un ambiente...');
            return;
        }

        const ambienteSelect = $(this.options.ambienteSelector);
        this.setLoadingState(ambienteSelect, 'Cargando ambientes...');

        $.ajax({
            url: `/ficha/ambientes-por-sede/${sedeId}`,
            method: 'GET',
            success: (response) => {
                if (response.success && response.data) {
                    this.populateSelect(ambienteSelect, response.data, 'Seleccione un ambiente...', (ambiente) => ({
                        value: ambiente.id,
                        text: `${ambiente.title} - ${ambiente.descripcion}`
                    }));
                } else {
                    this.setErrorState(ambienteSelect, 'Error al cargar ambientes');
                }
            },
            error: (xhr, status, error) => {
                console.error('Error al cargar ambientes:', error);
                this.setErrorState(ambienteSelect, 'Error al cargar ambientes');
                this.alertHandler.showError('Error al cargar los ambientes. Intente nuevamente.');
            }
        });
    }

    /**
     * Poblar select con datos
     */
    populateSelect(select, data, placeholder, formatter = null) {
        select.html(`<option value="">${placeholder}</option>`);
        
        data.forEach(item => {
            let option;
            if (formatter) {
                const formatted = formatter(item);
                option = new Option(formatted.text, formatted.value);
            } else {
                option = new Option(item.nombre || item.name, item.id);
            }
            select.append(option);
        });
        
        select.prop('disabled', false);
    }

    /**
     * Limpiar select
     */
    clearSelect(selector, placeholder) {
        const select = $(selector);
        select.html(`<option value="">${placeholder}</option>`);
        select.prop('disabled', true);
    }

    /**
     * Establecer estado de carga
     */
    setLoadingState(select, message) {
        select.html(`<option value="">${message}</option>`);
        select.prop('disabled', true);
    }

    /**
     * Establecer estado de error
     */
    setErrorState(select, message) {
        select.html(`<option value="">${message}</option>`);
        select.prop('disabled', true);
    }

    /**
     * Cargar valores iniciales desde la URL o datos existentes
     */
    loadInitialValues() {
        // Cargar valores desde atributos data si existen
        const paisSelect = $(this.options.paisSelector);
        const departamentoSelect = $(this.options.departamentoSelector);
        const municipioSelect = $(this.options.municipioSelector);

        const paisId = paisSelect.data('initial-value');
        const departamentoId = departamentoSelect.data('initial-value');
        const municipioId = municipioSelect.data('initial-value');

        if (departamentoSelect.length) {
            this.options.departamentosTemplate = departamentoSelect.data('url-template') || this.options.departamentosTemplate;
        }
        if (municipioSelect.length) {
            this.options.municipiosTemplate = municipioSelect.data('url-template') || this.options.municipiosTemplate;
        }

        if (paisId) {
            paisSelect.val(paisId);
            this.loadDepartamentos(paisId).then(() => {
                if (departamentoId) {
                    departamentoSelect.val(departamentoId);
                    this.loadMunicipios(departamentoId).then(() => {
                        if (municipioId) {
                            municipioSelect.val(municipioId);
                        }
                    });
                }
            });
        }
    }

    /**
     * Resetear todos los selects
     */
    reset() {
        this.clearSelect(this.options.paisSelector, 'Seleccione un país...');
        this.clearSelect(this.options.departamentoSelector, 'Primero seleccione un país...');
        this.clearSelect(this.options.municipioSelector, 'Primero seleccione un departamento...');
        this.clearSelect(this.options.ambienteSelector, 'Primero seleccione una sede...');
    }

    /**
     * Construir URL desde plantilla y valor
     */
    buildUrl(template, value) {
        if (!template) {
            throw new Error('No se definió plantilla de URL para select dinámico');
        }
        return template.replace(/__ID__/g, value);
    }
}
