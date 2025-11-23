@extends('adminlte::page')

@section('title', 'Gestión de Aspirantes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-users mr-2 text-primary"></i>Gestión de Aspirantes</h1>
            <p class="text-muted mb-0">Administre los aspirantes a programas de formación complementaria</p>
        </div>
    </div>
@stop

@section('content')
    @php
        $totalProgramas = $programas->count();
        $totalAspirantes = $programas->sum('aspirantes_count');
        $programasConAspirantes = $programas->filter(fn($p) => $p->aspirantes_count > 0)->count();
    @endphp

    <!-- Estadísticas Resumen -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Programas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProgramas }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-left-success shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Aspirantes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAspirantes }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Con Aspirantes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $programasConAspirantes }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de Programas -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <h5 class="card-title mb-0">
                <i class="fas fa-list mr-2 text-primary"></i>Programas Complementarios
            </h5>
        </div>
        <div class="card-body">
            @forelse($programas as $programa)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card h-100 shadow-sm border-0 hover-shadow" style="transition: all 0.3s ease;">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <!-- Icono y Badge -->
                                    <div class="col-md-1 text-center mb-3 mb-md-0">
                                        <div class="mb-3">
                                            <i class="{{ $programa->icono ?? 'fas fa-graduation-cap' }} fa-3x text-primary"></i>
                                        </div>
                                        <span class="badge badge-pill {{ $programa->badge_class ?? 'bg-secondary' }} px-3 py-2">
                                            {{ $programa->estado_label ?? 'N/A' }}
                                        </span>
                                    </div>

                                    <!-- Información Principal -->
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <h4 class="card-title font-weight-bold text-dark mb-2">
                                            {{ $programa->nombre }}
                                        </h4>
                                        <p class="text-muted mb-3">
                                            {{ Str::limit($programa->descripcion ?? 'Sin descripción', 150) }}
                                        </p>
                                        <div class="row">
                                            @if(isset($programa->modalidad) && $programa->modalidad)
                                                <div class="col-auto mb-2">
                                                    <span class="badge badge-light border">
                                                        <i class="fas fa-chalkboard-teacher text-primary mr-1"></i>
                                                        {{ optional($programa->modalidad->parametro)->name ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            @endif
                                            @if(isset($programa->jornada) && $programa->jornada)
                                                <div class="col-auto mb-2">
                                                    <span class="badge badge-light border">
                                                        <i class="fas fa-clock text-primary mr-1"></i>
                                                        {{ $programa->jornada->jornada ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Estadísticas y Acción -->
                                    <div class="col-md-5">
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <div class="info-box mb-3 bg-light">
                                                    <span class="info-box-icon bg-primary elevation-1">
                                                        <i class="fas fa-users"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Aspirantes Registrados</span>
                                                        <span class="info-box-number">{{ $programa->aspirantes_count ?? 0 }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <a href="{{ route('aspirantes.programa', ['programa' => $programa->id]) }}"
                                                class="btn btn-primary btn-block btn-lg shadow-sm">
                                                <i class="fas fa-users mr-2"></i>Ver Aspirantes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-inbox fa-5x text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted font-weight-bold mb-2">No hay programas complementarios disponibles</h5>
                    <p class="text-muted">No se encontraron programas complementarios en el sistema.</p>
                </div>
            @endforelse
        </div>
    </div>
@stop

@section('css')
<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }

    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }

    .border-left-info {
        border-left: 4px solid #17a2b8 !important;
    }

    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-2px);
    }

    .info-box {
        display: block;
        min-height: 80px;
        background: #ffffff;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        border-radius: 2px;
        margin-bottom: 15px;
    }

    .info-box-icon {
        border-top-left-radius: 2px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 2px;
        display: block;
        float: left;
        height: 80px;
        width: 80px;
        text-align: center;
        font-size: 45px;
        line-height: 80px;
        background: rgba(0, 0, 0, 0.2);
    }

    .info-box-content {
        padding: 5px 10px;
        margin-left: 80px;
    }

    .info-box-text {
        text-transform: uppercase;
        font-weight: 600;
        font-size: 13px;
    }

    .info-box-number {
        display: block;
        font-weight: bold;
        font-size: 18px;
    }

    .text-gray-800 {
        color: #5a5c69 !important;
    }
</style>
@stop

@section('js')
    <script>
        console.log('Módulo de gestión de aspirantes cargado');
    </script>
@stop
