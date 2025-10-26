@extends('inventario.layouts.base')

@push('styles')
    @vite(['resources/css/inventario/inventario_listas.css'])
@endpush

@section('title', 'Contratos y Convenios')

@section('content_header')
    @include('inventario._components.modal-header', [
        'title' => 'Gestión de Contratos & Convenios',
        'subtitle' => 'Administra los contratos y convenios del inventario',
        'icon' => 'fas fa-file-contract',
        'modalTarget' => 'createContratoModal',
        'buttonText' => 'Nuevo Contrato/Convenio'
    ])
@endsection

@section('main-content')
    {{-- Búsqueda --}}
    @include('inventario._components.search-filter', [
        'placeholder' => 'Buscar contratos/convenios...',
        'inputId' => 'filtro-contratos'
    ])

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table contratos-table mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Nombre</th>
                        <th style="width:120px">Código</th>
                        <th style="width:110px">Fecha Inicio</th>
                        <th style="width:110px">Fecha Fin</th>
                        <th style="width:100px">Vigencia</th>
                        <th style="width:130px">Proveedor</th>
                        <th style="width:80px">Estado</th>
                        <th class="actions-cell text-center" style="width:140px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contratosConvenios as $item)
                        <tr>
                            <td><span class="badge badge-light">{{ $loop->iteration }}</span></td>
                            <td class="fw-semibold">{{ $item->name }}</td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $item->codigo }}</span>
                            </td>
                            <td>
                                <span class="text-sm text-muted">
                                    {{ $item->fecha_inicio ? \Carbon\Carbon::parse($item->fecha_inicio)->format('d/m/Y') : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-sm text-muted">
                                    {{ $item->fecha_fin ? \Carbon\Carbon::parse($item->fecha_fin)->format('d/m/Y') : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($item->fecha_inicio && $item->fecha_fin)
                                    @php
                                        $inicio = \Carbon\Carbon::parse($item->fecha_inicio);
                                        $fin = \Carbon\Carbon::parse($item->fecha_fin);
                                        $vigencia = $inicio->diffInDays($fin);
                                    @endphp
                                    <span class="badge bg-primary text-white">{{ $vigencia }} días</span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($item->proveedor)
                                    <span class="badge bg-warning text-dark">{{ $item->proveedor->proveedor }}</span>
                                @else
                                    <span class="badge bg-secondary">Sin proveedor</span>
                                @endif
                            </td>
                            <td>
                                @if($item->estado)
                                    <span class="badge bg-success">{{ $item->estado->parametro->name }}</span>
                                @else
                                    <span class="badge bg-secondary">Sin estado</span>
                                @endif
                            </td>
                            <td class="text-center actions-cell">
                                <button type="button" class="btn btn-xs btn-info" title="Ver" 
                                    onclick="viewContrato({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->codigo }}', '{{ $item->fecha_inicio }}', '{{ $item->fecha_fin }}', '{{ $item->proveedor->proveedor ?? 'Sin proveedor' }}', '{{ $item->estado->parametro->name ?? 'Sin estado' }}', '{{ $item->userCreate->name ?? 'Usuario desconocido' }}', '{{ $item->userUpdate->name ?? 'Usuario desconocido' }}', '{{ $item->created_at?->format('d/m/Y H:i') }}', '{{ $item->updated_at?->format('d/m/Y H:i') }}')"
                                    data-toggle="modal" data-target="#viewContratoModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-warning" title="Editar" 
                                    onclick="editContrato({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->codigo }}', '{{ $item->fecha_inicio }}', '{{ $item->fecha_fin }}', {{ $item->proveedor_id ?? 'null' }}, {{ $item->estado_id ?? 'null' }})"
                                    data-toggle="modal" data-target="#editContratoModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-danger" title="Eliminar"
                                    onclick="confirmDeleteContrato({{ $item->id }}, '{{ addslashes($item->name) }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                <i class="fas fa-file-contract fa-2x mb-2 d-block"></i>
                                Sin contratos/convenios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación JS -->
    <div id="pagination-container" class="mt-3"></div>

    @include('inventario.contratos_convenios._modals')
@endsection

@push('js')
    @vite([
        'resources/js/inventario/inventario_listas.js',
        'resources/js/inventario/contratos_convenios.js',
        'resources/js/inventario/paginacion.js'
    ])
@endpush
