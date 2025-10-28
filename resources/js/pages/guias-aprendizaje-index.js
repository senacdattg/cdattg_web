/**
 * Script específico para la página de índice de guías de aprendizaje
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
    
    let searchTimeout;
    
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Búsqueda en tiempo real con debounce
    $('#searchGuia').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            performSearch();
        }, 500);
    });

    // Botón de búsqueda
    $('#btnSearch').on('click', function() {
        performSearch();
    });

    // Función de búsqueda
    function performSearch() {
        const searchTerm = $('#searchGuia').val();
        const currentUrl = new URL(window.location);
        
        if (searchTerm.trim()) {
            currentUrl.searchParams.set('search', searchTerm);
        } else {
            currentUrl.searchParams.delete('search');
        }
        
        window.location.href = currentUrl.toString();
    }

    // Limpiar búsqueda
    $('#btnClearSearch').on('click', function() {
        $('#searchGuia').val('');
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.delete('search');
        window.location.href = currentUrl.toString();
    });

    // Filtros de estado
    $('#filterEstado').on('change', function() {
        const estado = $(this).val();
        const currentUrl = new URL(window.location);
        
        if (estado) {
            currentUrl.searchParams.set('estado', estado);
        } else {
            currentUrl.searchParams.delete('estado');
        }
        
        window.location.href = currentUrl.toString();
    });

    // Filtros de programa
    $('#filterPrograma').on('change', function() {
        const programa = $(this).val();
        const currentUrl = new URL(window.location);
        
        if (programa) {
            currentUrl.searchParams.set('programa_id', programa);
        } else {
            currentUrl.searchParams.delete('programa_id');
        }
        
        window.location.href = currentUrl.toString();
    });

    // Filtros de competencia
    $('#filterCompetencia').on('change', function() {
        const competencia = $(this).val();
        const currentUrl = new URL(window.location);
        
        if (competencia) {
            currentUrl.searchParams.set('competencia_id', competencia);
        } else {
            currentUrl.searchParams.delete('competencia_id');
        }
        
        window.location.href = currentUrl.toString();
    });

    // Filtros de estado
    $('#filterStatus').on('change', function() {
        const status = $(this).val();
        const currentUrl = new URL(window.location);
        
        if (status !== '') {
            currentUrl.searchParams.set('status', status);
        } else {
            currentUrl.searchParams.delete('status');
        }
        
        window.location.href = currentUrl.toString();
    });

    // Limpiar todos los filtros
    $('#btnClearFilters').on('click', function() {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.delete('search');
        currentUrl.searchParams.delete('programa_id');
        currentUrl.searchParams.delete('competencia_id');
        currentUrl.searchParams.delete('status');
        window.location.href = currentUrl.toString();
    });

    // Auto-focus en el campo de búsqueda si está vacío
    if (!$('#searchGuia').val()) {
        $('#searchGuia').focus();
    }

    // Función específica para confirmar eliminación de guías
    window.confirmarEliminacion = function(nombre, url) {
        alertHandler.showCustomAlert({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar la guía "${nombre}"? Esta acción no se puede deshacer.`,
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
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search')) $('#searchGuia').val(urlParams.get('search'));
    if (urlParams.has('programa_id')) $('#filterPrograma').val(urlParams.get('programa_id'));
    if (urlParams.has('competencia_id')) $('#filterCompetencia').val(urlParams.get('competencia_id'));
    if (urlParams.has('status')) $('#filterStatus').val(urlParams.get('status'));

    // Mostrar filtros si hay parámetros activos
    if (urlParams.has('search') || urlParams.has('programa_id') || urlParams.has('competencia_id') || urlParams.has('status')) {
        $('#filtrosCollapse').collapse('show');
    }

    // Cambiar ícono del chevron al expandir/colapsar
    $('#filtrosCollapse').on('show.bs.collapse', function () {
        $('[data-target="#filtrosCollapse"] .fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
    $('#filtrosCollapse').on('hide.bs.collapse', function () {
        $('[data-target="#filtrosCollapse"] .fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });
    
    console.log('Página de guías de aprendizaje inicializada correctamente');
});
