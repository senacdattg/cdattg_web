/**
 * Script genérico para vistas de detalle (show)
 */
import { AlertHandler } from '../modules/alert-handler.js';

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar manejador de alertas
    const alertHandler = new AlertHandler({
        autoHide: true,
        hideDelay: 5000,
        alertSelector: '.alert'
    });
    
    // Inicializar tooltips
    if (typeof $ !== 'undefined' && $.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }

    // Función genérica para confirmar eliminación
    window.confirmarEliminacion = function(nombre, url, tipo = 'elemento') {
        alertHandler.showCustomAlert({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar ${tipo} "${nombre}"? Esta acción no se puede deshacer.`,
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

    // Función específica para competencias
    window.confirmarEliminacionCompetencia = function(nombre, url) {
        window.confirmarEliminacion(nombre, url, 'la competencia');
    };

    // Función específica para guías
    window.confirmarEliminacionGuia = function(nombre, url) {
        window.confirmarEliminacion(nombre, url, 'la guía');
    };

    // Función específica para programas
    window.confirmarEliminacionPrograma = function(nombre, url) {
        window.confirmarEliminacion(nombre, url, 'el programa');
    };

    // Función específica para fichas
    window.confirmarEliminacionFicha = function(nombre, url) {
        window.confirmarEliminacion(nombre, url, 'la ficha');
    };

    // Función específica para instructores
    window.confirmarEliminacionInstructor = function(nombre, url) {
        window.confirmarEliminacion(nombre, url, 'el instructor');
    };

    // Función específica para aprendices
    window.confirmarEliminacionAprendiz = function(nombre, url) {
        window.confirmarEliminacion(nombre, url, 'el aprendiz');
    };

    // Manejo de modales si existen
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function () {
            $(this).find('.modal-content').addClass('animate__animated animate__fadeInDown');
        });
        
        modal.addEventListener('hidden.bs.modal', function () {
            $(this).find('.modal-content').removeClass('animate__animated animate__fadeInDown');
        });
    });

    // Manejo de formularios de eliminación inline
    const deleteForms = document.querySelectorAll('.formulario-eliminar');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const entityName = formData.get('entity_name') || 'este elemento';
            const actionUrl = form.action;
            
            window.confirmarEliminacion(entityName, actionUrl);
        });
    });

    // Auto-focus en botones de acción si es necesario
    const actionButtons = document.querySelectorAll('.btn-primary, .btn-success');
    if (actionButtons.length > 0 && !document.querySelector('input:focus, select:focus, textarea:focus')) {
        actionButtons[0].focus();
    }

    console.log('Vista de detalle inicializada correctamente');
});
