/**
 * Módulo para manejar el historial de asignaciones
 * Maneja la visualización de detalles de errores y logs
 */
export class HistorialAsignacionesHandler {
    constructor(options = {}) {
        this.options = {
            errorDetailsSelector: '[id^="detalles-error-"]',
            toggleButtonSelector: '[onclick^="toggleDetallesError"]',
            ...options
        };
        this.init();
    }

    init() {
        this.bindEvents();
        this.makeToggleFunctionGlobal();
    }

    bindEvents() {
        // Agregar event listeners a los botones de toggle
        document.addEventListener('click', (e) => {
            if (e.target.matches('[onclick*="toggleDetallesError"]')) {
                e.preventDefault();
                const logId = this.extractLogIdFromOnclick(e.target.getAttribute('onclick'));
                if (logId) {
                    this.toggleDetallesError(logId);
                }
            }
        });
    }

    makeToggleFunctionGlobal() {
        // Hacer la función disponible globalmente para compatibilidad
        window.toggleDetallesError = (logId) => this.toggleDetallesError(logId);
    }

    extractLogIdFromOnclick(onclickAttribute) {
        const match = onclickAttribute.match(/toggleDetallesError\(['"]([^'"]+)['"]\)/);
        return match ? match[1] : null;
    }

    toggleDetallesError(logId) {
        const detalles = document.getElementById(`detalles-error-${logId}`);
        if (detalles) {
            const isHidden = detalles.style.display === 'none' || detalles.style.display === '';
            
            if (isHidden) {
                detalles.style.display = 'block';
                this.addAnimation(detalles, 'fadeIn');
            } else {
                this.addAnimation(detalles, 'fadeOut', () => {
                    detalles.style.display = 'none';
                });
            }
        }
    }

    addAnimation(element, animationType, callback = null) {
        element.classList.add(`animate-${animationType}`);
        
        setTimeout(() => {
            element.classList.remove(`animate-${animationType}`);
            if (callback) {
                callback();
            }
        }, 300);
    }

    // Método para expandir todos los detalles
    expandAllDetails() {
        const allDetails = document.querySelectorAll(this.options.errorDetailsSelector);
        allDetails.forEach(details => {
            details.style.display = 'block';
            this.addAnimation(details, 'fadeIn');
        });
    }

    // Método para colapsar todos los detalles
    collapseAllDetails() {
        const allDetails = document.querySelectorAll(this.options.errorDetailsSelector);
        allDetails.forEach(details => {
            this.addAnimation(details, 'fadeOut', () => {
                details.style.display = 'none';
            });
        });
    }

    // Método para filtrar logs por tipo
    filterLogsByType(type) {
        const allLogs = document.querySelectorAll('.log-entry');
        
        allLogs.forEach(log => {
            const logType = log.dataset.logType;
            if (type === 'all' || logType === type) {
                log.style.display = 'block';
            } else {
                log.style.display = 'none';
            }
        });
    }

    // Método para buscar en logs
    searchInLogs(searchTerm) {
        const allLogs = document.querySelectorAll('.log-entry');
        const term = searchTerm.toLowerCase();
        
        allLogs.forEach(log => {
            const logText = log.textContent.toLowerCase();
            if (logText.includes(term)) {
                log.style.display = 'block';
                this.highlightSearchTerm(log, searchTerm);
            } else {
                log.style.display = 'none';
                this.removeHighlight(log);
            }
        });
    }

    highlightSearchTerm(element, term) {
        const walker = document.createTreeWalker(
            element,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );

        const textNodes = [];
        let node;
        while (node = walker.nextNode()) {
            textNodes.push(node);
        }

        textNodes.forEach(textNode => {
            const text = textNode.textContent;
            const regex = new RegExp(`(${term})`, 'gi');
            if (regex.test(text)) {
                const highlightedText = text.replace(regex, '<mark>$1</mark>');
                const span = document.createElement('span');
                span.innerHTML = highlightedText;
                textNode.parentNode.replaceChild(span, textNode);
            }
        });
    }

    removeHighlight(element) {
        const marks = element.querySelectorAll('mark');
        marks.forEach(mark => {
            mark.outerHTML = mark.textContent;
        });
    }

    // Método para exportar logs
    exportLogs(format = 'json') {
        const logs = [];
        const logEntries = document.querySelectorAll('.log-entry');
        
        logEntries.forEach(entry => {
            const logData = {
                id: entry.dataset.logId,
                type: entry.dataset.logType,
                message: entry.querySelector('.log-message')?.textContent || '',
                timestamp: entry.querySelector('.log-timestamp')?.textContent || '',
                details: entry.querySelector('.log-details')?.textContent || ''
            };
            logs.push(logData);
        });

        if (format === 'json') {
            this.downloadAsJson(logs, 'historial-asignaciones.json');
        } else if (format === 'csv') {
            this.downloadAsCsv(logs, 'historial-asignaciones.csv');
        }
    }

    downloadAsJson(data, filename) {
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        this.downloadBlob(blob, filename);
    }

    downloadAsCsv(data, filename) {
        const headers = ['ID', 'Tipo', 'Mensaje', 'Timestamp', 'Detalles'];
        const csvContent = [
            headers.join(','),
            ...data.map(log => [
                log.id,
                log.type,
                `"${log.message.replace(/"/g, '""')}"`,
                log.timestamp,
                `"${log.details.replace(/"/g, '""')}"`
            ].join(','))
        ].join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv' });
        this.downloadBlob(blob, filename);
    }

    downloadBlob(blob, filename) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
}

// Inicialización automática si el DOM está listo
document.addEventListener('DOMContentLoaded', () => {
    // Solo inicializar si existe el componente de historial
    if (document.querySelector('[id^="detalles-error-"]')) {
        new HistorialAsignacionesHandler();
    }
});
