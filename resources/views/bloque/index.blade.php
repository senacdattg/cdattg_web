@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Bloques"
        subtitle="Gestión de bloques del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Bloques', 'icon' => 'fa-cog', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card 
                        url="{{ route('bloque.create') }}"
                        title="Crear Bloque"
                        icon="fa-plus-circle"
                        permission="CREAR BLOQUE"
                    />

                    <x-data-table 
                        title="Lista de Bloques"
                        searchable="true"
                        searchAction="{{ route('bloque.index') }}"
                        searchPlaceholder="Buscar bloque..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Nombre', 'width' => '25%'],
                            ['label' => 'Sede', 'width' => '30%'],
                            ['label' => 'Estado', 'width' => '15%', 'class' => 'text-center'],
                            ['label' => 'Acciones', 'width' => '25%', 'class' => 'text-center']
                        ]"
                        :pagination="$bloques->links()"
                    >
                        @forelse($bloques as $key => $bloque)
                            <tr>
                                <td>{{ $bloques->firstItem() + $key }}</td>
                                <td>{{ $bloque->bloque }}</td>
                                <td>{{ $bloque->sede->sede ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if($bloque->status === 1)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('EDITAR BLOQUE')
                                            <form action="{{ route('bloque.cambiarEstado', $bloque->id) }}"
                                                method="POST" style="display: inline-block; margin-right: 2px;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-light" title="Cambiar Estado">
                                                    <i class="fas fa-sync text-success"></i>
                                                </button>
                                            </form>
                                        @endcan
                                        @can('VER BLOQUE')
                                            <a href="{{ route('bloque.show', $bloque->id) }}"
                                                class="btn btn-sm btn-light" title="Ver"
                                                style="margin-right: 2px;">
                                                <i class="fas fa-eye text-warning"></i>
                                            </a>
                                        @endcan
                                        @can('EDITAR BLOQUE')
                                            <a href="{{ route('bloque.edit', $bloque->id) }}"
                                                class="btn btn-sm btn-light" title="Editar"
                                                style="margin-right: 2px;">
                                                <i class="fas fa-pencil-alt text-primary"></i>
                                            </a>
                                        @endcan
                                        @can('ELIMINAR BLOQUE')
                                            <form action="{{ route('bloque.destroy', $bloque->id) }}"
                                                method="POST" style="display: inline-block;"
                                                onsubmit="return confirm('¿Está seguro de eliminar este bloque?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light" title="Eliminar">
                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay bloques registrados</td>
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
