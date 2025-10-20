@extends('adminlte::page')

@section('title', "Regionales")

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header 
        icon="fa-cogs" 
        title="Regionales"
        subtitle="Gestión de regionales"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'], ['label' => 'Regionales', 'icon' => 'fa-cog', 'active' => true]]"
    />
@endsection

@section('content')
<section class="content mt-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <x-table-filters 
                    action="{{ route('regional.store') }}"
                    method="POST"
                    title="Crear Regional"
                    icon="fa-plus-circle"
                >
                    @include('regional.create')
                </x-table-filters>

                <x-data-table 
                        title="Lista de Regionales"
                        searchable="true"
                        searchAction="{{ route('regional.index') }}"
                        searchPlaceholder="Buscar regional..."
                        searchValue="{{ request('search') }}"
                        :columns="[['label' => '#', 'width' => '5%'], ['label' => 'Regional', 'width' => '30%'], ['label' => 'Departamento', 'width' => '30%'], ['label' => 'Estado', 'width' => '15%', 'class' => 'text-center'], ['label' => 'Acciones', 'width' => '20%', 'class' => 'text-center']]"
                        :pagination="$regionales->links()"
                    >
                        @forelse($regionales as $key => $regional)
                            <tr>
                                <td>{{ $regionales->firstItem() + $key }}</td>
                                <td>{{ $regional->regional }}</td>
                                <td>{{ $regional->departamento->departamento ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if($regional->status)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('EDITAR REGIONAL')
                                            <form action="{{ route('regional.cambiarEstado', $regional->id) }}"
                                                method="POST" style="display: inline-block; margin-right: 2px;">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-light" title="Cambiar Estado">
                                                    <i class="fas fa-sync text-success"></i>
                                                </button>
                                            </form>
                                        @endcan
                                        @can('VER REGIONAL')
                                            <a href="{{ route('regional.show', $regional->id) }}"
                                                class="btn btn-sm btn-light" title="Ver"
                                                style="margin-right: 2px;">
                                                <i class="fas fa-eye text-warning"></i>
                                            </a>
                                        @endcan
                                        @can('EDITAR REGIONAL')
                                            <a href="{{ route('regional.edit', $regional->id) }}"
                                                class="btn btn-sm btn-light" title="Editar"
                                                style="margin-right: 2px;">
                                                <i class="fas fa-pencil-alt text-primary"></i>
                                            </a>
                                        @endcan
                                        @can('ELIMINAR REGIONAL')
                                            <form action="{{ route('regional.destroy', $regional->id) }}"
                                                method="POST" style="display: inline-block;"
                                                onsubmit="return confirm('¿Está seguro de eliminar esta regional?')">
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
                                <td colspan="5" class="text-center">No hay regionales registradas</td>
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
    @vite(['resources/js/regional.js'])
@endsection