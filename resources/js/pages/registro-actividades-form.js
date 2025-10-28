/**
 * Script específico para formularios de registro de actividades
 * Maneja calendarios, validaciones y configuraciones específicas
 */
import { AlertHandler } from '../modules/alert-handler.js';

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar manejador de alertas
    const alertHandler = new AlertHandler({
        autoHide: true,
        hideDelay: 5000,
        alertSelector: '.alert'
    });

    console.log('Iniciando configuración del calendario...');
    
    // Configuración inicial
    const CONFIG = {
        diasFormacion: window.diasFormacion || [],
        fechaFinRap: window.fechaFinRap || null,
        fechaHoy: new Date().toISOString().split('T')[0],
        actividades: window.actividades || []
    };

    console.log('Configuración cargada:', CONFIG);

    // Mapeo de días de la semana
    const DIAS_SEMANA = {
        DOMINGO: 18,
        LUNES: 19,
        MARTES: 20,
        MIERCOLES: 21,
        JUEVES: 22,
        VIERNES: 23,
        SABADO: 24
    };

    // Función para obtener el nombre del día
    function obtenerNombreDia(diaId) {
        const nombres = {
            18: 'Domingo',
            19: 'Lunes', 
            20: 'Martes',
            21: 'Miércoles',
            22: 'Jueves',
            23: 'Viernes',
            24: 'Sábado'
        };
        return nombres[diaId] || 'Día desconocido';
    }

    // Función para verificar si una fecha es un día de formación
    function esDiaFormacion(fecha) {
        const diaSemana = new Date(fecha).getDay();
        const diaId = Object.keys(DIAS_SEMANA).find(key => DIAS_SEMANA[key] === (diaSemana + 18));
        return CONFIG.diasFormacion.includes(DIAS_SEMANA[diaId]);
    }

    // Función para obtener fechas permitidas
    function obtenerFechasPermitidas() {
        const fechas = [];
        const fechaInicio = new Date(CONFIG.fechaHoy);
        const fechaFin = CONFIG.fechaFinRap ? new Date(CONFIG.fechaFinRap) : new Date(fechaInicio.getTime() + (30 * 24 * 60 * 60 * 1000)); // 30 días por defecto
        
        for (let fecha = new Date(fechaInicio); fecha <= fechaFin; fecha.setDate(fecha.getDate() + 1)) {
            if (esDiaFormacion(fecha.toISOString().split('T')[0])) {
                fechas.push(fecha.toISOString().split('T')[0]);
            }
        }
        
        return fechas;
    }

    // Inicializar Flatpickr
    function inicializarCalendario() {
        const fechasPermitidas = obtenerFechasPermitidas();
        
        if (fechasPermitidas.length === 0) {
            alertHandler.showWarning('No hay días de formación disponibles en el rango de fechas permitido.');
            return;
        }

        const flatpickrConfig = {
            locale: 'es',
            dateFormat: 'Y-m-d',
            enable: fechasPermitidas,
            disable: [
                function(date) {
                    return !esDiaFormacion(date.toISOString().split('T')[0]);
                }
            ],
            minDate: CONFIG.fechaHoy,
            maxDate: CONFIG.fechaFinRap || null,
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const fechaSeleccionada = selectedDates[0];
                    const diaNombre = obtenerNombreDia(fechaSeleccionada.getDay() + 18);
                    
                    // Actualizar información del día seleccionado
                    actualizarInformacionDia(fechaSeleccionada, diaNombre);
                    
                    // Verificar si ya existe una actividad para esta fecha
                    verificarActividadExistente(dateStr);
                }
            },
            onReady: function(selectedDates, dateStr, instance) {
                // Resaltar fechas con actividades existentes
                resaltarFechasConActividades();
            }
        };

        // Inicializar el calendario
        const calendar = flatpickr('#fecha_actividad', flatpickrConfig);
        
        if (calendar) {
            console.log('Calendario inicializado correctamente');
        } else {
            console.error('Error al inicializar el calendario');
        }
    }

    // Función para actualizar información del día seleccionado
    function actualizarInformacionDia(fecha, diaNombre) {
        const infoElement = document.getElementById('info-dia-seleccionado');
        if (infoElement) {
            infoElement.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-calendar-day"></i>
                    <strong>Día seleccionado:</strong> ${diaNombre}, ${fecha.toLocaleDateString('es-ES')}
                </div>
            `;
        }
    }

    // Función para verificar si ya existe una actividad para la fecha seleccionada
    function verificarActividadExistente(fecha) {
        const actividadExistente = CONFIG.actividades.find(act => act.fecha_actividad === fecha);
        
        if (actividadExistente) {
            alertHandler.showWarning(`Ya existe una actividad registrada para el ${obtenerNombreDia(new Date(fecha).getDay() + 18)}, ${fecha}.`);
            
            // Mostrar información de la actividad existente
            mostrarActividadExistente(actividadExistente);
        } else {
            // Limpiar información de actividad existente
            limpiarActividadExistente();
        }
    }

    // Función para mostrar información de actividad existente
    function mostrarActividadExistente(actividad) {
        const infoElement = document.getElementById('info-actividad-existente');
        if (infoElement) {
            infoElement.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Actividad existente:</strong> ${actividad.nombre}
                    <br>
                    <small>Horas: ${actividad.horas} | Estado: ${actividad.estado}</small>
                </div>
            `;
        }
    }

    // Función para limpiar información de actividad existente
    function limpiarActividadExistente() {
        const infoElement = document.getElementById('info-actividad-existente');
        if (infoElement) {
            infoElement.innerHTML = '';
        }
    }

    // Función para resaltar fechas con actividades existentes
    function resaltarFechasConActividades() {
        CONFIG.actividades.forEach(actividad => {
            const fechaElement = document.querySelector(`[data-date="${actividad.fecha_actividad}"]`);
            if (fechaElement) {
                fechaElement.classList.add('has-activity');
                fechaElement.title = `Actividad: ${actividad.nombre}`;
            }
        });
    }

    // Función para validar formulario
    function validarFormulario() {
        const form = document.getElementById('form-registro-actividad');
        if (!form) return false;

        const fechaActividad = document.getElementById('fecha_actividad').value;
        const nombreActividad = document.getElementById('nombre_actividad').value;
        const horasActividad = document.getElementById('horas_actividad').value;

        if (!fechaActividad) {
            alertHandler.showError('Debe seleccionar una fecha para la actividad.');
            return false;
        }

        if (!nombreActividad.trim()) {
            alertHandler.showError('Debe ingresar el nombre de la actividad.');
            return false;
        }

        if (!horasActividad || horasActividad <= 0) {
            alertHandler.showError('Debe ingresar un número válido de horas.');
            return false;
        }

        // Verificar si ya existe una actividad para esta fecha
        const actividadExistente = CONFIG.actividades.find(act => act.fecha_actividad === fechaActividad);
        if (actividadExistente) {
            alertHandler.showError('Ya existe una actividad registrada para esta fecha.');
            return false;
        }

        return true;
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', () => {
        // Inicializar calendario
        inicializarCalendario();

        // Validar formulario al enviar
        const form = document.getElementById('form-registro-actividad');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validarFormulario()) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Auto-focus en el primer campo
        const firstInput = document.querySelector('input[type="text"], input[type="number"], select');
        if (firstInput && !firstInput.value) {
            firstInput.focus();
        }
    });

    console.log('Script de registro de actividades inicializado correctamente');
});
