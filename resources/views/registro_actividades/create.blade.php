<form method="POST" action="{{ route('registro-actividades.store', $caracterizacion) }}" class="row g-2">
    @csrf
    <!-- Información del RAP actual -->
    <div class="col-md-12 mb-3">
        <div class="card border-warning shadow-sm">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-calendar-times mr-2"></i>
                <strong>Resultado de Aprendizaje actual</strong>
            </div>
            <div class="card-body">
                @if($rapActual)
                    <div class="row">
                        <div class="col-md-6">
                            <strong>RAP:</strong> {{ $rapActual->codigo }} - {{ $rapActual->nombre }}
                        </div>
                        <div class="col-md-3">
                            <strong>Inicio:</strong> {{ \Carbon\Carbon::parse($rapActual->fecha_inicio)->format('d/m/Y') }}
                        </div>
                        <div class="col-md-3">
                            <strong>Fin:</strong> {{ \Carbon\Carbon::parse($rapActual->fecha_fin)->format('d/m/Y') }}
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle"></i> Solo podrás crear actividades hasta el {{ \Carbon\Carbon::parse($rapActual->fecha_fin)->format('d/m/Y') }}.
                    </small>
                @else
                    <div class="text-muted">
                        <i class="fas fa-exclamation-triangle"></i> No hay un RAP activo configurado para el período actual.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="form-label fw-bold">Nombre de la actividad</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control" placeholder="Ingrese el nombre">
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label class="form-label fw-bold">Fecha de la actividad</label>
            <div class="input-group">
                <input type="text" name="fecha_evidencia" id="fecha_evidencia" value="{{ old('fecha_evidencia') }}" class="form-control" placeholder="Seleccione la fecha" readonly>
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary" id="abrir-calendario">
                        <i class="fas fa-calendar-alt"></i>
                    </button>
                </div>
            </div>
            <small class="form-text text-muted">
                <i class="fas fa-calendar-alt"></i> Haz clic en el botón del calendario para seleccionar una fecha. Los días no disponibles aparecen deshabilitados.
            </small>
        </div>
    </div>

    <!-- Calendario personalizado -->
    <div id="calendario-modal" class="calendario-modal" style="display: none;">
        <div class="calendario-content">
            <div class="calendario-header">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="mes-anterior">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h5 id="mes-actual" class="mb-0"></h5>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="mes-siguiente">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="calendario-body">
                <div class="calendario-dias-semana">
                    <div>Dom</div>
                    <div>Lun</div>
                    <div>Mar</div>
                    <div>Mié</div>
                    <div>Jue</div>
                    <div>Vie</div>
                    <div>Sáb</div>
                </div>
                <div id="calendario-dias" class="calendario-dias"></div>
            </div>
            <div class="calendario-footer">
                <button type="button" class="btn btn-secondary" id="cerrar-calendario">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmar-fecha">Confirmar</button>
            </div>
        </div>
    </div>
    <div class="col-12 text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>Guardar Actividad
        </button>
    </div>
</form>

<style>
/* Estilos para el calendario personalizado */
.calendario-modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.calendario-content {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    width: 350px;
    max-width: 90vw;
    animation: slideInDown 0.3s ease-out;
}

@keyframes slideInDown {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.calendario-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
    border-radius: 12px 12px 0 0;
}

.calendario-header h5 {
    margin: 0;
    font-weight: 600;
}

.calendario-body {
    padding: 20px;
}

.calendario-dias-semana {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-bottom: 15px;
    text-align: center;
    font-weight: 600;
    color: #6c757d;
    font-size: 0.9rem;
}

.calendario-dias {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}

.calendario-dia {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 500;
    border: 2px solid transparent;
    background-color: #f8f9fa;
    min-height: 40px;
}

.calendario-dia.empty {
    background-color: transparent;
    cursor: default;
}

.calendario-dia:hover:not(.disabled):not(.empty) {
    background-color: #e9ecef;
    transform: scale(1.05);
}

.calendario-dia.disabled {
    background-color: #f8f9fa;
    color: #adb5bd;
    cursor: not-allowed;
    opacity: 0.5;
    position: relative;
}

.calendario-dia.disabled::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 1px;
    height: 1px;
    background-color: #adb5bd;
    transform: rotate(45deg) scale(20);
    opacity: 0.3;
}

.calendario-dia.today {
    background-color: #17a2b8;
    color: white;
    font-weight: 600;
    border-color: #17a2b8;
}

.calendario-dia.fecha-seleccionada {
    background-color: #28a745;
    color: white;
    font-weight: 600;
    border-color: #28a745;
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}

.calendario-footer {
    display: flex;
    justify-content: space-between;
    padding: 20px;
    border-top: 1px solid #e9ecef;
    background-color: #f8f9fa;
    border-radius: 0 0 12px 12px;
}

/* Estilos para el input group */
.input-group .form-control {
    border-right: 0;
}

