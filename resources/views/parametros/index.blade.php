@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Parámetros"
        subtitle="Gestión de parámetros del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Parámetros', 'icon' => 'fa-cog', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @can('CREAR PARAMETRO')
                        <x-table-filters 
                            action="{{ route('parametro.store') }}"
                            method="POST"
                            title="Crear Parámetro"
                            icon="fa-plus-circle"
                        >
                            @include('parametros.create')
                        </x-table-filters>
                    @endcan

                    <x-data-table 
                        title="Lista de Parámetros"
                        searchable="true"
                        searchAction="{{ route('parametro.index') }}"
                        searchPlaceholder="Buscar parámetro..."
                        searchValue="{{ request('search') }}"
                        :columns="[['label' => '#', 'width' => '5%'], ['label' => 'Nombre', 'width' => '40%'], ['label' => 'Estado', 'width' => '20%'], ['label' => 'Acciones', 'width' => '35%', 'class' => 'text-center']]"
                        :pagination="$parametros->links()"
                    >
                        @forelse($parametros as $key => $parametro)
                            <tr>
                                <td>{{ $parametros->firstItem() + $key }}</td>
                                <td>{{ $parametro->name }}</td>
                                <td class="text-center">
                                    @if($parametro->status)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('VER PARAMETRO')
                                            <a href="{{ route('parametro.show', $parametro->id) }}" 
                                               class="btn btn-sm btn-light" 
                                               title="Ver detalles"
                                               style="margin-right: 2px;">
                                                <i class="fas fa-eye text-warning"></i>
                                            </a>
                                        @endcan
                                        @can('EDITAR PARAMETRO')
                                            <a href="{{ route('parametro.edit', $parametro->id) }}" 
                                               class="btn btn-sm btn-light" 
                                               title="Editar"
                                               style="margin-right: 2px;">
                                                <i class="fas fa-pencil-alt text-primary"></i>
                                            </a>
                                        @endcan
                                        @can('ELIMINAR PARAMETRO')
                                            <form action="{{ route('parametro.destroy', $parametro->id) }}" 
                                                  method="POST" 
                                                  style="display: inline-block;"
                                                  onsubmit="return confirm('¿Está seguro de eliminar este parámetro?')">
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
                                <td colspan="4" class="text-center">No hay parámetros registrados</td>
                            </tr>
                        @endforelse
                    </x-data-table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/parametros.js'])
@endsection
