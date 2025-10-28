@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Sedes"
        subtitle="Gestión de sedes del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Sedes', 'icon' => 'fa-cog', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card 
                        url="{{ route('sede.create') }}"
                        title="Crear Sede"
                        icon="fa-plus-circle"
                        permission="CREAR SEDE"
                    />

                    <x-data-table 
                        title="Lista de Sedes"
                        searchable="true"
                        searchAction="{{ route('sede.index') }}"
                        searchPlaceholder="Buscar sede..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Sede', 'width' => '20%'],
                            ['label' => 'Dirección', 'width' => '25%'],
                            ['label' => 'Municipio', 'width' => '20%'],
                            ['label' => 'Estado', 'width' => '10%', 'class' => 'text-center'],
                            ['label' => 'Acciones', 'width' => '20%', 'class' => 'text-center']
                        ]"
                        :pagination="$sedes->links()"
                    >
                        @forelse($sedes as $key => $sede)
                            <tr>
                                <td>{{ $sedes->firstItem() + $key }}</td>
                                <td>{{ $sede->sede }}</td>
                                <td>{{ $sede->direccion }}</td>
                                <td>{{ $sede->municipio->municipio ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if($sede->status === 1)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('EDITAR SEDE')
                                            <form action="{{ route('sede.cambiarEstado', $sede->id) }}"
                                                method="POST" style="display: inline-block; margin-right: 2px;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-light" title="Cambiar Estado">
                                                    <i class="fas fa-sync text-success"></i>
                                                </button>
                                            </form>
                                        @endcan
                                        @can('VER SEDE')
                                            <a href="{{ route('sede.show', $sede->id) }}"
                                                class="btn btn-sm btn-light" title="Ver"
                                                style="margin-right: 2px;">
                                                <i class="fas fa-eye text-warning"></i>
                                            </a>
                                        @endcan
                                        @can('EDITAR SEDE')
                                            <a href="{{ route('sede.edit', $sede->id) }}"
                                                class="btn btn-sm btn-light" title="Editar"
                                                style="margin-right: 2px;">
                                                <i class="fas fa-pencil-alt text-primary"></i>
                                            </a>
                                        @endcan
                                        @can('ELIMINAR SEDE')
                                            <form action="{{ route('sede.destroy', $sede->id) }}"
                                                method="POST" style="display: inline-block;"
                                                onsubmit="return confirm('¿Está seguro de eliminar esta sede?')">
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
                                <td colspan="6" class="text-center">No hay sedes registradas</td>
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
