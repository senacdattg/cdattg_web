<form method="POST" action="{{ route('registro-actividades.store', $caracterizacion) }}" class="row g-2">
    @csrf
    <div class="col-md-12">
        <div class="form-group">
            <label class="form-label fw-bold">Nombre de la actividad</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control" placeholder="Ingrese el nombre">
        </div>
    </div>

    <!-- Información del RAP actual -->
    <div class="col-md-12 mb-3">
        <div class="card border-warning shadow-sm">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-calendar-times mr-2"></i>
                <strong>Período del RAP actual</strong>
            </div>
            <div class="card-body">
                @php
                    $rapActual = $caracterizacion->ficha->programaFormacion->competenciaActual()->rapActual();
                @endphp
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
    // Obtener los días de formación del instructor desde la caracterización
    @php
        $diasFormacion = $caracterizacion->instructorFichaDias;
        $diasIds = $diasFormacion->pluck('dia_id')->toArray();

        // Obtener la fecha de fin del RAP actual
        $rapActual = $caracterizacion->ficha->programaFormacion->competenciaActual()->rapActual();
        $fechaFinRap = $rapActual ? $rapActual->fecha_fin : null;
    @endphp

    const diasFormacion = @json($diasIds);
    const fechaFinRap = @json($fechaFinRap);
    const inputFecha = document.getElementById('fecha_evidencia');
    const abrirCalendarioBtn = document.getElementById('abrir-calendario');
    const calendarioModal = document.getElementById('calendario-modal');
    const cerrarCalendarioBtn = document.getElementById('cerrar-calendario');
    const confirmarFechaBtn = document.getElementById('confirmar-fecha');
    const mesActual = document.getElementById('mes-actual');
    const mesAnteriorBtn = document.getElementById('mes-anterior');
    const mesSiguienteBtn = document.getElementById('mes-siguiente');
    const calendarioDias = document.getElementById('calendario-dias');

    console.log('Días de formación configurados:', diasFormacion);
    console.log('Fecha fin del RAP actual:', fechaFinRap);
    console.log('Tipo de fechaFinRap:', typeof fechaFinRap);
    console.log('Fecha fin RAP como Date:', fechaFinRap ? new Date(fechaFinRap) : 'null');
    console.log('Fecha fin RAP como string:', fechaFinRap ? new Date(fechaFinRap).toISOString().split('T')[0] : 'null');

    // Función para verificar si una fecha es un día de formación
    function esDiaFormacion(fecha) {
        const diaSemana = fecha.getDay(); // 0 = Domingo, 1 = Lunes, etc.
        // Mapeo: 0=Domingo->18, 1=Lunes->12, 2=Martes->13, etc.
        const diaId = (diaSemana === 0) ? 18 : diaSemana + 11;

        console.log('Fecha:', fecha.toDateString(), 'Día semana:', diaSemana, 'Día ID:', diaId, 'Es día formación:', diasFormacion.includes(diaId));

        return diasFormacion.includes(diaId);
    }

    // Función para obtener la próxima fecha válida desde una fecha inicial
    function obtenerProximaFechaValida(fechaInicial) {
        let fechaActual = new Date(fechaInicial);
        let intentos = 0;
        const maxIntentos = 365; // Buscar hasta un año adelante

        console.log('Buscando próxima fecha válida desde:', fechaActual.toISOString().split('T')[0]);
        console.log('Fecha fin del RAP:', fechaFinRap);

        while (intentos < maxIntentos) {
            fechaActual.setDate(fechaActual.getDate() + 1);
            intentos++;

            // Verificar si la fecha actual supera la fecha de fin del RAP
            if (fechaFinRap) {
                const fechaFinRapDate = new Date(fechaFinRap);
                // Comparar usando strings de fecha (YYYY-MM-DD) para mayor precisión
                const fechaActualString = fechaActual.toISOString().split('T')[0];
                const fechaFinRapString = fechaFinRapDate.toISOString().split('T')[0];

                if (fechaActualString > fechaFinRapString) {
                    console.log('Fecha supera el límite del RAP:', fechaActualString);
                    return null;
                }
            }

            if (esDiaFormacion(fechaActual)) {
                console.log('Fecha válida encontrada:', fechaActual.toISOString().split('T')[0]);
                return fechaActual;
            }
        }

        console.log('No se encontró fecha válida después de', maxIntentos, 'intentos');
        return null;
    }

    // Función para obtener la primera fecha válida desde hoy
    function obtenerPrimeraFechaValida() {
        const hoy = new Date();
        const primeraFecha = obtenerProximaFechaValida(hoy);

        if (!primeraFecha) {
            console.log('No se encontró primera fecha válida');
            return null;
        }

        // Verificar que la primera fecha no supere el límite del RAP
        if (fechaFinRap) {
            const fechaFinRapDate = new Date(fechaFinRap);
            // Comparar usando strings de fecha (YYYY-MM-DD) para mayor precisión
            const primeraFechaString = primeraFecha.toISOString().split('T')[0];
            const fechaFinRapString = fechaFinRapDate.toISOString().split('T')[0];

            if (primeraFechaString > fechaFinRapString) {
                console.log('Primera fecha válida supera el límite del RAP');
                return null;
            }
        }

        return primeraFecha;
    }

    // Función para validar y corregir la fecha seleccionada
    function validarFecha() {
        if (!inputFecha.value) {
            // Si no hay fecha seleccionada, sugerir la primera fecha válida
            const primeraFecha = obtenerPrimeraFechaValida();
            if (primeraFecha) {
                const confirmacion = confirm(
                    '¿Desea seleccionar la primera fecha disponible (' +
                    primeraFecha.toLocaleDateString('es-ES', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }) + ')?'
                );

                if (confirmacion) {
                    inputFecha.value = primeraFecha.toISOString().split('T')[0];
                }
            }
            return true;
        }

        const fechaSeleccionada = new Date(inputFecha.value);
        const fechaMinima = new Date('{{ \Carbon\Carbon::now()->format('Y-m-d') }}');
        const fechaMaxima = fechaFinRap ? new Date(fechaFinRap) : null;

        // Verificar que la fecha no sea anterior a hoy
        if (fechaSeleccionada < fechaMinima) {
            alert('La fecha no puede ser anterior a hoy.');
            inputFecha.value = '';
            return false;
        }

        // Verificar que la fecha no sea posterior a la fecha de fin del RAP
        if (fechaMaxima) {
            // Comparar usando strings de fecha (YYYY-MM-DD) para mayor precisión
            const fechaSeleccionadaString = fechaSeleccionada.toISOString().split('T')[0];
            const fechaMaximaString = fechaMaxima.toISOString().split('T')[0];

            if (fechaSeleccionadaString > fechaMaximaString) {
                alert('La fecha no puede ser posterior al ' + fechaMaxima.toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }) + ' (fecha de fin del RAP actual).');
                inputFecha.value = '';
                return false;
            }
        }

        // Verificar que sea un día de formación
        if (!esDiaFormacion(fechaSeleccionada)) {
            const proximaFecha = obtenerProximaFechaValida(fechaSeleccionada);
            if (proximaFecha) {
                // Corregir automáticamente sin mostrar confirmación
                inputFecha.value = proximaFecha.toISOString().split('T')[0];
            } else {
                // Si no se encuentra fecha válida, mostrar información de debugging
                const diasNombres = {
                    12: 'Lunes',
                    13: 'Martes',
                    14: 'Miércoles',
                    15: 'Jueves',
                    16: 'Viernes',
                    17: 'Sábado',
                    18: 'Domingo'
                };

                const diasDisponibles = diasFormacion.map(id => diasNombres[id]).join(', ');

                alert(
                    'No se encontró una fecha válida en el próximo año. ' +
                    'Días de formación configurados: ' + diasDisponibles + '\n\n' +
                    'Por favor, verifique la configuración de días de formación del instructor.'
                );
                inputFecha.value = '';
            }
            return false;
        }

        return true;
    }

    // Event listeners
    inputFecha.addEventListener('change', function() {
        validarFecha();
    });
    inputFecha.addEventListener('blur', validarFecha);

    // Validar el formulario antes de enviar
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!validarFecha()) {
            e.preventDefault();
            return false;
        }
    });

    // Mostrar información sobre los días de formación disponibles
    const diasNombres = {
        12: 'Lunes',
        13: 'Martes',
        14: 'Miércoles',
        15: 'Jueves',
        16: 'Viernes',
        17: 'Sábado',
        18: 'Domingo'
    };

    const diasDisponibles = diasFormacion.map(id => diasNombres[id]).join(', ');
    console.log('Días de formación disponibles:', diasDisponibles);

    // Verificar si hay días de formación configurados
    if (diasFormacion.length === 0) {
        console.warn('ADVERTENCIA: No hay días de formación configurados para este instructor');
        alert('ADVERTENCIA: No hay días de formación configurados para este instructor. Por favor, configure los días de formación antes de crear actividades.');
    }

    // Manejo del calendario personalizado
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let selectedDate = null;

    function renderCalendar(month, year) {
        const firstDayOfMonth = new Date(year, month, 1);
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const startingDayOfWeek = firstDayOfMonth.getDay();

        // Actualizar el título del mes
        const monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        mesActual.textContent = `${monthNames[month]} ${year}`;

        // Limpiar días anteriores
        calendarioDias.innerHTML = '';

        // Agregar días vacíos al inicio
        for (let i = 0; i < startingDayOfWeek; i++) {
            calendarioDias.innerHTML += '<div class="calendario-dia empty"></div>';
        }

        // Agregar días del mes
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const dateString = date.toISOString().split('T')[0];
            const today = new Date();
            const isToday = date.toDateString() === today.toDateString();
            const isPast = date < new Date(today.getFullYear(), today.getMonth(), today.getDate());

            // Verificar si la fecha es posterior al fin del RAP
            let isAfterRapEnd = false;
            if (fechaFinRap) {
                const fechaFinRapDate = new Date(fechaFinRap);
                // Comparar usando strings de fecha (YYYY-MM-DD) para mayor precisión
                const dateStringOnly = date.toISOString().split('T')[0];
                const fechaFinRapString = fechaFinRapDate.toISOString().split('T')[0];
                isAfterRapEnd = dateStringOnly > fechaFinRapString;

                // Debug log para el 30 de agosto y fechas cercanas al fin del RAP
                if (dateStringOnly === fechaFinRapString || dateStringOnly === '2024-08-30') {
                    console.log('Debug fecha fin RAP:', {
                        dateStringOnly: dateStringOnly,
                        fechaFinRapString: fechaFinRapString,
                        isAfterRapEnd: isAfterRapEnd,
                        fechaFinRap: fechaFinRap,
                        day: day,
                        month: month,
                        year: year
                    });
                }
            }

            const isFormationDay = esDiaFormacion(date);
            const isDisabled = isPast || isAfterRapEnd || !isFormationDay;

            // Debug log para el último día del RAP
            if (fechaFinRap && dateString === fechaFinRap) {
                console.log('Debug último día RAP:', {
                    dateString: dateString,
                    isPast: isPast,
                    isAfterRapEnd: isAfterRapEnd,
                    isFormationDay: isFormationDay,
                    isDisabled: isDisabled,
                    diasFormacion: diasFormacion
                });
            }

            let className = 'calendario-dia';
            if (isToday) className += ' today';
            if (isDisabled) className += ' disabled';
            if (selectedDate === dateString) className += ' fecha-seleccionada';

            const dayElement = document.createElement('div');
            dayElement.className = className;
            dayElement.setAttribute('data-date', dateString);
            dayElement.innerHTML = `<span>${day}</span>`;

            if (!isDisabled) {
                dayElement.addEventListener('click', function() {
                    // Remover selección previa
                    document.querySelectorAll('.calendario-dia').forEach(d => d.classList.remove('fecha-seleccionada'));
                    // Seleccionar nueva fecha
                    this.classList.add('fecha-seleccionada');
                    selectedDate = this.getAttribute('data-date');
                });
            }

            calendarioDias.appendChild(dayElement);
        }
    }

    function prevMonth() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar(currentMonth, currentYear);
    }

    function nextMonth() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar(currentMonth, currentYear);
    }

    function openCalendar() {
        calendarioModal.style.display = 'flex';
        renderCalendar(currentMonth, currentYear);
    }

    function closeCalendar() {
        calendarioModal.style.display = 'none';
        selectedDate = null;
    }

    function confirmDate() {
        if (selectedDate) {
            inputFecha.value = selectedDate;
            closeCalendar();
        } else {
            alert('Por favor, selecciona una fecha válida');
        }
    }

    abrirCalendarioBtn.addEventListener('click', openCalendar);
    cerrarCalendarioBtn.addEventListener('click', closeCalendar);
    confirmarFechaBtn.addEventListener('click', confirmDate);
    mesAnteriorBtn.addEventListener('click', prevMonth);
    mesSiguienteBtn.addEventListener('click', nextMonth);

    // Close calendar when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target == calendarioModal) {
            closeCalendar();
        }
    });

    // Initial render
    renderCalendar(currentMonth, currentYear);
});
</script>
