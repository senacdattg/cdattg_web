@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Pisos"
        subtitle="Gestión de pisos del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Pisos', 'icon' => 'fa-cog', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card 
                        url="{{ route('piso.create') }}"
                        title="Crear Piso"
                        icon="fa-plus-circle"
                        permission="CREAR PISO"
                    />

                    <x-data-table 
                        title="Lista de Pisos"
                        searchable="true"
                        searchAction="{{ route('piso.index') }}"
                        searchPlaceholder="Buscar piso..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Nombre', 'width' => '20%'],
                            ['label' => 'Sede', 'width' => '20%'],
                            ['label' => 'Bloque', 'width' => '20%'],
                            ['label' => 'Estado', 'width' => '10%', 'class' => 'text-center'],
                            ['label' => 'Acciones', 'width' => '25%', 'class' => 'text-center']
                        ]"
                        :pagination="$pisos->links()"
                    >
                        @forelse($pisos as $key => $piso)
                            <tr>
                                <td>{{ $pisos->firstItem() + $key }}</td>
                                <td>{{ $piso->piso }}</td>
                                <td>{{ $piso->bloque->sede->sede ?? 'N/A' }}</td>
                                <td>{{ $piso->bloque->bloque ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if($piso->status === 1)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('EDITAR PISO')
                                            <form action="{{ route('piso.cambiarEstado', $piso->id) }}"
                                                method="POST" style="display: inline-block; margin-right: 2px;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-light" title="Cambiar Estado">
                                                    <i class="fas fa-sync text-success"></i>
                                                </button>
                                            </form>
                                        @endcan
                                        @can('VER PISO')
                                            <a href="{{ route('piso.show', $piso->id) }}"
                                                class="btn btn-sm btn-light" title="Ver"
                                                style="margin-right: 2px;">
                                                <i class="fas fa-eye text-warning"></i>
                                            </a>
                                        @endcan
                                        @can('EDITAR PISO')
                                            <a href="{{ route('piso.edit', $piso->id) }}"
                                                class="btn btn-sm btn-light" title="Editar"
                                                style="margin-right: 2px;">
                                                <i class="fas fa-pencil-alt text-primary"></i>
                                            </a>
                                        @endcan
                                        @can('ELIMINAR PISO')
                                            <form action="{{ route('piso.destroy', $piso->id) }}"
                                                method="POST" style="display: inline-block;"
                                                onsubmit="return confirm('¿Está seguro de eliminar este piso?')">
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
                                <td colspan="6" class="text-center">No hay pisos registrados</td>
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
