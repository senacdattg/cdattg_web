/**
 * Script genérico para vistas de gestión especializada
 * Maneja funcionalidades complejas como asignaciones, contadores, etc.
 */
import { AlertHandler } from '../modules/alert-handler.js';

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar manejador de alertas
    const alertHandler = new AlertHandler({
        autoHide: true,
        hideDelay: 5000,
        alertSelector: '.alert'
    });
    
    // Inicializar Select2 si existe
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Seleccione una opción',
            allowClear: true,
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                },
                searching: function() {
                    return "Buscando...";
                }
            }
        });
    }

    // Variables globales para contadores
    let elementosSeleccionadosAsignados = 0;
    let elementosSeleccionadosDisponibles = 0;

    // Función para actualizar contadores
    function actualizarContadores() {
        elementosSeleccionadosAsignados = $('.checkbox-asignado:checked').length;
        elementosSeleccionadosDisponibles = $('.checkbox-disponible:checked').length;
        
        // Actualizar botones de acción
        $('.btn-asignar').prop('disabled', elementosSeleccionadosDisponibles === 0);
        $('.btn-desasignar').prop('disabled', elementosSeleccionadosAsignados === 0);
        
        // Actualizar contadores en la UI si existen
        $('.contador-asignados').text(elementosSeleccionadosAsignados);
        $('.contador-disponibles').text(elementosSeleccionadosDisponibles);
    }

    // Eventos para checkboxes
    $(document).on('change', '.checkbox-asignado, .checkbox-disponible', function() {
        actualizarContadores();
    });

    // Función para confirmar asignación principal
    window.confirmarAsignacionPrincipal = function(especialidadNombre) {
        return alertHandler.showCustomAlert({
            title: '¿Asignar como Especialidad Principal?',
            html: `<div class="text-left">
                <p><strong>Especialidad:</strong> ${especialidadNombre}</p>
                <p><strong>Acción:</strong> Se asignará como especialidad principal</p>
                <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Esto reemplazará la especialidad principal actual (si existe)</p>
            </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, asignar',
            cancelButtonText: 'Cancelar'
        });
    };

    // Función para confirmar eliminación de especialidad
    window.confirmarEliminacionEspecialidad = function(especialidadNombre) {
        return alertHandler.showCustomAlert({
            title: '¿Eliminar Especialidad?',
            html: `<div class="text-left">
                <p><strong>Especialidad:</strong> ${especialidadNombre}</p>
                <p><strong>Acción:</strong> Se eliminará la especialidad del instructor</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Esta acción no se puede deshacer</p>
            </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });
    };

    // Función para mover elementos entre listas
    function moverElementos(sourceContainer, targetContainer, targetName) {
        $(sourceContainer + ' .list-item input:checked').each(function() {
            const item = $(this).closest('.list-item');
            const itemId = $(this).val();
            const itemText = item.find('.item-text').text();
            
            // Crear nuevo elemento en el contenedor destino
            const newItem = $(`
                <div class="list-item">
                    <input type="checkbox" name="${targetName}[]" value="${itemId}" class="form-check-input">
                    <span class="item-text">${itemText}</span>
                </div>
            `);
            
            $(targetContainer).append(newItem);
            item.remove();
        });
        
        actualizarContadores();
    }

    // Eventos para botones de movimiento
    $('.btn-mover-derecha').on('click', function() {
        moverElementos('.lista-disponibles', '.lista-asignados', 'elementos_asignados');
    });

    $('.btn-mover-izquierda').on('click', function() {
        moverElementos('.lista-asignados', '.lista-disponibles', 'elementos_disponibles');
    });

    // Función para recalcular horas cuando cambien los días de formación
    $('select[name*="[dias_formacion]"]').on('change', function() {
        const instructorRow = $(this).closest('.instructor-row')[0];
        if (instructorRow) {
            recalcularHoras(instructorRow);
        }
    });

    function recalcularHoras(instructorRow) {
        const diasFormacion = $(instructorRow).find('select[name*="[dias_formacion]"]').val();
        const horasPorDia = $(instructorRow).find('input[name*="[horas_dia]"]').val() || 8;
        
        if (diasFormacion && horasPorDia) {
            const totalHoras = diasFormacion * horasPorDia;
            $(instructorRow).find('.total-horas').text(totalHoras);
            $(instructorRow).find('input[name*="[total_horas]"]').val(totalHoras);
        }
    }

    // Función para validar formularios de gestión
    $('.form-gestion').on('submit', function(e) {
        const form = this;
        const formType = $(form).data('form-type');
        
        // Validaciones específicas según el tipo de formulario
        if (formType === 'asignacion-instructores') {
            const instructoresAsignados = $('.checkbox-asignado:checked').length;
            if (instructoresAsignados === 0) {
                e.preventDefault();
                alertHandler.showError('Debe seleccionar al menos un instructor para asignar.');
                return;
            }
        }
        
        if (formType === 'asignacion-aprendices') {
            const aprendicesAsignados = $('.checkbox-asignado:checked').length;
            if (aprendicesAsignados === 0) {
                e.preventDefault();
                alertHandler.showError('Debe seleccionar al menos un aprendiz para asignar.');
                return;
            }
        }
        
        // Mostrar loading en el botón de envío
        $(form).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...');
    });

    // Función para seleccionar/deseleccionar todos
    $('.select-all-asignados').on('change', function() {
        $('.checkbox-asignado').prop('checked', $(this).is(':checked'));
        actualizarContadores();
    });

    $('.select-all-disponibles').on('change', function() {
        $('.checkbox-disponible').prop('checked', $(this).is(':checked'));
        actualizarContadores();
    });

    // Inicializar contadores
    actualizarContadores();

    // Auto-focus en el primer campo de búsqueda si existe
    const searchInput = document.querySelector('input[type="search"], input[placeholder*="buscar" i]');
    if (searchInput) {
        searchInput.focus();
    }

    console.log('Vista de gestión especializada inicializada correctamente');
});
