@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header icon="fa-cogs" title="Personas" subtitle="Gestión de personas del sistema" :breadcrumb="[
        ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
        ['label' => 'Personas', 'url' => route('personas.index'), 'icon' => 'fa-cog'],
        ['label' => 'Editar Persona', 'icon' => 'fa-edit', 'active' => true],
    ]" />
@endsection

@section('content')
    <section class="content mt-4 mb-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a class="btn btn-outline-secondary" href="{{ route('personas.show', $persona->id) }}">
                    <i class="fas fa-arrow-left mr-1"></i> Ver persona
                </a>
                <span class="text-muted small">Actualiza los datos y guarda los cambios.</span>
            </div>

            <form method="POST" action="{{ route('personas.update', $persona->id) }}" id="form-persona-edit"
                autocomplete="off">
                @csrf
                @method('PUT')

                @include('personas.partials.form', ['showCaracterizacion' => true, 'cardinales' => $cardinales])

                <div class="card shadow-sm border-0 mt-4 mb-5">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Última modificación: {{ $persona->updated_at?->diffForHumans() ?? 'Sin información' }}
                        </div>
                        <div>
                            <a href="{{ route('personas.show', $persona->id) }}" class="btn btn-outline-secondary mr-2">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Guardar cambios
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('js')
    @vite(['resources/js/parametros.js'])
    @vite(['resources/js/pages/formularios-select-dinamico.js'])
    @stack('js')
@endsection
