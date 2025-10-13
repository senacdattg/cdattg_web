@extends('adminlte::page')

@section('content_header')
    <x-page-header 
        icon="fa-building" 
        title="Sedes"
        subtitle="Gestión de sedes del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('home.index'), 'icon' => 'fa-home'], ['label' => 'Sedes', 'active' => true, 'icon' => 'fa-building']]"
    />
@endsection

@section('content')

    <section class="content mt-4">
        <div class="container-fluid">
            <x-session-alerts />
            
            <div class="row">
                <div class="col-12">
                    <x-data-table 
                        title="Lista de Sedes"
                        searchable="true"
                        searchAction="{{ route('sede.index') }}"
                        searchPlaceholder="Buscar sede..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Sede', 'width' => '20%'],
                            ['label' => 'Dirección', 'width' => '30%'],
                            ['label' => 'Municipio', 'width' => '25%'],
                            ['label' => 'Estado', 'width' => '20%']
                        ]"
                        :pagination="$sedes->links()"
                    >
                            <?php
                            $i = 1;
                            ?>
                            @forelse ($sedes as $sede)
                                <tr>
                                    <td>
                                        {{ $i++ }}
                                    </td>
                                    <td>
                                        {{ $sede->sede }}
                                    </td>

                                    <td>
                                        {{ $sede->direccion }}
                                    </td>

                                    <td>
                                        {{ $sede->municipio->municipio }}
                                    </td>

                                    <td>
                                        <span class="badge badge-{{ $sede->status === 1 ? 'success' : 'danger' }}">
                                            @if ($sede->status === 1)
                                                ACTIVO
                                            @else
                                                INACTIVO
                                            @endif
                                        </span>
                                    </td>
                                    @can('EDITAR SEDE')
                                        <td>
                                            <form id="cambiarEstadoForm" class=" d-inline"
                                                action="{{ route('sede.cambiarEstado', ['sede' => $sede->id]) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-success btn-sm"><i
                                                        class="fas fa-sync"></i></button>
                                            </form>
                                        </td>
                                    @endcan
                                    @can('VER SEDE')
                                        <td>
                                            <a class="btn btn-warning btn-sm"
                                                href="{{ route('sede.show', ['sede' => $sede->id]) }}">
                                                <i class="fas fa-eye"></i>

                                            </a>
                                        </td>
                                    @endcan
                                    @can('EDITAR SEDE')

                                    <td>
                                        <a class="btn btn-info btn-sm"
                                        href="{{ route('sede.edit', ['sede' => $sede->id]) }}">
                                        <i class="fas fa-pencil-alt">
                                        </i>
                                    </a>
                                </td>
                                @endcan
                                @can('ELIMINAR SEDE')

                                <td>
                                    <form class="formulario-eliminar "
                                    action="{{ route('sede.destroy', ['sede' => $sede->id]) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger btn-sm">

                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                            @endcan
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="4">No hay sedes registradas</td>
                                </tr>
                            @endforelse
                    </x-data-table>
                </div>
            </div>
        </div>
    </section>
@endsection

