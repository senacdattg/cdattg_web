/**
 * Script específico para la página de índice de registro de actividades
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

    // Manejar el click en el botón de cancelar actividad
    $('button[data-target="#cancelarActividadModal"]').on('click', function() {
        const actividadId = $(this).data('actividad-id');
        const actividadNombre = $(this).data('actividad-nombre');
        const caracterizacionId = $(this).data('caracterizacion-id');
        
        // Actualizar el nombre de la actividad en el modal
        $('#actividad-nombre-modal').text(actividadNombre);
        
        // Actualizar la acción del formulario
        let actionUrl = window.location.origin + '/registro-actividades/' + caracterizacionId + '/' + actividadId;
        $('#form-cancelar-actividad').attr('action', actionUrl);
        
        // Mostrar el modal con animación
        $('#cancelarActividadModal').modal('show');
    });
    
    // Animación de entrada para el modal
    $('#cancelarActividadModal').on('show.bs.modal', function () {
        $(this).find('.modal-content').addClass('animate__animated animate__fadeInDown');
    });
    
    // Limpiar animación al cerrar
    $('#cancelarActividadModal').on('hidden.bs.modal', function () {
        $(this).find('.modal-content').removeClass('animate__animated animate__fadeInDown');
    });
    
    // Mostrar loading al enviar el formulario
    $('#form-cancelar-actividad').on('submit', function(e) {
        // Mostrar loading en el botón
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Cancelando...');
    });

    // Confirmación de eliminación personalizada para actividades
    window.confirmarEliminacionActividad = function(actividadNombre, caracterizacionId, actividadId) {
        alertHandler.showCustomAlert({
            title: '¿Estás seguro?',
            text: `¿Deseas cancelar la actividad "${actividadNombre}"? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear formulario para enviar DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = window.location.origin + '/registro-actividades/' + caracterizacionId + '/' + actividadId;
                
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
    
    console.log('Página de registro de actividades inicializada correctamente');
});
