@extends('inventario.layouts.base')

@push('styles')
    @vite([
        'resources/css/inventario/inventario_listas.css'
    ])
@endpush

@section('title', 'Categorías')

@section('content_header')
    @include('inventario._components.modal-header', [
        'title' => 'Gestión de Categorías',
        'subtitle' => 'Administra las categorías del inventario',
        'icon' => 'fas fa-tags',
        'modalTarget' => 'createCategoriaModal',
        'buttonText' => 'Nueva Categoría'
    ])
@endsection

@section('main-content')
    {{-- Búsqueda --}}
    @include('inventario._components.search-filter', [
        'placeholder' => 'Buscar categorías...',
        'inputId' => 'filtro-categorias'
    ])

    {{-- Tabla --}}
    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table categorias-table mb-0">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Categoria</th>
                        <th style="width:100px">Productos</th>
                        <th style="width:100px">Estado</th>
                        <th class="actions-cell text-center" style="width:180px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categorias as $categoria)
                        <tr>
                            <td><span class="badge badge-light">{{ $loop->iteration }}</span></td>
                            <td class="fw-semibold">{{ $categoria->nombre }}</td>
                            <td>
                                <span class="badge bg-info text-white">{{ $categoria->productos_count ?? 0 }}</span>
                            </td>
                            <td>
                                @if(($categoria->status ?? 1) == 1)
                                    <span class="badge bg-success">ACTIVO</span>
                                @else
                                    <span class="badge bg-danger">INACTIVO</span>
                                @endif
                            </td>
                            <td class="text-center actions-cell">
                                <button type="button" class="btn btn-xs btn-info" title="Ver" 
                                    onclick="viewCategoria({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}', {{ $categoria->productos_count ?? 0 }}, {{ $categoria->status ?? 1 }}, '{{ $categoria->userCreate->name ?? 'Usuario desconocido' }}', '{{ $categoria->userUpdate->name ?? 'Usuario desconocido' }}', '{{ $categoria->created_at?->format('d/m/Y H:i') }}', '{{ $categoria->updated_at?->format('d/m/Y H:i') }}')"
                                    data-toggle="modal" data-target="#viewCategoriaModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-warning" title="Editar" 
                                    onclick="editCategoria({{ $categoria->id }}, '{{ $categoria->nombre }}')"
                                    data-toggle="modal" data-target="#editCategoriaModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-danger" title="Eliminar" 
                                    onclick="confirmDeleteCategoria({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Sin categorías registradas.
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
    @include('inventario.categorias._modals')
@endsection

@push('scripts')
    @vite([
        'resources/js/inventario/inventario_listas.js',
        'resources/js/inventario/categorias.js',
        'resources/js/inventario/paginacion.js'
    ])
@endpush
