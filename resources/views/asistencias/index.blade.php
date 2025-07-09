@extends('layout.app')

@section('title', 'Asistencias')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Asistencias</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item active">Asistencias</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

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
</div>
@endsection

@section('js')
    <script src="{{ asset('js/websocket-handler.js') }}"></script>
    <script>
        // Configuración adicional específica para esta página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página de asistencias cargada con WebSocket habilitado');
        });
    </script>
@endsection