.input-group .btn {
    border-left: 0;
}

.input-group .form-control:focus {
    border-right: 0;
    box-shadow: none;
}

.input-group .btn:focus {
    box-shadow: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuración inicial
    const CONFIG = {
        diasFormacion: @json($caracterizacion->instructorFichaDias->pluck('dia_id')->toArray()),
        fechaFinRap: @json($caracterizacion->ficha->programaFormacion->competenciaActual()->rapActual()?->fecha_fin),
        fechaHoy: '{{ \Carbon\Carbon::now()->format('Y-m-d') }}',
        actividades: @json($actividades),
        maxIntentosBusqueda: 365
    };

    // Elementos del DOM
    const DOM = {
        inputFecha: document.getElementById('fecha_evidencia'),
        calendarioModal: document.getElementById('calendario-modal'),
        calendarioDias: document.getElementById('calendario-dias'),
        mesActual: document.getElementById('mes-actual'),
        selectedDate: null,
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        fechasInvalidadas: []
    };

    // Mapeo de días de la semana
    const DIAS_SEMANA = {
        DOMINGO: 18,
        LUNES: 12,
        MARTES: 13,
        MIERCOLES: 14,
        JUEVES: 15,
        VIERNES: 16,
        SABADO: 17
    };

    const NOMBRES_DIAS = {
        12: 'Lunes', 13: 'Martes', 14: 'Miércoles', 15: 'Jueves',
        16: 'Viernes', 17: 'Sábado', 18: 'Domingo'
    };

    const NOMBRES_MESES = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    // Utilidades de fecha
    const DateUtils = {
        toDateString: (date) => date.toISOString().split('T')[0],

        getDiaId: (fecha) => {
            const diaSemana = fecha.getDay();
            return diaSemana === 0 ? DIAS_SEMANA.DOMINGO : diaSemana + 11;
        },

        esDiaFormacion: (fecha) => {
            const diaId = DateUtils.getDiaId(fecha);
            return CONFIG.diasFormacion.includes(diaId);
        },

        esFechaValida: (fecha) => {
            const fechaHoy = new Date(CONFIG.fechaHoy);
            const fechaFinRap = CONFIG.fechaFinRap ? new Date(CONFIG.fechaFinRap) : null;

            const esPasada = fecha < fechaHoy;
            const esDespuesRap = fechaFinRap && DateUtils.toDateString(fecha) > DateUtils.toDateString(fechaFinRap);
            const esDiaFormacion = DateUtils.esDiaFormacion(fecha);
            const esFechaOcupada = CONFIG.actividades.some(actividad =>
                DateUtils.toDateString(fecha) === DateUtils.toDateString(new Date(actividad.fecha_evidencia))
            );

            return !esPasada && !esDespuesRap && esDiaFormacion && !esFechaOcupada;
        },

        obtenerProximaFechaValida: (fechaInicial) => {
            let fechaActual = new Date(fechaInicial);
            let intentos = 0;

            while (intentos < CONFIG.maxIntentosBusqueda) {
                fechaActual.setDate(fechaActual.getDate() + 1);
                intentos++;

                if (DateUtils.esFechaValida(fechaActual)) {
                    return fechaActual;
                }
            }
            return null;
        },

        obtenerPrimeraFechaValida: () => {
            const hoy = new Date();
            return DateUtils.obtenerProximaFechaValida(hoy);
        }
    };

    // Validación de fechas
    const Validator = {
        validarFecha: () => {
            if (!DOM.inputFecha.value) {
                return Validator.sugerirPrimeraFecha();
            }

            const fechaSeleccionada = new Date(DOM.inputFecha.value);

            if (!DateUtils.esFechaValida(fechaSeleccionada)) {
                return Validator.corregirFecha(fechaSeleccionada);
            }

            return true;
        },

        sugerirPrimeraFecha: () => {
            const primeraFecha = DateUtils.obtenerPrimeraFechaValida();
            if (!primeraFecha) return false;

            const confirmacion = confirm(
                `¿Desea seleccionar la primera fecha disponible (${primeraFecha.toLocaleDateString('es-ES', {
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                })})?`
            );

            if (confirmacion) {
                DOM.inputFecha.value = DateUtils.toDateString(primeraFecha);
            }
            return true;
        },

        corregirFecha: (fechaInvalida) => {
            const proximaFecha = DateUtils.obtenerProximaFechaValida(fechaInvalida);

            if (proximaFecha) {
                DOM.inputFecha.value = DateUtils.toDateString(proximaFecha);
                return false;
            }

            const diasDisponibles = CONFIG.diasFormacion.map(id => NOMBRES_DIAS[id]).join(', ');
            alert(`No se encontró una fecha válida. Días disponibles: ${diasDisponibles}`);
            DOM.inputFecha.value = '';
            return false;
        }
    };

    // Calendario personalizado
    const Calendar = {
        render: (month, year) => {
            const firstDay = new Date(year, month, 1);
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const startingDay = firstDay.getDay();

            Calendar.updateHeader(month, year);
            Calendar.renderDays(startingDay, daysInMonth, month, year);
        },

        updateHeader: (month, year) => {
            DOM.mesActual.textContent = `${NOMBRES_MESES[month]} ${year}`;
        },

        renderDays: (startingDay, daysInMonth, month, year) => {
            DOM.calendarioDias.innerHTML = '';

            // Días vacíos al inicio
            for (let i = 0; i < startingDay; i++) {
                DOM.calendarioDias.innerHTML += '<div class="calendario-dia empty"></div>';
            }

            // Días del mes
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dateString = DateUtils.toDateString(date);
                const dayElement = Calendar.createDayElement(date, day, dateString);
                DOM.calendarioDias.appendChild(dayElement);
            }
        },

        createDayElement: (date, day, dateString) => {
            const isToday = DateUtils.toDateString(date) === CONFIG.fechaHoy;
            const isDisabled = !DateUtils.esFechaValida(date);
            const isSelected = DOM.selectedDate === dateString;

            const className = Calendar.getDayClassName(isToday, isDisabled, isSelected);

            const dayElement = document.createElement('div');
            dayElement.className = className;
            dayElement.setAttribute('data-date', dateString);
            dayElement.innerHTML = `<span>${day}</span>`;

            if (!isDisabled) {
                dayElement.addEventListener('click', () => Calendar.selectDate(dateString));
            }

            return dayElement;
        },

        getDayClassName: (isToday, isDisabled, isSelected) => {
            let className = 'calendario-dia';
            if (isToday) className += ' today';
            if (isDisabled) className += ' disabled';
            if (isSelected) className += ' fecha-seleccionada';
            return className;
        },

        selectDate: (dateString) => {
            document.querySelectorAll('.calendario-dia').forEach(d =>
                d.classList.remove('fecha-seleccionada')
            );
            document.querySelector(`[data-date="${dateString}"]`).classList.add('fecha-seleccionada');
            DOM.selectedDate = dateString;
        },

        open: () => {
            DOM.calendarioModal.style.display = 'flex';
            Calendar.render(DOM.currentMonth, DOM.currentYear);
        },

        close: () => {
            DOM.calendarioModal.style.display = 'none';
            DOM.selectedDate = null;
        },

        confirm: () => {
            if (DOM.selectedDate) {
                DOM.inputFecha.value = DOM.selectedDate;
                Calendar.close();
            } else {
                alert('Por favor, selecciona una fecha válida');
            }
        },

        prevMonth: () => {
            DOM.currentMonth--;
            if (DOM.currentMonth < 0) {
                DOM.currentMonth = 11;
                DOM.currentYear--;
            }
            Calendar.render(DOM.currentMonth, DOM.currentYear);
        },

        nextMonth: () => {
            DOM.currentMonth++;
            if (DOM.currentMonth > 11) {
                DOM.currentMonth = 0;
                DOM.currentYear++;
            }
            Calendar.render(DOM.currentMonth, DOM.currentYear);
        }
    };

    // Event listeners
    const EventHandlers = {
        init: () => {
            DOM.inputFecha.addEventListener('change', Validator.validarFecha);
            DOM.inputFecha.addEventListener('blur', Validator.validarFecha);

            document.querySelector('form').addEventListener('submit', (e) => {
                if (!Validator.validarFecha()) {
                    e.preventDefault();
                }
            });

            // Calendario
            document.getElementById('abrir-calendario').addEventListener('click', Calendar.open);
            document.getElementById('cerrar-calendario').addEventListener('click', Calendar.close);
            document.getElementById('confirmar-fecha').addEventListener('click', Calendar.confirm);
            document.getElementById('mes-anterior').addEventListener('click', Calendar.prevMonth);
            document.getElementById('mes-siguiente').addEventListener('click', Calendar.nextMonth);

            // Cerrar al hacer clic fuera
            window.addEventListener('click', (event) => {
                if (event.target === DOM.calendarioModal) {
                    Calendar.close();
                }
            });
        }
    };

    // Inicialización y validaciones
    const App = {
        init: () => {
            App.validarConfiguracion();
            EventHandlers.init();
            Calendar.render(DOM.currentMonth, DOM.currentYear);
        },

        validarConfiguracion: () => {
            if (CONFIG.diasFormacion.length === 0) {
                alert('ADVERTENCIA: No hay días de formación configurados para este instructor.');
            }

            console.log('Configuración cargada:', {
                diasFormacion: CONFIG.diasFormacion,
                fechaFinRap: CONFIG.fechaFinRap,
                diasDisponibles: CONFIG.diasFormacion.map(id => NOMBRES_DIAS[id]).join(', ')
            });
        }
    };

    // Iniciar aplicación
    App.init();
});
</script>
