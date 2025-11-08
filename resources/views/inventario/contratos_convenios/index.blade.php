@extends('adminlte::page')

@section('title', 'Gestión de Contratos & Convenios')

@section('content_header')
    <x-page-header
        icon="fas fa-file-contract"
        title="Gestión de Contratos & Convenios"
        subtitle="Administra los contratos y convenios del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Contratos y Convenios', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card
                        url="{{ route('inventario.contratos-convenios.create') }}"
                        title="Nuevo Contrato/Convenio"
                        icon="fa-plus-circle"
                        permission="CREAR CONTRATO"
                    />

                    <x-data-table
                        title="Lista de Contratos y Convenios"
                        searchable="true"
                        searchAction="{{ route('inventario.contratos-convenios.index') }}"
                        searchPlaceholder="Buscar contrato..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Nombre', 'width' => '25%'],
                            ['label' => 'Código', 'width' => '15%'],
                            ['label' => 'Fecha Inicio', 'width' => '12%'],
                            ['label' => 'Fecha Fin', 'width' => '12%'],
                            ['label' => 'Vigencia', 'width' => '10%'],
                            ['label' => 'Proveedor', 'width' => '10%'],
                            ['label' => 'Estado', 'width' => '6%'],
                            ['label' => 'Opciones', 'width' => '5%', 'class' => 'text-center']
                        ]"
                        :pagination="$contratosConvenios->links()"
                    >
                        @forelse ($contratosConvenios as $contrato)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $contrato->name ?? 'N/A' }}</td>
                                <td>{{ $contrato->codigo ?? 'N/A' }}</td>
                                <td>
                                    @if($contrato->fecha_inicio)
                                        @if(is_string($contrato->fecha_inicio))
                                            {{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}
                                        @else
                                            {{ $contrato->fecha_inicio->format('d/m/Y') }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($contrato->fecha_fin)
                                        @if(is_string($contrato->fecha_fin))
                                            {{ \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') }}
                                        @else
                                            {{ $contrato->fecha_fin->format('d/m/Y') }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $fechaFin = null;
                                        if($contrato->fecha_fin) {
                                            $fechaFin = is_string($contrato->fecha_fin)
                                                ? \Carbon\Carbon::parse($contrato->fecha_fin)
                                                : $contrato->fecha_fin;
                                        }
                                    @endphp
                                    <span class="badge badge-{{ $fechaFin && $fechaFin->isPast() ? 'danger' : 'success' }}">
                                        {{ $fechaFin && $fechaFin->isPast() ? 'Vencido' : 'Vigente' }}
                                    </span>
                                </td>
                                <td>
                                    @if($contrato->proveedor)
                                        {{ is_object($contrato->proveedor) ? ($contrato->proveedor->proveedor ?? 'N/A') : $contrato->proveedor }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($contrato->estado)
                                        <span class="badge badge-{{ $contrato->estado->status == 1 ? 'success' : 'danger' }}">
                                            {{ $contrato->estado->parametro->name ?? 'N/A' }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">SIN ESTADO</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <x-action-buttons
                                        show="true"
                                        edit="true"
                                        delete="true"
                                        showUrl="{{ route('inventario.contratos-convenios.show', $contrato->id) }}"
                                        editUrl="{{ route('inventario.contratos-convenios.edit', $contrato->id) }}"
                                        deleteUrl="{{ route('inventario.contratos-convenios.destroy', $contrato->id) }}"
                                        showTitle="Ver contrato"
                                        editTitle="Editar contrato"
                                        deleteTitle="Eliminar contrato"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-table-empty
                                colspan="9"
                                message="No hay contratos/convenios registrados"
                                icon="fas fa-file-contract"
                            />
                        @endforelse
                    </x-data-table>
                    <div class="float-left pt-2">
                        <small class="text-muted">
                            Mostrando {{ $contratosConvenios->firstItem() ?? 0 }} a {{ $contratosConvenios->lastItem() ?? 0 }} 
                            de {{ $contratosConvenios->total() }} contratos/convenios  
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Paginación --}}
    <div id="pagination-container" class="mt-3"></div>
    
    {{-- Modal de confirmación de eliminación --}}
    <x-confirm-delete-modal />
    
    {{-- Alertas --}}
    @include('layout.alertas')
    
    {{-- Footer SENA --}}
    @include('layout.footer')
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',    
    ])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/pages/formularios-generico.js'])
@endpush

