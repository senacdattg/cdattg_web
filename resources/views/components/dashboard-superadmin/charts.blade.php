<div class="container-fluid px-4">
    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Asistencia Semanal</h3>
                    <i class="fas fa-chart-line text-muted"></i>
                </div>
                <div class="card-body p-4">
                    <canvas id="asistenciaChartNew" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Uso de Ambientes por Jornada</h3>
                    <i class="fas fa-chart-bar text-muted"></i>
                </div>
                <div class="card-body p-4">
                    <canvas id="ambientesChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm">
                <div class="card-header py-3">
                    <h3 class="card-title mb-0">Distribución por Programa</h3>
                </div>
                <div class="card-body p-4">
                    <canvas id="distribucionChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
