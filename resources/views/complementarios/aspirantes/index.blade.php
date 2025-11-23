@extends('adminlte::page')

@section('title', 'Gestión de Aspirantes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Gestión de Aspirantes</h1>
            <p class="text-muted">Administre los aspirantes a programas de formación complementaria</p>
        </div>
    </div>
@stop

@section('content')
    <link rel="stylesheet" href="{{ asset('resources/css/complementario/gestion_aspirantes.css') }}">

    <!-- Main Content -->
    <div class="container-fluid px-4 py-4">
        <div class="row">
            @forelse($programas as $programa)
                <div class="col-md-4 mb-4">
                    <div class="course-card">
                        <div class="card-icon">
                            <i class="{{ $programa->icono }}"></i>
                        </div>
                        <div class="card-title">{{ $programa->nombre }}</div>
                        <span class="badge {{ $programa->badge_class }} mb-2">{{ $programa->estado_label }}</span>
                        <p>{{ $programa->descripcion }}</p>
                        <p><strong>Aspirantes:</strong> {{ $programa->aspirantes_count }}</p>
                        <a href="{{ route('aspirantes.programa', ['programa' => $programa->id]) }}"
                            class="btn btn-primary w-100">
                            <i class="fas fa-users"></i> Ver Aspirantes
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h5>No hay programas complementarios disponibles</h5>
                        <p>No se encontraron programas complementarios en el sistema.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
    <script>
        console.log('Módulo de gestión de aspirantes cargado');
    </script>
@stop

