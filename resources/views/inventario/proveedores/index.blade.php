@extends('inventario.layouts.base')

@push('styles')
    @vite([
        'resources/css/inventario/inventario_listas.css'
    ])
@endpush

@section('title', 'Proveedores')

@section('content_header')
    @include('inventario._components.modal-header', [
        'title' => 'Gestión de Proveedores',
        'subtitle' => 'Administra los proveedores del inventario',
        'icon' => 'fas fa-truck',
        'modalTarget' => 'createProveedorModal',
        'buttonText' => 'Nuevo Proveedor'
    ])
@endsection

@section('main-content')
    {{-- Búsqueda --}}
    @include('inventario._components.search-filter', [
        'placeholder' => 'Buscar proveedores...',
        'inputId' => 'filtro-proveedores'
    ])

    {{-- Tabla --}}
    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table proveedores-table mb-0">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Proveedor</th>
                        <th style="width:120px">NIT</th>
                        <th>Contacto</th>
                        <th style="width:90px">Contratos</th>
                        <th style="width:80px">Estado</th>
                        <th class="actions-cell text-center" style="width:160px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $proveedor)
                        <tr>
                            <td><span class="badge badge-light">{{ $loop->iteration }}</span></td>
                            <td class="fw-semibold">{{ $proveedor->proveedor }}</td>
                            <td><span class="badge badge-light">{{ $proveedor->nit ?? '—' }}</span></td>
                            <td>
                                <div class="proveedor-contacto">
                                    @if($proveedor->email)
                                        <span><i class="fas fa-envelope text-muted"></i> {{ $proveedor->email }}</span>
                                    @endif
                                    @if($proveedor->telefono)
                                        <span><i class="fas fa-phone text-muted"></i> {{ $proveedor->telefono }}</span>
                                    @endif
                                    @if(!$proveedor->email && !$proveedor->telefono)
                                        <span class="text-muted">Sin contacto</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info text-white">{{ $proveedor->contratos_convenios_count ?? 0 }}</span>
                            </td>
                            <td>
                                @if(($proveedor->status ?? 1) == 1)
                                    <span class="badge bg-success">ACTIVO</span>
                                @else
                                    <span class="badge bg-danger">INACTIVO</span>
                                @endif
                            </td>
                            <td class="text-center actions-cell">
                                <button type="button" class="btn btn-xs btn-info" title="Ver" 
                                    onclick="viewProveedor({{ $proveedor->id }}, '{{ addslashes($proveedor->proveedor) }}', '{{ $proveedor->nit }}', '{{ $proveedor->email }}', {{ $proveedor->contratos_convenios_count ?? 0 }}, {{ $proveedor->status ?? 1 }}, '{{ $proveedor->userCreate->name ?? 'Usuario desconocido' }}', '{{ $proveedor->userUpdate->name ?? 'Usuario desconocido' }}', '{{ $proveedor->created_at?->format('d/m/Y H:i') }}', '{{ $proveedor->updated_at?->format('d/m/Y H:i') }}')"
                                    data-toggle="modal" data-target="#viewProveedorModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-warning" title="Editar" 
                                    onclick="editProveedor({{ $proveedor->id }}, '{{ $proveedor->proveedor }}', '{{ $proveedor->nit }}', '{{ $proveedor->email }}')"
                                    data-toggle="modal" data-target="#editProveedorModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-danger" title="Eliminar" 
                                    onclick="confirmDeleteProveedor({{ $proveedor->id }}, '{{ addslashes($proveedor->proveedor) }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-truck fa-2x mb-2 d-block"></i>
                                Sin proveedores registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginación --}}
    <div id="pagination-container" class="mt-3"></div>

    {{-- Modales --}}
    @include('inventario.proveedores._modals')
@endsection

@push('scripts')
    @vite([
        'resources/js/inventario/inventario_listas.js',
        'resources/js/inventario/proveedores.js',
        'resources/js/inventario/paginacion.js'
    ])
@endpush
