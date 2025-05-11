@extends('adminlte::page')
@section('content')
    <section class="content mt-3">
        <div class="card">
            <div class="card-header">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a class="btn btn-warning btn-sm" href="{{ route('asistence.web') }}">
                                <i class="fas fa-arrow-left"></i>
                                Volver
                            </a>
                        </div>
                        <div class="col-md-6">
                            <h3 class="text-center">N° Ficha: {{$caracterizacion->ficha->ficha}}</h3>
                        </div>
                    </div>
                </div>
            </div>
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
                <div class="qr-result" id="qr-result"></div>
                <h3>Tomar asistencia</h3>
                <div style="display: flex; justify-content: center;">
                    <div id="qr-lector" style="width: 40%; margin-bottom: 3%;"></div>
                </div>
                <form id="asistencia-form" action="{{route('asistence.store')}}" method="POST">
                    @csrf
                    <input type="hidden" name="caracterizacion_id" value="{{$caracterizacion->id}}">
                    <input type="hidden" name="asignacion_de_formacion_id" value="{{$asignacionDeFormacion->id}}">
                    <div class="card">
                        <ul name="asistencia_web" id="asistencia-list"></ul>
                    </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">Guardar Asistencia</button>
                    </div>
                    <div class="col-md-6 text-right">
                        <a class="btn btn-danger" href="{{ route('asistence.exitFormation', ['caracterizacion_id' => $caracterizacion->id]) }}">
                            Finalizar Formación
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
    function domIsReady(fn) {
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(fn, 1);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }

    domIsReady(() => {
        let thisQr = document.getElementById('qr-result');
        let asistenciaList = document.getElementById('asistencia-list');
        let lastResult, countResults = 0;

        function onScanSuccess(decodedText, decodedResult) {
            if (decodedText !== lastResult) {
                ++countResults;
                lastResult = decodedText;

                // Dividir el texto escaneado por el delimitador "|"
                let parts = decodedText.split('|');
                let nombres = parts[0] ? parts[0].trim() : '';
                let apellidos = parts[1] ? parts[1].trim() : '';
                let identificacion = parts[2] ? parts[2].trim() : '';
                let horaIngreso = new Date().toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                // Mostrar los valores en la lista
                let listItem = document.createElement('li');
                listItem.innerHTML = `
                    <strong>Nombres:</strong> ${nombres} <br>
                    <strong>Apellidos:</strong> ${apellidos} <br>
                    <strong>Identificación:</strong> ${identificacion}
                    <br><strong>Hora de Ingreso:</strong> ${horaIngreso}
                `;
                asistenciaList.appendChild(listItem);

                // Añadir un campo oculto al formulario con el valor escaneado y la hora de ingreso
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'asistencia[]';
                input.value = JSON.stringify({
                    nombres: nombres,
                    apellidos: apellidos,
                    identificacion: identificacion,
                    hora_ingreso: horaIngreso
                });
                document.getElementById('asistencia-form').appendChild(input);
            }
        }

        let htmlScan = new Html5QrcodeScanner("qr-lector", { fps: 10, qrbox: 250 });
        htmlScan.render(onScanSuccess);
    });
</script>
@endsection
