/**
 * Gestión de Aspirantes - Programa Complementario
 * Maneja todas las funcionalidades de la vista de aspirantes por programa
 */

class AspirantesPrograma {
    constructor(config) {
        this.config = config;
        this.progressInterval = null;
        this.currentProgressId = null;
        this.personaEncontrada = null;
        this.table = null;
        
        this.init();
    }

    init() {
        console.log('Módulo de gestión de aspirantes cargado');

        $(document).ready(() => {
            // Inicializar DataTables si hay datos
            if (this.config.hasAspirantes) {
                this.initDataTable();
            }

            // Verificar progreso existente
            if (this.config.existingProgressId) {
                console.log('Progreso existente encontrado, iniciando monitoreo...');
                this.currentProgressId = this.config.existingProgressId;
                this.startProgressMonitoring(this.currentProgressId);
                this.updateUIForValidationInProgress();
            }

            // Configurar modal de agregar aprendiz
            this.setupModalHandlers();
            
            // Configurar botones de validación
            this.setupValidationButtons();
            
            // Configurar botones de acción de aspirantes
            this.setupAspiranteActionButtons();
        });
    }

    /**
     * Inicializar DataTable
     */
    initDataTable() {
        this.table = $('#aspirantes-table').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json',
                zeroRecords: 'No se encontraron aspirantes que coincidan con los filtros',
                emptyTable: 'No hay aspirantes registrados'
            },
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
            order: [[3, 'desc']], // Ordenar por fecha de solicitud
            dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
            responsive: true,
            columnDefs: [
                { orderable: false, targets: [7] } // Deshabilitar ordenamiento en columna de acciones
            ]
        });

        // Búsqueda personalizada
        $('#buscar-aspirante').on('keyup', () => {
            this.handleSearch();
        });

        // Filtro por estado
        $('#filtro-estado').on('change', () => {
            this.handleFilterByEstado();
        });

        // Filtro por documento
        $('#filtro-documento').on('change', () => {
            this.handleFilterByDocumento();
        });

        // Limpiar filtros
        $('#limpiar-filtros').on('click', () => {
            this.limpiarFiltros();
        });
    }

    /**
     * Manejar búsqueda personalizada
     */
    handleSearch() {
        const searchTerm = $('#buscar-aspirante').val().toLowerCase();
        this.table.column(1).search(searchTerm).draw();
        
        // También buscar en documento y email
        this.table.rows().nodes().each((node) => {
            const $row = $(node);
            const nombre = $row.data('nombre') || '';
            const documento = $row.data('documento-numero') || '';
            const email = $row.data('email') || '';
            const match = nombre.includes(searchTerm) || 
                         documento.includes(searchTerm) || 
                         email.includes(searchTerm);
            $row.toggle(match || searchTerm === '');
        });
        this.actualizarContador();
    }

    /**
     * Manejar filtro por estado
     */
    handleFilterByEstado() {
        const estado = $('#filtro-estado').val();
        this.table.rows().nodes().to$().each(function() {
            const $row = $(this);
            if (!estado || $row.data('estado') == estado) {
                $row.show();
            } else {
                $row.hide();
            }
        });
        this.table.draw();
        this.actualizarContador();
    }

    /**
     * Manejar filtro por documento
     */
    handleFilterByDocumento() {
        const tieneDocumento = $('#filtro-documento').val();
        this.table.rows().nodes().to$().each(function() {
            const $row = $(this);
            if (!tieneDocumento || $row.data('documento') == tieneDocumento) {
                $row.show();
            } else {
                $row.hide();
            }
        });
        this.table.draw();
        this.actualizarContador();
    }

    /**
     * Limpiar todos los filtros
     */
    limpiarFiltros() {
        $('#buscar-aspirante').val('');
        $('#filtro-estado').val('');
        $('#filtro-documento').val('');
        this.table.search('').columns().search('').draw();
        this.table.rows().nodes().to$().show();
        this.table.draw();
        this.actualizarContador();
    }

    /**
     * Actualizar contador de aspirantes visibles
     */
    actualizarContador() {
        if (this.table) {
            const visibleRows = this.table.rows({ filter: 'applied' }).count();
            $('#contador-aspirantes').text(visibleRows);
        }
    }

    /**
     * Configurar handlers del modal de agregar aprendiz
     */
    setupModalHandlers() {
        // Configurar formulario de búsqueda
        const formBuscar = document.getElementById('formBuscarAprendiz');
        if (formBuscar) {
            formBuscar.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.buscarPersona();
            });
        }

        // Configurar botón de agregar persona encontrada
        const btnAgregarEncontrada = document.getElementById('btnAgregarPersonaEncontrada');
        if (btnAgregarEncontrada) {
            btnAgregarEncontrada.addEventListener('click', async () => {
                if (this.personaEncontrada) {
                    await this.agregarPersonaEncontrada(this.personaEncontrada.numero_documento);
                }
            });
        }

        // Configurar botón de nueva búsqueda
        const btnNuevaBusqueda = document.getElementById('btnNuevaBusqueda');
        if (btnNuevaBusqueda) {
            btnNuevaBusqueda.addEventListener('click', () => {
                this.resetearModalBusqueda();
            });
        }

        // Resetear modal cuando se cierra
        $('#modalAgregarAprendiz').on('hidden.bs.modal', () => {
            this.resetearModalBusqueda();
        });
    }

    /**
     * Buscar persona por número de documento
     */
    async buscarPersona() {
        const numeroDocumento = document.getElementById('numero_documento_buscar').value.trim();
        
        if (!numeroDocumento) {
            this.mostrarError('Por favor ingrese un número de documento.');
            return;
        }

        this.mostrarLoading(true);
        this.ocultarSecciones();

        try {
            const response = await fetch(this.config.routes.buscarPersona, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({
                    numero_documento: numeroDocumento
                })
            });

            const data = await response.json();
            this.mostrarLoading(false);

            if (data.success && data.found) {
                this.personaEncontrada = data.persona;
                this.mostrarInformacionPersona(data.persona);
                this.verificarInscripcionExistente(data.persona.numero_documento);
            } else {
                // Persona no encontrada - redirigir al formulario
                window.location.href = `${this.config.routes.create}?numero_documento=${encodeURIComponent(numeroDocumento)}`;
            }
        } catch (error) {
            console.error('Error:', error);
            this.mostrarLoading(false);
            this.mostrarError('Error de conexión. Por favor, intente nuevamente.');
        }
    }

    /**
     * Mostrar información de la persona encontrada
     */
    mostrarInformacionPersona(persona) {
        document.getElementById('persona-nombre').textContent = persona.nombre_completo || 'No disponible';
        document.getElementById('persona-documento').textContent = persona.numero_documento || 'No disponible';
        document.getElementById('persona-email').textContent = persona.email || 'No registrado';
        document.getElementById('persona-telefono').textContent = persona.telefono || 'No registrado';

        document.getElementById('busqueda-section').classList.add('d-none');
        document.getElementById('persona-info-section').classList.remove('d-none');
        document.getElementById('btnAgregarPersonaEncontrada').classList.remove('d-none');
        document.getElementById('btnNuevaBusqueda').classList.remove('d-none');
        document.getElementById('btnCancelar').classList.add('d-none');
    }

    /**
     * Agregar persona encontrada como aspirante
     */
    async agregarPersonaEncontrada(numeroDocumento) {
        const btnAgregar = document.getElementById('btnAgregarPersonaEncontrada');
        const originalText = btnAgregar.innerHTML;
        btnAgregar.disabled = true;
        btnAgregar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Agregando...';

        try {
            const response = await fetch(this.config.routes.agregarExistente, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({
                    numero_documento: numeroDocumento
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert('success', data.message);
                $('#modalAgregarAprendiz').modal('hide');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                this.mostrarError(data.message || 'Error al agregar el aspirante.');
                btnAgregar.disabled = false;
                btnAgregar.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            this.mostrarError('Error de conexión. Por favor, intente nuevamente.');
            btnAgregar.disabled = false;
            btnAgregar.innerHTML = originalText;
        }
    }

    /**
     * Verificar si ya está inscrita (placeholder)
     */
    verificarInscripcionExistente(numeroDocumento) {
        // Esta verificación se puede hacer en el servidor cuando se intente agregar
    }

    /**
     * Resetear modal de búsqueda
     */
    resetearModalBusqueda() {
        this.personaEncontrada = null;
        document.getElementById('numero_documento_buscar').value = '';
        document.getElementById('busqueda-section').classList.remove('d-none');
        document.getElementById('persona-info-section').classList.add('d-none');
        document.getElementById('error-section').classList.add('d-none');
        document.getElementById('btnAgregarPersonaEncontrada').classList.add('d-none');
        document.getElementById('btnNuevaBusqueda').classList.add('d-none');
        document.getElementById('btnCancelar').classList.remove('d-none');
        this.mostrarLoading(false);
    }

    /**
     * Mostrar/ocultar loading
     */
    mostrarLoading(show) {
        const loading = document.getElementById('loading-busqueda');
        if (loading) {
            if (show) {
                loading.classList.remove('d-none');
                document.getElementById('busqueda-section').querySelector('form').style.display = 'none';
            } else {
                loading.classList.add('d-none');
                document.getElementById('busqueda-section').querySelector('form').style.display = 'block';
            }
        }
    }

    /**
     * Mostrar error en el modal
     */
    mostrarError(mensaje) {
        document.getElementById('error-message').textContent = mensaje;
        document.getElementById('error-section').classList.remove('d-none');
    }

    /**
     * Ocultar secciones del modal
     */
    ocultarSecciones() {
        document.getElementById('persona-info-section').classList.add('d-none');
        document.getElementById('error-section').classList.add('d-none');
    }

    /**
     * Configurar botones de validación
     */
    setupValidationButtons() {
        // Botón de validación de documentos
        document.getElementById('btn-validar-documento')?.addEventListener('click', async () => {
            await this.validarDocumentos();
        });

        // Botón de validación SenaSofiaPlus
        document.getElementById('btn-validar-sofia')?.addEventListener('click', async () => {
            await this.validarSofiaPlus();
        });
    }

    /**
     * Validar documentos en Google Drive
     */
    async validarDocumentos() {
        const button = document.getElementById('btn-validar-documento');
        const originalText = button.innerHTML;

        if (!confirm('¿Está seguro de que desea validar los documentos de todos los aspirantes en Google Drive?')) {
            return;
        }

        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Validando...';

        try {
            const response = await fetch(this.config.routes.validarDocumento, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert('success', data.message);
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                this.showAlert('error', data.message || 'Error durante la validación');
                button.disabled = false;
                button.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('error', 'Error de conexión. Intente nuevamente.');
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }

    /**
     * Validar en SenaSofiaPlus
     */
    async validarSofiaPlus() {
        const button = document.getElementById('btn-validar-sofia');
        const originalText = button.innerHTML;

        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Iniciando validación...';

        try {
            const response = await fetch(this.config.routes.validarSofia, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                }
            });

            const data = await response.json();

            if (data.success) {
                this.currentProgressId = data.progress_id;
                this.showAlert('success', data.message);
                this.startProgressMonitoring(this.currentProgressId);
                button.innerHTML = '<i class="fas fa-clock me-1"></i>Procesando...';
                this.updateUIForValidationInProgress();
            } else {
                this.showAlert('error', data.message || 'Error durante la validación');
                button.disabled = false;
                button.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('error', 'Error de conexión. Intente nuevamente.');
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }

    /**
     * Actualizar UI durante validación en progreso
     */
    updateUIForValidationInProgress() {
        const validationButton = document.getElementById('btn-validar-sofia');
        if (validationButton) {
            validationButton.disabled = true;
            validationButton.innerHTML = '<i class="fas fa-clock me-1"></i>Procesando...';
        }

        const documentoButton = document.getElementById('btn-validar-documento');
        if (documentoButton) {
            documentoButton.disabled = true;
            documentoButton.innerHTML = '<i class="fas fa-clock me-1"></i>Procesando...';
        }

        const agregarAprendizButton = document.getElementById('btn-agregar-aprendiz');
        if (agregarAprendizButton) {
            agregarAprendizButton.disabled = true;
        }
    }

    /**
     * Monitorear progreso de validación
     */
    startProgressMonitoring(progressId) {
        this.progressInterval = setInterval(async () => {
            try {
                const response = await fetch(`/sofia-validation-progress/${progressId}`);
                const data = await response.json();

                if (data.success) {
                    const progress = data.progress;
                    this.updateProgressDisplay(progress);

                    if (progress.status === 'completed' || progress.status === 'failed') {
                        clearInterval(this.progressInterval);
                        this.handleValidationComplete(progress);
                    }
                }
            } catch (error) {
                console.error('Error monitoreando progreso:', error);
            }
        }, 3000);
    }

    /**
     * Actualizar visualización del progreso
     */
    updateProgressDisplay(progress) {
        let progressContainer = document.getElementById('sofia-progress-container');
        if (!progressContainer) {
            progressContainer = document.createElement('div');
            progressContainer.id = 'sofia-progress-container';
            progressContainer.className = 'mt-3';
            const cardBody = document.querySelector('.card-body');
            if (cardBody) {
                cardBody.insertBefore(progressContainer, cardBody.firstChild);
            }
        }

        const remaining = progress.total_aspirantes - progress.processed_aspirantes;
        const successRate = progress.processed_aspirantes > 0 ?
            Math.round((progress.successful_validations / progress.processed_aspirantes) * 100) : 0;
        const estimatedTimeRemaining = this.calculateEstimatedTime(progress);
        const progressBarClass = progress.status === 'failed' ? 'bg-danger' :
                               progress.status === 'completed' ? 'bg-success' : 'bg-info';

        progressContainer.innerHTML = `
            <div class="card border-info shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">
                            <i class="fas fa-cog ${progress.status === 'processing' ? 'fa-spin' : ''} me-2"></i>
                            Validación SenaSofiaPlus - ${progress.status_label}
                        </h6>
                        <small class="text-muted">${progress.progress_percentage}%</small>
                    </div>
                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar ${progressBarClass} progress-bar-striped ${progress.status === 'processing' ? 'progress-bar-animated' : ''}" 
                             role="progressbar"
                             style="width: ${progress.progress_percentage}%"
                             aria-valuenow="${progress.progress_percentage}"
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    <div class="row text-center mb-2">
                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block">Total</small>
                            <strong>${progress.total_aspirantes}</strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <small class="text-success d-block">Exitosos</small>
                            <strong class="text-success">${progress.successful_validations}</strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <small class="text-danger d-block">Errores</small>
                            <strong class="text-danger">${progress.failed_validations}</strong>
                        </div>
                        <div class="col-6 col-md-3">
                            <small class="text-info d-block">Pendientes</small>
                            <strong class="text-info">${remaining}</strong>
                        </div>
                    </div>
                    ${progress.status === 'processing' ? `
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-chart-line me-1"></i>
                                    Tasa de éxito: ${successRate}%
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    ${estimatedTimeRemaining}
                                </small>
                            </div>
                        </div>
                    ` : ''}
                    ${progress.started_at ? `
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                Iniciado: ${new Date(progress.started_at).toLocaleString('es-CO')}
                            </small>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    /**
     * Calcular tiempo estimado restante
     */
    calculateEstimatedTime(progress) {
        if (progress.status !== 'processing' || progress.processed_aspirantes === 0) {
            return 'Calculando...';
        }

        const elapsed = (new Date() - new Date(progress.started_at)) / 1000;
        const avgTimePerAspirante = elapsed / progress.processed_aspirantes;
        const remaining = progress.total_aspirantes - progress.processed_aspirantes;
        const estimatedSeconds = avgTimePerAspirante * remaining;

        if (estimatedSeconds < 60) {
            return `~${Math.round(estimatedSeconds)}s restantes`;
        } else if (estimatedSeconds < 3600) {
            return `~${Math.round(estimatedSeconds / 60)}min restantes`;
        } else {
            return `~${Math.round(estimatedSeconds / 3600)}h restantes`;
        }
    }

    /**
     * Manejar cuando la validación se completa
     */
    handleValidationComplete(progress) {
        const button = document.getElementById('btn-validar-sofia');
        const progressContainer = document.getElementById('sofia-progress-container');

        if (progress.status === 'completed') {
            const successRate = progress.total_aspirantes > 0 ?
                Math.round((progress.successful_validations / progress.total_aspirantes) * 100) : 0;

            this.showAlert('success',
                `Validación completada. ${progress.successful_validations}/${progress.total_aspirantes}
                    aspirantes validados exitosamente (${successRate}%).`
            );

            button.innerHTML = '<i class="fas fa-check-circle me-1"></i>Completado';
            button.className = 'btn btn-success btn-sm';

            setTimeout(() => {
                location.reload();
            }, 3000);
        } else if (progress.status === 'failed') {
            let errorMessage = 'La validación falló. ';
            if (progress.errors && progress.errors.length > 0) {
                errorMessage += `Errores encontrados: ${progress.errors.length}. `;
            }
            this.showAlert('error', errorMessage);

            button.disabled = false;
            button.innerHTML = '<i class="fas fa-search me-1"></i>Validar SenaSofiaPlus';
            this.restoreUIAfterValidation();
        }

        if (progressContainer) {
            setTimeout(() => {
                progressContainer.remove();
            }, 5000);
        }
    }

    /**
     * Restaurar UI después de validación
     */
    restoreUIAfterValidation() {
        const validationButton = document.getElementById('btn-validar-sofia');
        if (validationButton) {
            validationButton.disabled = false;
            validationButton.innerHTML = '<i class="fas fa-search me-1"></i>Validar SenaSofiaPlus';
        }

        const documentoButton = document.getElementById('btn-validar-documento');
        if (documentoButton) {
            documentoButton.disabled = false;
            documentoButton.innerHTML = '<i class="fas fa-file-pdf me-1"></i>Validar Documentos';
        }

        const agregarAprendizButton = document.getElementById('btn-agregar-aprendiz');
        if (agregarAprendizButton) {
            agregarAprendizButton.disabled = false;
        }
    }

    /**
     * Configurar botones de acción de aspirantes
     */
    setupAspiranteActionButtons() {
        document.addEventListener('click', (e) => {
            const button = e.target.closest('.aspirante-action-btn');
            if (button && button.dataset.aspiranteId) {
                e.preventDefault();
                this.handleRechazarAspirante(button);
            }
        });
    }

    /**
     * Manejar rechazo de aspirante
     */
    async handleRechazarAspirante(button) {
        const aspiranteId = button.dataset.aspiranteId;
        const aspiranteNombre = button.dataset.aspiranteNombre || 'este aspirante';

        if (!confirm(`¿Está seguro de que desea rechazar a ${aspiranteNombre} del programa? El aspirante será marcado como rechazado.`)) {
            return;
        }

        const originalHTML = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const destroyUrl = this.config.routes.destroy.replace('__ID__', aspiranteId);
            const response = await fetch(destroyUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert('success', data.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                this.showAlert('error', data.message);
                button.disabled = false;
                button.innerHTML = originalHTML;
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('error', 'Error de conexión. Intente nuevamente.');
            button.disabled = false;
            button.innerHTML = originalHTML;
        }
    }

    /**
     * Mostrar alerta
     */
    showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'}
            alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;

        document.body.appendChild(alertDiv);

        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    /**
     * Obtener token CSRF
     */
    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }
}

// Exportar para uso global
if (typeof window !== 'undefined') {
    window.AspirantesPrograma = AspirantesPrograma;
    
    // Auto-inicializar cuando la configuración esté disponible
    function initAspirantes() {
        if (window.aspirantesConfig && typeof jQuery !== 'undefined') {
            window.aspirantesPrograma = new AspirantesPrograma(window.aspirantesConfig);
        } else {
            // Reintentar si jQuery o la configuración aún no están disponibles
            setTimeout(initAspirantes, 100);
        }
    }
    
    // Intentar inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAspirantes);
    } else {
        // DOM ya está listo
        initAspirantes();
    }
}

export default AspirantesPrograma;

