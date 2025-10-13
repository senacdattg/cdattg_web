/**
 * Script específico para la edición de instructores
 */
import { FormHandler } from '../modules/form-handler.js';
import { AlertHandler } from '../modules/alert-handler.js';

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar manejador de formularios
    const formHandler = new FormHandler('form', {
        validateOnSubmit: true,
        showLoadingOnSubmit: true,
        preventDoubleSubmit: true
    });
    
    // Inicializar manejador de alertas
    const alertHandler = new AlertHandler({
        autoHide: true,
        hideDelay: 5000,
        alertSelector: '.alert'
    });
    
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    console.log('Página de edición de instructores inicializada correctamente');
});
