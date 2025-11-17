@extends('adminlte::page')

@section('css')
    @vite(['resources/css/temas.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-home" 
        title="Temas"
        subtitle="Gestión de temas del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Temas', 'icon' => 'fa-fw fa-paint-brush', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-12">
                    @can('CREAR TEMA')
                        <x-table-filters 
                            action="{{ route('tema.store') }}"
                            method="POST"
                            title="Crear Tema"
                            icon="fa-plus-circle"
                        >
                            @include('temas.create')
                        </x-table-filters>
                    @endcan
                    <x-data-table 
                        title="Lista de Temas"
                        searchable="true"
                        searchAction="{{ route('tema.index') }}"
                        searchPlaceholder="Buscar tema..."
                        searchValue="{{ request('search') }}"
                        :columns="[['label' => '#', 'width' => '5%'], ['label' => 'Nombre', 'width' => '25%'], ['label' => 'Estado', 'width' => '15%', 'class' => 'text-center'], ['label' => 'Parámetros', 'width' => '40%', 'class' => 'text-center'], ['label' => 'Acciones', 'width' => '25%', 'class' => 'text-center']]"
                        :pagination="$temas->links()"
                    >
                        @forelse($temas as $key => $tema)
                            <tr>
                                <td>{{ $temas->firstItem() + $key }}</td>
                                <td>{{ $tema->name }}</td>
                                <td class="text-center">
                                    @if($tema->status)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info badge-pill">
                                        {{ $tema->parametros->count() }} parámetros
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('VER TEMA')
                                            <a href="{{ route('tema.show', $tema->id) }}" 
                                               class="btn btn-sm btn-light" 
                                               title="Ver detalles"
                                               style="margin-right: 2px;">
                                                <i class="fas fa-eye text-warning"></i>
                                            </a>
                                        @endcan
                                        @can('EDITAR TEMA')
                                            <a href="{{ route('tema.edit', $tema->id) }}" 
                                               class="btn btn-sm btn-light" 
                                               title="Editar"
                                               style="margin-right: 2px;">
                                                <i class="fas fa-pencil-alt text-primary"></i>
                                            </a>
                                        @endcan
                                        @can('ELIMINAR TEMA')
                                            <form action="{{ route('tema.destroy', $tema->id) }}" 
                                                  method="POST" 
                                                  style="display: inline-block;"
                                                  onsubmit="return confirm('¿Está seguro de eliminar este tema?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-light" 
                                                        title="Eliminar">
                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay temas registrados</td>
                            </tr>
                        @endforelse
                    </x-data-table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('js')
    <script src="{{ asset('vendor/chart.js/Chart.bundle.min.js') }}"></script>
    @vite(['resources/js/parametros.js'])
@endsection
