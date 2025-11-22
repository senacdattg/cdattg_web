@extends('adminlte::page')

@section('title', 'Estadísticas')

@section('content_header')
    <h1 class="mb-1"><i class="fas fa-chart-bar me-2"></i>Estadísticas</h1>
    <p class="text-muted mb-3">Panel de visualización de estadísticas del sistema</p>
@stop

@section('content')

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 mb-2 text-primary"><i class="fas fa-users"></i></div>
                    <div class="h4 mb-0" id="total-aspirantes">
                        {{ number_format($estadisticas['total_aspirantes']) }}
                    </div>
                    <small class="text-muted">Total Aspirantes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 mb-2 text-success"><i class="fas fa-user-check"></i></div>
                    <div class="h4 mb-0" id="aspirantes-aceptados">
                        {{ number_format($estadisticas['aspirantes_aceptados']) }}
                    </div>
                    <small class="text-muted">Aspirantes Aceptados</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 mb-2 text-warning"><i class="fas fa-user-clock"></i></div>
                    <div class="h4 mb-0" id="aspirantes-pendientes">
                        {{ number_format($estadisticas['aspirantes_pendientes']) }}
                    </div>
                    <small class="text-muted">Aspirantes Pendientes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 mb-2 text-info"><i class="fas fa-graduation-cap"></i></div>
                    <div class="h4 mb-0" id="programas-activos">
                        {{ number_format($estadisticas['programas_activos']) }}
                    </div>
                    <small class="text-muted">Programas Activos</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0">
                    <strong>Tendencia de Inscripciones</strong>
                </div>
                <div class="card-body">
                    <canvas id="inscripcionesChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0">
                    <strong>Distribución por Programas</strong>
                </div>
                <div class="card-body">
                    <canvas id="programasPieChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Programas con Mayor Demanda</strong>
            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-file-export me-1"></i>Exportar</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Nombre del Programa</th>
                            <th>Total Aspirantes</th>
                            <th>Aceptados</th>
                            <th>Pendientes</th>
                            <th>Tasa de Aceptación</th>
                            <th>Tendencia</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-programas-demanda">
                        @foreach ($estadisticas['programas_demanda'] as $programa)
                            <tr>
                                <td>{{ $programa['programa'] }}</td>
                                <td>{{ $programa['total_aspirantes'] }}</td>
                                <td>{{ $programa['aceptados'] }}</td>
                                <td>{{ $programa['pendientes'] }}</td>
                                <td>{{ $programa['tasa_aceptacion'] }}%</td>
                                <td class="text-success"><i class="fas fa-arrow-up"></i> 0%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-header strong {
            font-size: 1rem;
        }
    </style>
@stop

@section('plugins.Chartjs', true)

@section('js')
    <script src="{{ asset('js/complementarios/estadisticas.js') }}"></script>
    <script>
        let inscripcionesChart, programasPieChart;
        const estadisticasIniciales = @json($estadisticas);

        // Función para formatear datos de tendencia
        function formatearTendenciaInscripciones(tendencia) {
            const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            const labels = [];
            const data = [];

            // Crear array de los últimos 6 meses
            const ultimosMeses = [];
            for (let i = 5; i >= 0; i--) {
                const fecha = new Date();
                fecha.setMonth(fecha.getMonth() - i);
                ultimosMeses.push({
                    year: fecha.getFullYear(),
                    month: fecha.getMonth() + 1
                });
            }

            // Mapear datos existentes
            const datosPorMes = {};
            tendencia.forEach(item => {
                const key = `${item.year}-${item.month}`;
                datosPorMes[key] = item.total;
            });

            // Llenar con datos reales o cero
            ultimosMeses.forEach(({
                year,
                month
            }) => {
                const key = `${year}-${month}`;
                labels.push(`${meses[month - 1]} ${year}`);
                data.push(datosPorMes[key] || 0);
            });

            return {
                labels,
                data
            };
        }

        // Función para inicializar gráficos
        function inicializarGraficos() {
            // Datos de tendencia de inscripciones
            const tendenciaData = formatearTendenciaInscripciones(estadisticasIniciales.tendencia_inscripciones);

            // Línea: Tendencia de Inscripciones
            const ctxLine = document.getElementById('inscripcionesChart').getContext('2d');
            inscripcionesChart = new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: tendenciaData.labels,
                    datasets: [{
                        label: 'Inscripciones',
                        data: tendenciaData.data,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13,110,253,0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#0d6efd'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Datos para gráfico de distribución
            const distribucionData = estadisticasIniciales.distribucion_programas;
            const labelsPie = distribucionData.map(item => item.programa);
            const dataPie = distribucionData.map(item => item.total);

            // Pastel: Distribución por Programas
            const ctxPie = document.getElementById('programasPieChart').getContext('2d');
            programasPieChart = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: labelsPie,
                    datasets: [{
                        data: dataPie,
                        backgroundColor: [
                            '#0d6efd', '#dc3545', '#ffc107', '#20c997', '#6f42c1',
                            '#fd7e14', '#e83e8c', '#20c997', '#6610f2', '#6c757d'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar gráficos
            inicializarGraficos();
        });
    </script>
@stop
