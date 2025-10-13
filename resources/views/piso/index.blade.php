@extends('adminlte::page')

@section('content_header')
    <x-page-header 
        icon="fa-layer-group" 
        title="Pisos"
        subtitle="Gestión de pisos del sistema"
        :breadcrumb="[['label' => 'Inicio', 'url' => route('home.index'), 'icon' => 'fa-home'], ['label' => 'Pisos', 'active' => true, 'icon' => 'fa-layer-group']]"
    />
@endsection

@section('content')

    <section class="content mt-4">
        <div class="container-fluid">
            <x-session-alerts />
            
            <div class="row">
                <div class="col-12">
                    <x-data-table 
                        title="Lista de Pisos"
                        searchable="true"
                        searchAction="{{ route('piso.index') }}"
                        searchPlaceholder="Buscar piso..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Nombre', 'width' => '20%'],
                            ['label' => 'Sede', 'width' => '25%'],
                            ['label' => 'Bloque', 'width' => '25%'],
                            ['label' => 'Estado', 'width' => '15%'],
                            ['label' => 'Acciones', 'width' => '10%', 'class' => 'text-center']
                        ]"
                        :pagination="$pisos->links()"
                    >
                            <?php $i = 1; ?>
                            @forelse ($pisos as $piso)
                                <tr>
                                    <td>
                                        {{ $i++ }}
                                    </td>
                                    <td>
                                        {{ $piso->piso }}
                                    </td>
                                    <td>
                                        {{ $piso->bloque->sede->sede }}
                                    </td>
                                    <td>
                                        {{ $piso->bloque->bloque }}
                                    </td>

                                    <td>
                                        <span class="badge badge-{{ $piso->status === 1 ? 'success' : 'danger' }}">
                                            @if ($piso->status === 1)
                                                ACTIVO
                                            @else
                                                INACTIVO
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <x-action-buttons 
                                            :show="true"
                                            :edit="true"
                                            :delete="true"
                                            showUrl="{{ route('piso.show', $piso->id) }}"
                                            editUrl="{{ route('piso.edit', $piso->id) }}"
                                            deleteUrl="{{ route('piso.destroy', $piso->id) }}"
                                            showPermission="VER PISO"
                                            editPermission="EDITAR PISO"
                                            deletePermission="ELIMINAR PISO"
                                            :custom="[
                                                [
                                                    'url' => route('piso.cambiarEstado', $piso->id),
                                                    'title' => 'Cambiar estado',
                                                    'icon' => 'fas fa-sync',
                                                    'color' => 'text-success',
                                                    'permission' => 'EDITAR PISO',
                                                    'action' => 'change-status',
                                                    'confirm' => false
                                                ]
                                            ]"
                                        />
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="4">No hay pisos registrados</td>
                                </tr>
                            @endforelse
                    </x-data-table>
                </div>
            </div>
        </div>
    </section>
@endsection
