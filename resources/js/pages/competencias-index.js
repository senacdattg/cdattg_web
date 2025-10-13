/**
 * Script específico para la página de índice de competencias
 */
import { TableActionsHandler } from '../modules/table-actions.js';
import { AlertHandler } from '../modules/alert-handler.js';

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar manejador de acciones de tabla
    const tableHandler = new TableActionsHandler('body', {
        deleteSelector: '.formulario-eliminar',
        tooltipSelector: '[data-toggle="tooltip"]',
        alertSelector: '.alert',
        autoHideAlerts: true,
        alertHideDelay: 5000
    });
    
    // Inicializar manejador de alertas
    const alertHandler = new AlertHandler({
        autoHide: true,
        hideDelay: 5000,
        alertSelector: '.alert'
    });
    
    // Función específica para confirmar eliminación de competencias
    window.confirmarEliminacion = function(nombre, url) {
        alertHandler.showCustomAlert({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar la competencia "${nombre}"? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear formulario para enviar DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    };
    
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Abrir filtros automáticamente si hay filtros activos
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search') || urlParams.has('status') || urlParams.has('duracion_min') || 
        urlParams.has('duracion_max') || urlParams.has('fecha_inicio') || urlParams.has('fecha_fin')) {
        $('#filtrosCollapse').collapse('show');
    }

    // Cambiar ícono del chevron al expandir/colapsar
    $('#filtrosCollapse').on('show.bs.collapse', function () {
        $('[data-target="#filtrosCollapse"] .fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
    $('#filtrosCollapse').on('hide.bs.collapse', function () {
        $('[data-target="#filtrosCollapse"] .fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });
    
    console.log('Página de competencias inicializada correctamente');
});
