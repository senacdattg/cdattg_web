@extends('adminlte::page')

@section('title', 'Asistencias')

@section('content_header')
    <x-page-header 
        icon="fa-check-circle" 
        title="Asistencias"
        subtitle="Gestión de asistencias de aprendices"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
            ['label' => 'Asistencias', 'active' => true, 'icon' => 'fa-check-circle']
        ]"
    />
@endsection

@section('content')

    <section class="content">
        <div class="container-fluid">
            <!-- Contador de asistencias en tiempo real -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="asistencia-counter">0</h3>
                            <p>Asistencias Hoy</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de asistencias -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Asistencias</h3>
                </div>
                <div class="card-body">
                    <table id="asistencia-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Aprendiz</th>
                                <th>Documento</th>
                                <th>Hora Ingreso</th>
                                <th>Hora Salida</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Las filas se agregarán dinámicamente via WebSocket -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="{{ asset('js/websocket-handler.js') }}"></script>
@endsection
