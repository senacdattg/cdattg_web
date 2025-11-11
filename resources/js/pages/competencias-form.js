/**
 * Script específico para formularios de competencias (create/edit)
 */
import { AlertHandler } from '../modules/alert-handler.js';

document.addEventListener('DOMContentLoaded', () => {
    const alertHandler = new AlertHandler({
        autoHide: true,
        hideDelay: 5000,
        alertSelector: '.alert'
    });

    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Seleccione una opción',
            allowClear: true,
            language: {
                noResults: () => 'No se encontraron resultados',
                searching: () => 'Buscando...'
            }
        });
    }

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', (event) => {
            const programas = document.querySelector('#programas');
            if (programas && (!programas.value || programas.selectedOptions.length === 0)) {
                event.preventDefault();
                alertHandler.showError('Debe seleccionar al menos un programa de formación.');
            }
        });
    }

    const firstInput = document.querySelector('input[type="text"], input[type="number"], textarea');
    if (firstInput && !firstInput.value) {
        firstInput.focus();
    }
});
