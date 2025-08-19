@extends('adminlte::page')

@section('title', 'Gestión de Aspirantes')

@section('content_header')
    <h1>Gestión de Aspirantes</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Panel de Gestión de Aspirantes</h3>
        </div>
        <div class="card-body">
            <p>Bienvenido al módulo de gestión de aspirantes. Aquí podrás administrar toda la información relacionada con los aspirantes del sistema.</p>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Esta sección está en desarrollo. Próximamente estarán disponibles todas las funcionalidades.
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Módulo de gestión de aspirantes cargado');
    </script>
@stop
