@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Centros de Formación"
        subtitle="Gestión de centros de formación"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Centros de Formación', 'icon' => 'fa-cog', 'active' => true]]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card 
                        url="{{ route('centros.create') }}"
                        title="Crear Centro de Formación"
                        icon="fa-plus-circle"
                        permission="CREAR CENTRO DE FORMACION"
                    />

                    <x-data-table 
                        title="Lista de Centros de Formación"
                        searchable="true"
                        searchAction="{{ route('centros.index') }}"
                        searchPlaceholder="Buscar centro..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Regional', 'width' => '30%'],
                            ['label' => 'Centro de Formación', 'width' => '30%'],
                            ['label' => 'Estado', 'width' => '15%', 'class' => 'text-center'],
                            ['label' => 'Acciones', 'width' => '20%', 'class' => 'text-center']
                        ]"
                        :pagination="$centros->links()"
                    >
                        @forelse($centros as $key => $centro)
                            <tr>
                                <td>{{ $centros->firstItem() + $key }}</td>
                                <td>{{ $centro->regional->nombre ?? 'N/A' }}</td>
                                <td>{{ $centro->nombre }}</td>
                                <td class="text-center">
                                    @if($centro->status)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('EDITAR CENTRO DE FORMACION')
                                            <form action="{{ route('centro.cambiarEstado', $centro->id) }}"
                                                method="POST" style="display: inline-block; margin-right: 2px;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-light" title="Cambiar Estado">
                                                    <i class="fas fa-sync text-success"></i>
                                                </button>
                                            </form>
                                        @endcan
                                        @can('VER CENTRO DE FORMACION')
                                            <a href="{{ route('centros.show', $centro->id) }}"
                                                class="btn btn-sm btn-light" title="Ver"
                                                style="margin-right: 2px;">
                                                <i class="fas fa-eye text-warning"></i>
                                            </a>
                                        @endcan
                                        @can('EDITAR CENTRO DE FORMACION')
                                            <a href="{{ route('centros.edit', $centro->id) }}"
                                                class="btn btn-sm btn-light" title="Editar"
                                                style="margin-right: 2px;">
                                                <i class="fas fa-pencil-alt text-primary"></i>
                                            </a>
                                        @endcan
                                        @can('ELIMINAR CENTRO DE FORMACION')
                                            <form action="{{ route('centros.destroy', $centro->id) }}"
                                                method="POST" style="display: inline-block;"
                                                onsubmit="return confirm('¿Está seguro de eliminar este centro de formación?')">
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
                                <td colspan="5" class="text-center">No hay centros de formación registrados</td>
                            </tr>
                        @endforelse
                    </x-data-table>
                </div>
            </div>
        </div>
    </section>

    @include('components.confirm-delete-modal')
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/parametros.js'])
    @vite(['resources/js/pages/formularios-generico.js'])
@endsection
