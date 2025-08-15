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
    <section class="content-mt4">
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
                            <div class="mb-3">
                                <a href="{{ route('registro-actividades.index', ['caracterizacion' => $caracterizacion]) }}" class="btn btn-primary">
                                    Ir a la página de selección de caracterización
                                </a>
                            </div>
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
                    <div class="card shadow-sm mb-4 no-hover" id="qr-scanner-card" style="display: none;">
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
                            <div class="qr-feedback-messages mb-4"></div>
                            <div class="d-flex justify-content-center mb-4">
                                <form id="asistencia-form" action="{{ route('asistence.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="caracterizacion_id" id="ficha_caracterizacion_id"
                                        value="{{ $fichaCaracterizacion->id }}">
                                </form>
                                <div class="qr-scanner-container rounded-lg border border-primary shadow-sm p-3"
                                    style="width: 100%; max-width: 350px;">
                                    <div id="qr-lector" class="rounded-lg border border-primary shadow-sm p-3"
                                        style="width: 100%; max-width: 350px; position: relative;">
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

                    <div class="card shadow-sm mb-4 no-hover" id="no-classes-card" style="display: none;">
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
                                            <th class="px-4 py-3">Nombre del aprendiz</th>
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
                                                        <span
                                                            class="text-success">{{ $aprendiz->asistenciaHoy->formatted_hora_ingreso }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 text-center hora-salida-cell">
                                                    @if ($aprendiz->asistenciaHoy && $aprendiz->asistenciaHoy->formatted_hora_salida)
                                                        <span
                                                            class="text-info">{{ $aprendiz->asistenciaHoy->formatted_hora_salida }}</span>
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
                    <!-- Botón para finalizar asistencia, redirige a caracter_selected -->
                    <a href="{{ route('asistence.web') }}" class="btn btn-success btn-block py-2 font-weight-bold mb-3">
                        <i class="fas fa-check-circle mr-1"></i> Finalizar asistencia
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="{{ asset('js/websocket-handler.js') }}"></script>
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        window.apiVerifyDocumentRoute = '{{ route('api.verifyDocument') }}';
        window.horarioHoy = @json($horarioHoy);

        // Mostrar el div del escáner si hay clases programadas
        const qrScannerCard = document.getElementById('qr-scanner-card');
        if (qrScannerCard) {
            qrScannerCard.style.display = 'block';
        }
    </script>
    @vite(['resources/js/Asistencia/index-qr.js'])
@endsection
