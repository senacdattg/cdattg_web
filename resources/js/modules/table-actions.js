/**
 * Módulo para manejar acciones de tabla (eliminar, tooltips, etc.)
 */
export class TableActionsHandler {
    constructor(tableSelector = 'body', options = {}) {
        this.table = $(tableSelector);
        this.options = {
            deleteSelector: '.formulario-eliminar',
            tooltipSelector: '[data-toggle="tooltip"]',
            alertSelector: '.alert',
            autoHideAlerts: true,
            alertHideDelay: 5000,
            ...options
        };
        
        this.init();
    }
    
    init() {
        this.initDeleteConfirmation();
        this.initTooltips();
        this.initAutoHideAlerts();
    }
    
    /**
     * Inicializa la confirmación de eliminación
     */
    initDeleteConfirmation() {
        this.table.on('submit', this.options.deleteSelector, (e) => {
            e.preventDefault();
            const form = e.target;
            const row = $(form).closest('tr');
            
            // Extraer información de la entidad desde la fila
            const entityInfo = this.extractEntityInfo(row);
            
            this.showDeleteConfirmation(entityInfo, () => {
                this.showLoading();
                form.submit();
            });
        });
    }
    
    /**
     * Extrae información de la entidad desde la fila de la tabla
     */
    extractEntityInfo(row) {
        const cells = row.find('td');
        const info = {
            name: cells.eq(1).text().trim(), // Segunda columna generalmente es el nombre
            document: cells.eq(2).text().trim(), // Tercera columna generalmente es documento
            additional: {}
        };
        
        // Extraer información adicional si existe
        if (cells.length > 3) {
            cells.each((index, cell) => {
                const $cell = $(cell);
                const text = $cell.text().trim();
                if (text && index > 2) {
                    info.additional[`field_${index}`] = text;
                }
            });
        }
        
        return info;
    }
    
    /**
     * Muestra la confirmación de eliminación
     */
    showDeleteConfirmation(entityInfo, callback) {
        const { name, document, additional } = entityInfo;
        
        let htmlContent = `
            <div class="text-left">
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle"></i>
                    <strong>Información del Registro:</strong>
                </div>
                <p><strong>Nombre:</strong> ${name}</p>
        `;
        
        if (document && document !== 'N/A') {
            htmlContent += `<p><strong>Documento:</strong> ${document}</p>`;
        }
        
        // Agregar información adicional si existe
        Object.entries(additional).forEach(([key, value]) => {
            if (value && value !== 'N/A') {
                htmlContent += `<p><strong>${key.replace('field_', 'Campo ')}:</strong> ${value}</p>`;
            }
        });
        
        htmlContent += `
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Importante:</strong> Esta acción no se puede deshacer.
                </div>
            </div>
        `;
        
        Swal.fire({
            title: '⚠️ Confirmar Eliminación',
            html: htmlContent,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            focusConfirm: false,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    }
    
    /**
     * Muestra el indicador de carga
     */
    showLoading() {
        Swal.fire({
            title: 'Procesando...',
            text: 'Por favor espere mientras se procesa la solicitud',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
    }
    
    /**
     * Inicializa los tooltips
     */
    initTooltips() {
        this.table.find(this.options.tooltipSelector).tooltip();
    }
    
    /**
     * Inicializa el auto-ocultamiento de alertas
     */
    initAutoHideAlerts() {
        if (this.options.autoHideAlerts) {
            setTimeout(() => {
                this.table.find(this.options.alertSelector).fadeOut('slow');
            }, this.options.alertHideDelay);
        }
    }
    
    /**
     * Método para eliminar confirmación personalizada
     */
    showCustomDeleteConfirmation(title, message, callback) {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    }
}
