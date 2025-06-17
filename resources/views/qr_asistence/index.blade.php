@extends('adminlte::page')
@section('css')
    @vite(['resources/css/temas.css'])
@endsection

@section('content_header')
    <section class="content-header dashboard-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 48px; height: 48px;">
                        <i class="fas fa-fw fa-qrcode text-white"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Asistencia QR</h1>
                        <p class="text-muted mb-0 font-weight-light">
                            {{ $fichaCaracterizacion->programaFormacion->nombre ?? 'Programa no disponible' }}</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('verificarLogin') }}" class="link_right_header">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-fw fa-qrcode"></i> Asistencia QR
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm mb-4 no-hover">
                        <div class="card-header bg-white py-3 d-flex align-items-center">
                            <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                                <i class="fas fa-info-circle mr-2"></i> Información del Programa
                            </h5>
                        </div>
                        <div class="card-body bg-light">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="info-box bg-white shadow-sm rounded">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                                            style="width: 48px; height: 48px;">
                                            <i class="fas fa-fw fa-book text-white"></i>
                                        </div>
                                        <div class="info-box-content">
                                            <span class="info-box-text text-secondary">N° Ficha</span>
                                            <span class="info-box-number fw-bold">{{ $fichaCaracterizacion->ficha }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-white shadow-sm rounded">
                                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center mr-3"
                                            style="width: 48px; height: 48px;">
                                            <i class="fas fa-clock text-white"></i>
                                        </div>
                                        <div class="info-box-content">
                                            <span class="info-box-text text-secondary">Jornada</span>
                                            <span class="info-box-number fw-bold">
                                                {{ $fichaCaracterizacion->jornadaFormacion->jornada }}
                                            </span>
                                            @if ($horarioHoy)
                                                <span class="text-muted">
                                                    ({{ \Carbon\Carbon::parse($horarioHoy->hora_inicio)->format('h:i A') }}
                                                    - {{ \Carbon\Carbon::parse($horarioHoy->hora_fin)->format('h:i A') }})
                                                </span>
                                            @else
                                                <span class="text-danger">
                                                    (No hay clases hoy)
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-white shadow-sm rounded">
                                        <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center mr-3"
                                            style="width: 48px; height: 48px;">
                                            <i class="fas fa-user-tie text-white"></i>
                                        </div>
                                        <div class="info-box-content">
                                            <span class="info-box-text text-secondary">Instructor líder</span>
                                            <span class="info-box-number fw-bold">
                                                @if ($fichaCaracterizacion->instructor && $fichaCaracterizacion->instructor->persona)
                                                    {{ $fichaCaracterizacion->instructor->persona->getNombreCompletoAttribute() }}
                                                @else
                                                    No asignado
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Añadir estilos CSS personalizados -->
                    <style>
                        .info-box {
                            padding: 1rem;
                            transition: transform 0.3s ease;
                            background-color: #fff;
                        }

                        .info-box:hover {
                            transform: translateY(-5px);
                        }

                        .info-box-icon {
                            width: 60px;
                            height: 60px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-right: 1rem;
                            border-radius: 9999px;
                            /* Asegura un círculo perfecto */
                            aspect-ratio: 1;
                            min-width: 60px;
                            min-height: 60px;
                        }

                        .info-box-icon i {
                            font-size: 1.5rem;
                            color: #fff;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            width: 100%;
                            height: 100%;
                        }

                        @media (max-width: 768px) {
                            .info-box-icon {
                                width: 48px;
                                height: 48px;
                                min-width: 48px;
                                min-height: 48px;
                            }

                            .info-box-icon i {
                                font-size: 1.2rem;
                            }
                        }

                        @media (max-width: 576px) {
                            .info-box-icon {
                                width: 40px;
                                height: 40px;
                                min-width: 40px;
                                min-height: 40px;
                            }

                            .info-box-icon i {
                                font-size: 1rem;
                            }
                        }

                        .bg-success {
                            background-color: #28a745 !important;
                        }

                        .bg-warning {
                            background-color: #ffc107 !important;
                        }

                        .bg-info {
                            background-color: #17a2b8 !important;
                        }
                    </style>

                    @if ($horarioHoy)
                        <div class="card shadow-sm mb-4 no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center">
                                <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                                    <i class="fas fa-qrcode mr-2"></i> Escanear QR
                                </h5>
                                <div class="status-indicator align-items-center">
                                    <i class="fas fa-circle text-success me-1" style="font-size: 0.75rem;"></i>
                                    <span class="text-muted small">Escáner activo</span>
                                </div>
                            </div>
                            <div class="card-body mt-1">
                                <!-- Mensajes de alerta -->
                                <div class="qr-feedback-messages mb-4"></div>

                                <div class="d-flex justify-content-center mb-4">
                                    <form id="asistencia-form" action="{{route('asistence.store')}}" method="POST">
                                        @csrf
                                        <input type="hidden" name="caracterizacion_id" id="ficha_caracterizacion_id" value="{{$fichaCaracterizacion->id}}">
                                    </form>
                                    <div class="qr-scanner-container rounded-lg border border-primary shadow-sm p-3" style="width: 100%; max-width: 350px;">
                                        <div id="qr-lector" class="rounded-lg border border-primary shadow-sm p-3" style="width: 100%; max-width: 350px; position: relative;">
                                            <!-- Recuadro guía para el usuario -->
                                            <div class="qr-frame"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="qr-scanner-footer mt-3">
                                    <div class="text-center text-secondary mb-3">
                                        <p class="mb-0">Posicione el código QR en el recuadro</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card shadow-sm mb-4 no-hover">
                            <div class="card-header bg-white py-3 d-flex align-items-center">
                                <h5 class="card-title m-0 font-weight-bold text-warning d-flex align-items-center flex-grow-1">
                                    <i class="fas fa-exclamation-triangle mr-2"></i> No hay clases programadas
                                </h5>
                            </div>
                            <div class="card-body text-center py-5">
                                <div class="text-warning mb-3">
                                    <i class="fas fa-calendar-times fa-3x"></i>
                                </div>
                                <h6 class="text-muted">No hay clases programadas para hoy</h6>
                                <p class="text-muted mb-0">El escáner QR estará disponible cuando haya clases programadas.</p>
                            </div>
                        </div>
                    @endif

                    <style>
                        /* Estilos del escáner */
                        .qr-scanner-container {
                            background: #fff;
                            transition: all 0.3s ease;
                            position: relative;
                        }

                        .qr-scanner-container:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
                        }

                        /* Recuadro guía para el usuario */
                        .qr-frame {
                            position: absolute;
                            top: 50%;
                            left: 50%;
                            width: 200px;
                            height: 200px;
                            transform: translate(-50%, -50%);
                            border: 2px solid rgba(255, 255, 255, 0.9);
                            border-radius: 8px;
                            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
                        }

                        /* Responsividad */
                        @media (max-width: 768px) {
                            .qr-scanner-container {
                                padding: 0.75rem;
                            }

                            .qr-frame {
                                width: 160px;
                                height: 160px;
                            }
                        }
                    </style>

                    <div class="card shadow-sm no-hover">
                        <div class="card-header bg-white py-3 d-flex align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary d-flex flex-grow-1">
                                <i class="fas fa-users mr-2"></i> Listado de Aprendices
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-borderless table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="px-4 py-3">#</th>
                                            <th class="px-4 py-3">Documento</th>
                                            <th class="px-4 py-3">Nombre Completo</th> {{-- Cambiado para reflejar el nombre completo --}}
                                            <th class="px-4 py-3 text-center">Hora Ingreso</th>
                                            <th class="px-4 py-3 text-center">Hora Salida</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($aprendizPersonaConAsistencia as $index => $aprendiz)
                                            <tr data-documento="{{ $aprendiz->numero_documento }}">
                                                <td class="px-4">{{ $index + 1 }}</td>
                                                <td class="px-4 font-weight-medium">{{ $aprendiz->numero_documento }}</td>
                                                <td class="px-4 font-weight-medium">
                                                    {{ $aprendiz->getNombreCompletoAttribute() }} {{-- Usar el accesor --}}
                                                </td>
                                                <td class="px-4 text-center hora-ingreso-cell">
                                                    @if ($aprendiz->asistenciaHoy && $aprendiz->asistenciaHoy->formatted_hora_ingreso)
                                                        <span class="text-success">{{ $aprendiz->asistenciaHoy->formatted_hora_ingreso }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 text-center hora-salida-cell">
                                                    @if ($aprendiz->asistenciaHoy && $aprendiz->asistenciaHoy->formatted_hora_salida)
                                                        <span class="text-info">{{ $aprendiz->asistenciaHoy->formatted_hora_salida }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <img src="{{ asset('img/no-data.svg') }}" alt="No data"
                                                        style="width: 120px" class="mb-3">
                                                    <p class="text-muted">No hay aprendices registrados</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('css')
    <style>
        .table thead th {
            font-weight: 600;
            color: #6c757d;
        }

        .table tbody td {
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.075);
        }

        .font-weight-medium {
            font-weight: 500;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-info {
            color: #17a2b8 !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .qr-feedback-messages .alert {
            margin-bottom: 1rem;
        }
    </style>
@endsection

@section('js')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function cuandoElDocumentoEsteListo(funcionAEjecutar) {
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                funcionAEjecutar();
            } else {
                document.addEventListener('DOMContentLoaded', funcionAEjecutar);
            }
        }

        cuandoElDocumentoEsteListo(function() {
            // Solo inicializar el escáner si hay clases programadas
            @if ($horarioHoy)
                // Usa Html5QrcodeScanner que maneja la UI automáticamente
                const html5QrcodeScanner = new Html5QrcodeScanner(
                    "qr-lector", {
                        fps: 25,
                        qrbox: 180,
                        aspectRatio: 1,
                        // Si tienes múltiples cámaras y quieres especificar una
                        // videoConstraints: { facingMode: { exact: "environment" } }
                    },
                    /* verbose= */
                    false
                );

                const qrFeedbackMessages = document.querySelector('.qr-feedback-messages');
                // Asegúrate de que 'caracterizacion_id' es el ID de la ficha actual
                const fichaId = document.querySelector('input[name="caracterizacion_id"]').value;

                function showFeedback(message, type = 'info') {
                    const alertHtml = `
                        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                            ${message}
                        </div>
                    `;
                    qrFeedbackMessages.innerHTML = alertHtml;
                    setTimeout(() => {
                        const alertElement = qrFeedbackMessages.querySelector('.alert');
                        if (alertElement) {
                            alertElement.classList.remove('show');
                            alertElement.classList.add('fade');
                            setTimeout(() => alertElement.remove(), 150);
                        }
                    }, 6000);
                }

                function updateLearnerRow(documento, horaIngreso, horaSalida = null) {
                    const row = document.querySelector(`tr[data-documento="${documento}"]`);
                    if (row) {
                        const horaIngresoCell = row.querySelector('.hora-ingreso-cell');
                        const horaSalidaCell = row.querySelector('.hora-salida-cell');

                        if (horaIngresoCell && horaIngreso) {
                            horaIngresoCell.innerHTML = `<span class="text-success">${horaIngreso}</span>`;
                        }
                        if (horaSalidaCell && horaSalida) {
                            horaSalidaCell.innerHTML = `<span class="text-info">${horaSalida}</span>`;
                        }
                    }
                }

                // Función que se ejecuta cuando el QR es escaneado con éxito
                const onScanSuccess = (decodedText, decodedResult) => {
                    // Al escanear con éxito, detenemos el escáner para evitar múltiples lecturas rápidas
                    html5QrcodeScanner.pause(); // Pausa el escáner en lugar de detenerlo completamente

                    let numeroIdentificacion = decodedText.trim();

                    // Verificar si el texto contiene el formato con separadores |
                    if (numeroIdentificacion.includes('|')) {
                        const partes = numeroIdentificacion.split('|');
                        // Tomar el tercer elemento (índice 2) que es el número de documento
                        if (partes.length >= 3) {
                            numeroIdentificacion = partes[2].trim();
                        }
                    }

                    fetch('{{ route('api.verifyDocument') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                numero_documento: numeroIdentificacion,
                                ficha_id: fichaId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'registered') {
                                showFeedback(data.message, 'success');
                                updateLearnerRow(data.aprendiz_data.numero_documento, data.hora_ingreso);
                            } else if (data.status === 'exit_registered') {
                                showFeedback(data.message, 'success');
                                updateLearnerRow(data.aprendiz_data.numero_documento, data.hora_ingreso, data.hora_salida);
                            } else if (data.status === 'already_registered' || data.status === 'attendance_complete') {
                                showFeedback(data.message, 'info');
                                updateLearnerRow(data.aprendiz_data.numero_documento, data.hora_ingreso, data.hora_salida || null);
                            } else if (data.status === 'not_found' || data.status === 'not_a_learner' || data
                                .status === 'not_in_ficha' || data.status === 'not_assigned_instructor') {
                                showFeedback(data.message, 'danger');
                            } else {
                                showFeedback(data.message || 'Error desconocido al procesar el QR.', 'danger');
                            }
                        })
                        .catch(error => {
                            console.error('Error de comunicación con el servidor:', error);
                            showFeedback('Error de comunicación con el servidor.', 'danger');
                        })
                        .finally(() => {
                            // Reanudamos el escáner después de un breve retraso para permitir la visualización del feedback
                            setTimeout(() => {
                                html5QrcodeScanner.resume();
                            }, 2000);
                        });
                };

                // Función que se ejecuta si hay un error en el escaneo (normalmente inofensivo)
                const onScanFailure = (error) => {
                    // Puedes imprimir el error en la consola para depuración, pero no lo muestres al usuario
                    // console.warn(`Error de escaneo (normalmente inofensivo): ${error}`);
                };

                // Inicia el escáner
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            @endif
        });
    </script>
@endsection
