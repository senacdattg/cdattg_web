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
                        <i class="fas fa-fw fa-paint-brush text-white"></i>
                    </div>
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Fichas de formación</h1>
                        <p class="text-muted mb-0 font-weight-light">Gestión de fichas de formación</p>
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
                                <i class="fas fa-fw fa-paint-brush"></i> Fichas de formación
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    @if (session('error'))
        <div class="alert alert-danger" id="error-message">
            {{ session('error') }}
        </div>
        <script>
            setTimeout(function() {
                document.getElementById('error-message').style.display = 'none';
            }, 3000);
        </script>
    @endif

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                @foreach ($caracterizaciones as $caracterizacion)
                    <div class="col-md-4 mb-4">
                        <div
                            class="card h-100 shadow-sm border-0 rounded-lg overflow-hidden transition-all hover:shadow-lg">
                            <div class="card-header bg-gradient-primary text-white py-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-book fa-lg mr-2"></i>
                                    <h3 class="card-title mb-0 font-weight-bold">{{ $caracterizacion->ficha->ficha }} -
                                        {{ $caracterizacion->ficha->programaFormacion->nombre }}
                                    </h3>
                                </div>
                            </div>
                            <div class="card-body py-3">
                                @php
                                    $proximaClaseFormacion = $caracterizacion->obtenerProximaClase();
                                @endphp
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-tasks text-primary mr-2"></i>
                                        <h6 class="mb-0"><b>Competencia:</b></h6>
                                    </div>
                                    <p class="ml-4 text-muted">
                                        {{ $caracterizacion->ficha->programaFormacion->competenciaActual()->nombre ?? 'No asignada' }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-list-ol text-primary mr-2"></i>
                                        <h6 class="mb-0"><b>RAP:</b></h6>
                                    </div>
                                    <p class="ml-4 text-muted">
                                        {{ $caracterizacion->ficha->programaFormacion->competenciaActual()->rapActual()->nombre ?? 'No asignado' }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="far fa-clock text-primary mr-2"></i>
                                        <h6 class="mb-0"><b>Jornada:</b></h6>
                                    </div>
                                    <p class="ml-4 text-muted">{{ $caracterizacion->ficha->jornadaFormacion->jornada }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="far fa-calendar-alt text-primary mr-2"></i>
                                        <h6 class="mb-0"><b>Horario de formación:</b></h6>
                                        <p class="ml-1 mb-0 text-muted">
                                            {{ Carbon\Carbon::parse($proximaClaseFormacion['hora_inicio'])->format('g:i A') }}
                                            -
                                            {{ Carbon\Carbon::parse($proximaClaseFormacion['hora_fin'])->format('g:i A') }}
                                        </p>
                                    </div>
                                    <?php
                                        $dias = $caracterizacion->instructorFichaDias;
                                        $proximoDiaFormacion = $caracterizacion->obtenerProximaClase();
                                    ?>
                                    <div class="d-flex ml-4" style="gap: 0.5rem;">
                                        @foreach ($dias as $dia)
                                            <div class="border rounded text-center px-2 py-1"
                                                style="min-width: 60px; background: {{ ($dia->dia_id == $proximoDiaFormacion['dia_id']) ? '#007bff' : '#f8f9fa' }}; color: {{ ($dia->dia_id == $proximoDiaFormacion['dia_id']) ? '#fff' : '#6c757d' }};">
                                                {{ substr($diasFormacion[$dia->dia_id], 0, 3) }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                        <h6 class="mb-0"><b>Lugar de formación:</b></h6>
                                    </div>
                                    <div class="d-flex align-items-center ml-4 text-muted" style="gap: 0.5rem;">
                                        <span>Centro: {{ $caracterizacion->ficha->nombre ?? '' }}</span>
                                        <i class="fas fa-arrow-right"></i>
                                        <span>Sede: {{ $caracterizacion->sede->nombre ?? '' }}</span>
                                        <i class="fas fa-arrow-right"></i>
                                        <span>Bloque: {{ $caracterizacion->bloque->nombre ?? '' }}</span>
                                        <i class="fas fa-arrow-right"></i>
                                        <span>Piso: {{ $caracterizacion->piso->nombre ?? '' }}</span>
                                        <i class="fas fa-arrow-right"></i>
                                        <span>Ambiente: {{ $caracterizacion->ambiente->nombre ?? '' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="{{ route('asistence.caracterSelected', ['id' => $caracterizacion->id]) }}"
                                            class="btn btn-primary btn-block py-2 font-weight-bold">
                                            <i class="fas fa-clipboard-check mr-1"></i> Asistencia
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('asistence.weblist', ['ficha' => $caracterizacion->ficha->ficha, 'jornada' => $caracterizacion->ficha->jornadaFormacion->jornada]) }}"
                                            class="btn btn-success btn-block py-2 font-weight-bold">
                                            <i class="fas fa-newspaper mr-1"></i> Novedades
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @push('css')
                <style>
                    .card {
                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                        border: 1px solid rgba(0, 0, 0, 0.05);
                    }

                    .card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, .15) !important;
                    }

                    .bg-gradient-primary {
                        background: linear-gradient(45deg, #4e73df 0%, #224abe 100%) !important;
                    }

                    .rounded-lg {
                        border-radius: 0.75rem !important;
                    }
                </style>
            @endpush
        </div>
    </section>
    </div>
@endsection
