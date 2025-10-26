@extends('inventario.layouts.base')

@push('styles')
    @vite(['resources/css/inventario/inventario_listas.css'])
@endpush

@section('title', 'Marcas')

@section('content_header')
    @include('inventario._components.modal-header', [
        'title' => 'Gestión de Marcas',
        'subtitle' => 'Administra las marcas del inventario',
        'icon' => 'fas fa-trademark',
        'modalTarget' => 'createMarcaModal',
        'buttonText' => 'Nueva Marca'
    ])
@endsection

@section('main-content')
    {{-- Búsqueda --}}
    @include('inventario._components.search-filter', [
        'placeholder' => 'Buscar marcas...',
        'inputId' => 'filtro-marcas'
    ])

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table marcas-table mb-0">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Marca</th>
                        <th style="width:100px">Productos</th>
                        <th style="width:100px">Estado</th>
                        <th class="actions-cell text-center" style="width:180px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($marcas as $marca)
                        <tr>
                            <td><span class="badge badge-light">{{ $loop->iteration }}</span></td>
                            <td class="fw-semibold">{{ $marca->nombre ?? $marca->marca }}</td>
                            <td>
                                <span class="badge bg-info text-white">{{ $marca->productos_count ?? 0 }}</span>
                            </td>
                            <td>
                                @if(($marca->status ?? 1) == 1)
                                    <span class="badge bg-success">ACTIVO</span>
                                @else
                                    <span class="badge bg-danger">INACTIVO</span>
                                @endif
                            </td>
                            <td class="text-center actions-cell">
                                <button type="button" class="btn btn-xs btn-info" title="Ver" 
                                    onclick="viewMarca({{ $marca->id }}, '{{ addslashes($marca->nombre ?? $marca->marca) }}', {{ $marca->productos_count ?? 0 }}, {{ $marca->status ?? 1 }}, '{{ $marca->userCreate->name ?? 'Usuario desconocido' }}', '{{ $marca->userUpdate->name ?? 'Usuario desconocido' }}', '{{ $marca->created_at?->format('d/m/Y H:i') }}', '{{ $marca->updated_at?->format('d/m/Y H:i') }}')"
                                    data-toggle="modal" data-target="#viewMarcaModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-warning" title="Editar" 
                                    onclick="editMarca({{ $marca->id }}, '{{ $marca->nombre ?? $marca->marca }}')"
                                    data-toggle="modal" data-target="#editMarcaModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-danger" title="Eliminar" 
                                    onclick="confirmDeleteMarca({{ $marca->id }}, '{{ addslashes($marca->nombre ?? $marca->marca) }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-tags fa-2x mb-2 d-block"></i>
                                Sin marcas registradas.
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
    @include('inventario.marcas._modals')
@endsection

@push('scripts')
    @vite([
        'resources/js/inventario/inventario_listas.js',
        'resources/js/inventario/marcas.js',
        'resources/js/inventario/paginacion.js'
    ])
@endpush
