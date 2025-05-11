<div class="container-fluid px-4 mt-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm">
                <div class="card-header py-3 d-flex justify-content-between align-items-center" data-toggle="collapse"
                    data-target="#calendarWidget" style="cursor: pointer;">
                    <h3 class="card-title mb-0">Calendario de Eventos</h3>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="collapse show" id="calendarWidget">
                    <div class="card-body">
                        <div class="small-calendar">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Diciembre 2023</h5>
                                <div>
                                    <button class="btn btn-sm btn-outline-secondary"><i
                                            class="fas fa-chevron-left"></i></button>
                                    <button class="btn btn-sm btn-outline-secondary"><i
                                            class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>Lu</th>
                                            <th>Ma</th>
                                            <th>Mi</th>
                                            <th>Ju</th>
                                            <th>Vi</th>
                                            <th>Sa</th>
                                            <th>Do</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-muted">27</td>
                                            <td class="text-muted">28</td>
                                            <td class="text-muted">29</td>
                                            <td class="text-muted">30</td>
                                            <td>1</td>
                                            <td>2</td>
                                            <td>3</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td class="bg-info text-white">5</td>
                                            <td>6</td>
                                            <td>7</td>
                                            <td class="bg-success text-white">8</td>
                                            <td>9</td>
                                            <td>10</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm">
                <div class="card-header py-3 d-flex justify-content-between align-items-center" data-toggle="collapse"
                    data-target="#messagesWidget" style="cursor: pointer;">
                    <h3 class="card-title mb-0">Mensajes Internos</h3>
                    <span class="badge bg-primary">3 nuevos</span>
                </div>
                <div class="collapse show" id="messagesWidget">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Coordinación Académica</h6>
                                    <small class="text-primary">Nuevo</small>
                                </div>
                                <small>Reunión de seguimiento programada...</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Sistema</h6>
                                    <small class="text-primary">Nuevo</small>
                                </div>
                                <small>Actualización del sistema completada...</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Mantenimiento</h6>
                                    <small>Ayer</small>
                                </div>
                                <small>Reporte de mantenimiento semanal...</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm">
                <div class="card-header py-3 d-flex justify-content-between align-items-center" data-toggle="collapse"
                    data-target="#activityWidget" style="cursor: pointer;">
                    <h3 class="card-title mb-0">Registro de Actividad</h3>
                    <i class="fas fa-history"></i>
                </div>
                <div class="collapse show" id="activityWidget">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Inicio de sesión</h6>
                                    <small>Hace 5 min</small>
                                </div>
                                <small class="text-muted">Usuario: Juan Pérez</small>
                            </div>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Actualización de registro</h6>
                                    <small>Hace 15 min</small>
                                </div>
                                <small class="text-muted">Ficha: 2556456</small>
                            </div>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Nueva asistencia registrada</h6>
                                    <small>Hace 30 min</small>
                                </div>
                                <small class="text-muted">Ambiente: 301</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@section('js')
    @vite(['resources/js/dashboards/superadmin/widgets.js'])
@endsection
