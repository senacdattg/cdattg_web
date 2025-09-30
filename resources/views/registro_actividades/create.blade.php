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
            <input type="text" name="fecha_evidencia" id="fecha_evidencia" value="{{ old('fecha_evidencia') }}" class="form-control" placeholder="Seleccione la fecha" readonly>
            <small class="form-text text-muted">
                <i class="fas fa-calendar-alt"></i> Haz clic en el campo para abrir el calendario. Solo se muestran fechas válidas para formación.
            </small>
        </div>
    </div>

    <div class="col-12 text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>Guardar Actividad
        </button>
    </div>
</form>

<style>
/* Estilos para Flatpickr */
.flatpickr-input {
    cursor: pointer !important;
}

.flatpickr-input:focus {
    border-color: #17a2b8;
    box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
}
</style>

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Iniciando configuración del calendario...');
    
    // Configuración inicial
    const CONFIG = {
        diasFormacion: @json($caracterizacion->instructorFichaDias->pluck('dia_id')->toArray()),
        fechaFinRap: @json($caracterizacion->ficha->programaFormacion->competenciaActual()->rapActual()?->fecha_fin),
        fechaHoy: '{{ \Carbon\Carbon::now()->format('Y-m-d') }}',
        actividades: @json($actividades ?? [])
    };

    console.log('Configuración cargada:', CONFIG);

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
        }
    };

    // Generar fechas permitidas
    const fechasPermitidas = [];
    const fechaInicio = new Date(CONFIG.fechaHoy);
    const fechaFin = CONFIG.fechaFinRap ? new Date(CONFIG.fechaFinRap) : new Date(fechaInicio.getTime() + (365 * 24 * 60 * 60 * 1000));

    for (let fecha = new Date(fechaInicio); fecha <= fechaFin; fecha.setDate(fecha.getDate() + 1)) {
        if (DateUtils.esFechaValida(new Date(fecha))) {
            fechasPermitidas.push(DateUtils.toDateString(new Date(fecha)));
        }
    }

    console.log('Fechas permitidas generadas:', fechasPermitidas.length);

    // Verificar que Flatpickr esté disponible
    if (typeof flatpickr === 'undefined') {
        console.error('Flatpickr no está cargado');
        alert('Error: No se pudo cargar el calendario. Por favor, recarga la página.');
        return;
    }

    // Configurar Flatpickr
    try {
        const flatpickrInstance = flatpickr("#fecha_evidencia", {
            locale: "es",
            dateFormat: "Y-m-d",
            enable: fechasPermitidas,
            minDate: CONFIG.fechaHoy,
            maxDate: CONFIG.fechaFinRap,
            allowInput: true,
            clickOpens: true,
            disable: [
                function(date) {
                    return !DateUtils.esFechaValida(date);
                }
            ],
            onChange: function(selectedDates, dateStr, instance) {
                console.log('Fecha seleccionada:', dateStr);
                if (selectedDates.length > 0) {
                    const fechaSeleccionada = selectedDates[0];
                    if (!DateUtils.esFechaValida(fechaSeleccionada)) {
                        instance.clear();
                        alert('La fecha seleccionada no es válida para formación.');
                    }
                }
            },
            onOpen: function(selectedDates, dateStr, instance) {
                console.log('Calendario abierto - Fechas permitidas:', fechasPermitidas.length);
            },
            onReady: function(selectedDates, dateStr, instance) {
                console.log('Flatpickr inicializado correctamente');
            }
        });

        console.log('Flatpickr configurado:', flatpickrInstance);

    } catch (error) {
        console.error('Error al configurar Flatpickr:', error);
        alert('Error al configurar el calendario. Por favor, recarga la página.');
    }

    // Validación del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        const inputFecha = document.getElementById('fecha_evidencia');
        
        if (!inputFecha.value) {
            e.preventDefault();
            alert('Por favor, seleccione una fecha para la actividad.');
            return false;
        }

        const fechaSeleccionada = new Date(inputFecha.value);
        if (!DateUtils.esFechaValida(fechaSeleccionada)) {
            e.preventDefault();
            alert('La fecha seleccionada no es válida. Verifique que sea un día de formación disponible.');
            return false;
        }
    });

    // Validación inicial
    if (CONFIG.diasFormacion.length === 0) {
        console.warn('No hay días de formación configurados');
        alert('ADVERTENCIA: No hay días de formación configurados para este instructor.');
    }

    console.log('Configuración del calendario completada');
});
</script>
