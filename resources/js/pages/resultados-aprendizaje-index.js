/**
 * Script específico para la página de índice de resultados de aprendizaje
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
    
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Buscar al presionar Enter en el campo de búsqueda
    $('#searchRAP').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            performSearch();
        }
    });

    // Botón de búsqueda
    $('#btnSearch').on('click', function() {
        performSearch();
    });

    // Función de búsqueda
    function performSearch() {
        const searchTerm = $('#searchRAP').val();
        const competencia = $('#filterCompetencia').val();
        const status = $('#filterStatus').val();

        // Construir URL con parámetros
        let url = window.location.pathname + '?';
        const params = [];

        if (searchTerm) params.push(`search=${encodeURIComponent(searchTerm)}`);
        if (competencia) params.push(`competencia_id=${competencia}`);
        if (status !== '') params.push(`status=${status}`);

        if (params.length > 0) {
            url += params.join('&');
        }

        window.location.href = url;
    }

    // Filtros
    $('#filterCompetencia, #filterStatus').on('change', function() {
        performSearch();
    });

    // Limpiar filtros
    $('#btnClearFilters').on('click', function() {
        $('#searchRAP').val('');
        $('#filterCompetencia').val('');
        $('#filterStatus').val('');
        window.location.href = window.location.pathname;
    });

    // Auto-focus en el campo de búsqueda si está vacío
    if (!$('#searchRAP').val()) {
        $('#searchRAP').focus();
    }

    // Mostrar filtros si hay parámetros activos
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search') || urlParams.has('competencia_id') || urlParams.has('status')) {
        $('#filtrosCollapse').collapse('show');
    }

    // Cambiar ícono del chevron al expandir/colapsar
    $('#filtrosCollapse').on('show.bs.collapse', function () {
        $('[data-target="#filtrosCollapse"] .fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
    $('#filtrosCollapse').on('hide.bs.collapse', function () {
        $('[data-target="#filtrosCollapse"] .fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    // Función específica para confirmar eliminación de resultados
    window.confirmarEliminacion = function(nombre, url) {
        alertHandler.showCustomAlert({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar el resultado "${nombre}"? Esta acción no se puede deshacer.`,
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

    // Restaurar valores de filtros desde URL
    if (urlParams.has('search')) $('#searchRAP').val(urlParams.get('search'));
    if (urlParams.has('competencia_id')) $('#filterCompetencia').val(urlParams.get('competencia_id'));
    if (urlParams.has('status')) $('#filterStatus').val(urlParams.get('status'));
    
    console.log('Página de resultados de aprendizaje inicializada correctamente');
});
