@extends('adminlte::page')
@section('content')
    <section class="content mt-3">
        <div class="card">
            <!-- Encabezado con información del programa -->
            <div class="card-header bg-primary">
                <div class="row">
                    <div class="col-md-6">
                        <a class="btn btn-warning btn-sm" href="{{ route('asistence.web') }}">
                            <i class="fas fa-arrow-left"></i>
                            Volver
                        </a>
                    </div>
                    <div class="col-md-6">
                        <h3 class="text-center text-white">{{$fichaCaracterizacion->programaFormacion->nombre}}</h3>
                    </div>
                </div>
            </div>

            <!-- Información del programa -->
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-book"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">N° Ficha</span>
                                <span class="info-box-number">{{$fichaCaracterizacion->ficha}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Jornada</span>
                                <span class="info-box-number">{{$fichaCaracterizacion->jornadaFormacion->jornada}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-user-tie"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Instructor</span>
                                <span class="info-box-number">{{$fichaCaracterizacion->instructor->nombre}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensajes de alerta -->
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success" id="success-alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger" id="error-alert">
                        {{ session('error') }}
                    </div>
                @endif

                <script>
                    setTimeout(() => {
                        let successAlert = document.getElementById('success-alert');
                        if (successAlert) {
                            successAlert.style.display = 'none';
                        }
                        let errorAlert = document.getElementById('error-alert');
                        if (errorAlert) {
                            errorAlert.style.display = 'none';
                        }
                    }, 3000);
                </script>

                <!-- Lector QR -->
                <div class="qr-result" id="qr-result"></div>
                <h4 class="mt-4 mb-3 text-primary"><i class="fas fa-qrcode"></i> Escanear QR para Asistencia</h4>
                <div class="d-flex justify-content-center mb-4">
                    <div id="qr-lector" style="width: 350px; border-radius: 10px; box-shadow: 0 2px 8px rgba(44,62,80,0.15);"></div>
                </div>

                <!-- Listado de aprendices -->
                <div class="card mt-4 border-0 shadow-sm">
                    <div class="card-header bg-gradient-secondary">
                        <h5 class="text-white mb-0"><i class="fas fa-users"></i> Listado de Aprendices</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Identificación</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Asistencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($aprendizPersona as $index => $aprendiz)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $aprendiz->numero_identificacion }}</td>
                                        <td>{{ $aprendiz->primer_nombre }} {{ $aprendiz->segundo_nombre }}</td>
                                        <td>{{ $aprendiz->primer_apellido }} {{ $aprendiz->segundo_apellido }}</td>
                                        <td>
                                            <span id="asistencia-{{ $aprendiz->numero_identificacion }}">
                                                <i class="fas fa-times text-danger"></i>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Formulario de asistencia -->
                <form id="asistencia-form" action="{{route('asistence.store')}}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="caracterizacion_id" value="{{$fichaCaracterizacion->id}}">
                    <ul name="asistencia_web" id="asistencia-list"></ul>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i> Guardar Asistencia
                            </button>
                        </div>
                        <div class="col-md-6 text-right">
                            <a class="btn btn-danger btn-block" href="{{ route('asistence.exitFormation', ['caracterizacion_id' => $fichaCaracterizacion->id]) }}">
                                <i class="fas fa-door-open"></i> Finalizar Formación
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function cuandoElDocumentoEsteListo(funcionAEjecutar) {
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(funcionAEjecutar, 1);
        } else {
            document.addEventListener("DOMContentLoaded", funcionAEjecutar);
        }
    }

    cuandoElDocumentoEsteListo(() => {
        let listaAsistencias = document.getElementById('asistencia-list');
        let ultimoCodigoEscaneado, contadorEscaneos = 0;

        function cuandoSeEscaneaQR(codigoQR, resultadoDecodificado) {
            if (codigoQR !== ultimoCodigoEscaneado) {
                ++contadorEscaneos;
                ultimoCodigoEscaneado = codigoQR;

                let numeroIdentificacion = codigoQR.trim();
                let horaActual = new Date().toLocaleTimeString('es-ES', { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit' 
                });

                // Marcar asistencia en la tabla
                let celdaAsistencia = document.getElementById('asistencia-' + numeroIdentificacion);
                if (celdaAsistencia) {
                    celdaAsistencia.innerHTML = '<i class="fas fa-check text-success"></i>';
                }

                // Guardar los datos en un campo oculto del formulario
                let campoOculto = document.createElement('input');
                campoOculto.type = 'hidden';
                campoOculto.name = 'asistencia[]';
                campoOculto.value = JSON.stringify({
                    identificacion: numeroIdentificacion,
                    hora_ingreso: horaActual
                });
                document.getElementById('asistencia-form').appendChild(campoOculto);
            }
        }

        // Inicializar el escáner de QR
        let escanerQR = new Html5QrcodeScanner("qr-lector", { 
            fps: 10,
            qrbox: 250
        });
        escanerQR.render(cuandoSeEscaneaQR);
    });
</script>
@endsection