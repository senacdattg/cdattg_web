@extends('adminlte::page')

@section('css')
    @vite(['resources/css/Asistencia/caracter_selecter.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-home" 
        title="Fichas de formación"
        subtitle="Gestión de fichas de formación"
        :breadcrumb="[['label' => 'Inicio', 'url' => '{{ route('verificarLogin') }}', 'icon' => 'fa-home'], ['label' => 'Fichas de formación', 'icon' => 'fa-fw fa-paint-brush', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                @foreach ($instructorFicha as $caracterizacion)
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
                                        <i class="fas fa-graduation-cap text-primary mr-2"></i>
                                        <h6 class="mb-0"><b>Modalidad:</b></h6>
                                    </div>
                                    <div class="d-flex align-items-center ml-4 text-muted">
                                        <span>{{ $caracterizacion->ficha->modalidadFormacion->name ?? 'No especificada' }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="far fa-sun text-primary mr-2"></i>
                                            <h6 class="mb-0"><b>Jornada:</b></h6>
                                        </div>
                                        <p class="ml-4 text-muted">{{ $caracterizacion->ficha->jornadaFormacion->jornada }}
                                        </p>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="far fa-clock text-primary mr-2"></i>
                                            <h6 class="mb-0"><b>Horario de formación:</b></h6>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <p class="ml-4 mb-0 text-muted">
                                                {{ Carbon\Carbon::parse($proximaClaseFormacion['hora_inicio'])->format('g:i A') }}
                                                -
                                                {{ Carbon\Carbon::parse($proximaClaseFormacion['hora_fin'])->format('g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="far fa-calendar-alt text-primary mr-2"></i>
                                        <h6 class="mb-0"><b>Dias de formación:</b></h6>
                                    </div>
                                    @php
                                        $dias = $caracterizacion->instructorFichaDias;
                                    @endphp
                                    <div class="d-flex ml-4" style="gap: 0.5rem;">
                                        @foreach ($dias as $dia)
                                            <div class="border rounded text-center px-2 py-1"
                                                style="min-width: 60px; background: {{ $dia->dia_id == $proximaClaseFormacion['dia_id'] ? '#007bff' : '#f8f9fa' }}; color: {{ $dia->dia_id == $proximaClaseFormacion['dia_id'] ? '#fff' : '#6c757d' }};">
                                                {{ substr($diasFormacion[$dia->dia_id - 12]['name'], 0, 3) }}
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
                                        <span>{{ $caracterizacion->ficha->ambiente->piso->bloque->sede->sede ?? '' }}</span>
                                        <i class="fas fa-arrow-right"></i>
                                        <span>{{ $caracterizacion->ficha->ambiente->piso->bloque->bloque ?? '' }}</span>
                                        <i class="fas fa-arrow-right"></i>
                                        <span>{{ $caracterizacion->ficha->ambiente->piso->piso ?? '' }}</span>
                                        <i class="fas fa-arrow-right"></i>
                                        <span>{{ $caracterizacion->ficha->ambiente->title ?? '' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="{{ route('registro-actividades.index', ['caracterizacion' => $caracterizacion]) }}"
                                            class="btn btn-primary btn-block py-2 font-weight-bold">
                                            <i class="fas fa-clipboard-check mr-1"></i> Actividades
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
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection
