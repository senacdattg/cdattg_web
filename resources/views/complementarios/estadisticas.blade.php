@extends('adminlte::page')

@section('title', 'Estadísticas')

@section('content_header')
    <h1 class="mb-1"><i class="fas fa-chart-bar me-2"></i>Estadísticas</h1>
    <p class="text-muted mb-3">Panel de visualización de estadísticas del sistema</p>
@stop

@section('content')
    <div class="card mb-4">
        <div class="card-body pb-2">
            <form class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="fecha_inicio" class="form-label mb-1">Fecha Inicio:</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                </div>
                <div class="col-md-3">
                    <label for="fecha_fin" class="form-label mb-1">Fecha Fin:</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                </div>
                <div class="col-md-2">
                    <label for="departamento" class="form-label mb-1">Departamento:</label>
                    <select class="form-control" id="departamento" name="departamento">
                        <option value="">Seleccione...</option>
                        <!-- Las opciones se llenan por JS -->
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="municipio" class="form-label mb-1">Municipio:</label>
                    <select class="form-control" id="municipio" name="municipio">
                        <option value="">Seleccione...</option>
                        <!-- Las opciones se llenan por JS -->
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="programa" class="form-label mb-1">Programa:</label>
                    <select class="form-control" id="programa" name="programa">
                        <option selected>Todos los programas</option>
                        <option>Desarrollo de Software</option>
                        <option>Análisis de Datos</option>
                        <option>Diseño Gráfico</option>
                        <option>Redes y Telecomunicaciones</option>
                        <option>Marketing Digital</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 mb-2 text-primary"><i class="fas fa-users"></i></div>
                    <div class="h4 mb-0">1,245</div>
                    <small class="text-muted">Total Aspirantes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 mb-2 text-success"><i class="fas fa-user-check"></i></div>
                    <div class="h4 mb-0">875</div>
                    <small class="text-muted">Aspirantes Aceptados</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 mb-2 text-warning"><i class="fas fa-user-clock"></i></div>
                    <div class="h4 mb-0">370</div>
                    <small class="text-muted">Aspirantes Pendientes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 mb-2 text-info"><i class="fas fa-graduation-cap"></i></div>
                    <div class="h4 mb-0">15</div>
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
                    <tbody>
                        <tr>
                            <td>Desarrollo de Software</td>
                            <td>320</td>
                            <td>240</td>
                            <td>80</td>
                            <td>75%</td>
                            <td class="text-success"><i class="fas fa-arrow-up"></i> 12%</td>
                        </tr>
                        <tr>
                            <td>Análisis de Datos</td>
                            <td>285</td>
                            <td>205</td>
                            <td>80</td>
                            <td>72%</td>
                            <td class="text-success"><i class="fas fa-arrow-up"></i> 8%</td>
                        </tr>
                        <tr>
                            <td>Diseño Gráfico</td>
                            <td>215</td>
                            <td>180</td>
                            <td>35</td>
                            <td>83%</td>
                            <td class="text-danger"><i class="fas fa-arrow-down"></i> 3%</td>
                        </tr>
                        <tr>
                            <td>Redes y Telecomunicaciones</td>
                            <td>185</td>
                            <td>120</td>
                            <td>65</td>
                            <td>65%</td>
                            <td class="text-success"><i class="fas fa-arrow-up"></i> 5%</td>
                        </tr>
                        <tr>
                            <td>Marketing Digital</td>
                            <td>170</td>
                            <td>105</td>
                            <td>65</td>
                            <td>62%</td>
                            <td class="text-success"><i class="fas fa-arrow-up"></i> 15%</td>
                        </tr>
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

@section('js')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/complementarios/estadisticas.js') }}"></script>
    <script>
        // Línea: Tendencia de Inscripciones
        const ctxLine = document.getElementById('inscripcionesChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [{
                    label: 'Inscripciones',
                    data: [120, 180, 250, 210, 300, 350],
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
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Pastel: Distribución por Programas
        const ctxPie = document.getElementById('programasPieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['ADSO', 'Contabilidad', 'Salud Ocupacional', 'Diseño Gráfico'],
                datasets: [{
                    data: [30, 25, 20, 25],
                    backgroundColor: [
                        '#0d6efd', '#dc3545', '#ffc107', '#20c997'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Datos de departamentos y municipios desde la base de datos
        const departamentos = @json($departamentos);

        document.addEventListener('DOMContentLoaded', function () {
            const departamentoSelect = document.getElementById('departamento');
            const municipioSelect = document.getElementById('municipio');

            // Llena el select de departamentos
            departamentoSelect.innerHTML = '<option value="">Seleccione...</option>';
            departamentos.forEach(dep => {
                const opt = document.createElement('option');
                opt.value = dep.id;
                opt.text = dep.departamento;
                departamentoSelect.appendChild(opt);
            });

            // Limpia y llena municipios según el departamento seleccionado
            departamentoSelect.addEventListener('change', function () {
                const departamentoId = this.value;
                municipioSelect.innerHTML = '<option value="">Seleccione...</option>';

                if (departamentoId) {
                    // Hacer petición AJAX para obtener municipios
                    fetch(`/complementarios/api/municipios/${departamentoId}`)
                        .then(response => response.json())
                        .then(municipios => {
                            municipios.forEach(mun => {
                                const opt = document.createElement('option');
                                opt.value = mun.id;
                                opt.text = mun.municipio;
                                municipioSelect.appendChild(opt);
                            });
                        })
                        .catch(error => {
                            console.error('Error al cargar municipios:', error);
                        });
                }
            });

            // Opcional: Limpia municipios si no hay departamento seleccionado
            municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
        });
    </script>
@stop
