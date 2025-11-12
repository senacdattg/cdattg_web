@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header icon="fa-cogs" title="Personas" subtitle="Gestión de personas del sistema" :breadcrumb="[
        ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
        ['label' => 'Personas', 'url' => route('personas.index'), 'icon' => 'fa-cog'],
        ['label' => 'Crear Persona', 'icon' => 'fa-plus', 'active' => true],
    ]" />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="mb-3">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('personas.index') }}" title="Volver">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>
            </div>

            <div class="card shadow-sm no-hover">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-plus mr-2"></i>Registro de Persona
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('personas.store') }}" autocomplete="off">
                        @csrf

                        @include('personas.partials.form', ['showCaracterizacion' => true, 'cardinales' => $cardinales])

                        <div class="col-12">
                            <hr class="mt-4">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('personas.index') }}" class="btn btn-outline-secondary btn-sm mx-1">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-outline-success btn-sm mx-1">
                                    <i class="fas fa-save mr-1"></i> Registrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    @vite(['resources/js/parametros.js'])
    @vite(['resources/js/pages/formularios-select-dinamico.js'])
    @stack('js')
@endsection
