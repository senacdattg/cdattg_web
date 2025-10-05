@extends('adminlte::page')

@section('title', 'Categorías')

@section('css')
    @vite(['resources/css/inventario/shared/base.css', 'resources/css/inventario/categorias.css'])
@stop

@section('content_header')
    <div class="categorias-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="mb-1">
                    <i></i>Gestión de Categorías
                </h1>
                <p class="subtitle mb-0">Administra las categorías del inventario</p>
            </div>
            <button type="button" class="btn btn-light btn-lg" data-toggle="modal" data-target="#createCategoriaModal">
                <i class="fas fa-plus me-2"></i>Nueva Categoría
            </button>
        </div>
    </div>
@stop

@section('content')
    <div class="search-filter-container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <input type="text" id="filtro-categorias" class="form-control" placeholder=" Buscar categorías...">
            </div>
            <div class="col-md-6 text-end">
                <span id="filter-counter" class="filter-counter"></span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table categorias-table mb-0">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Categoria</th>
                        <th style="width:120px">Agregado por</th>
                        <th style="width:120px">Actualizado por</th>
                        <th style="width:130px">Fecha creación</th>
                        <th style="width:130px">Última actualización</th>
                        <th class="actions-cell text-center" style="width:180px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categorias as $categoria)
                        <tr>
                            <td><span class="badge badge-light">{{ $loop->iteration }}</span></td>
                            <td class="fw-semibold">{{ $categoria->nombre }}</td>
                            <td>
                                <div class="audit-info">
                                    <span class="user-id">{{ $categoria->userCreate->name ?? 'ID: '.$categoria->user_create_id }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="audit-info">
                                    <span class="user-id">{{ $categoria->userUpdate->name ?? 'ID: '.$categoria->user_update_id }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="audit-info">
                                    <span class="badge badge-created">{{ $categoria->created_at?->format('d/m/Y') }}</span>
                                    <span class="date">{{ $categoria->created_at?->format('H:i') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="audit-info">
                                    <span class="badge badge-updated">{{ $categoria->updated_at?->format('d/m/Y') }}</span>
                                    <span class="date">{{ $categoria->updated_at?->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="text-center actions-cell">
                                <button type="button" class="btn btn-xs btn-info" title="Ver" 
                                    onclick="viewCategoria({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}', '{{ $categoria->userCreate->name ?? 'Usuario desconocido' }}', '{{ $categoria->userUpdate->name ?? 'Usuario desconocido' }}', '{{ $categoria->created_at?->format('d/m/Y H:i') }}', '{{ $categoria->updated_at?->format('d/m/Y H:i') }}')"
                                    data-toggle="modal" data-target="#viewCategoriaModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-warning" title="Editar" 
                                    onclick="editCategoria({{ $categoria->id }}, '{{ $categoria->nombre }}')"
                                    data-toggle="modal" data-target="#editCategoriaModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('inventario.categorias.destroy', $categoria) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-xs btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Sin categorías registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination-container">
        <div class="pagination-info" id="pagination-info">
            Mostrando registros
        </div>
        <div class="pagination-controls">
            <button class="btn btn-sm btn-outline-primary" id="prev-page">
                <i class="fas fa-chevron-left"></i> Anterior
            </button>
            <div class="page-numbers" id="page-numbers"></div>
            <button class="btn btn-sm btn-outline-primary" id="next-page">
                Siguiente <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Modal Ver Categoría -->
    <div class="modal fade" id="viewCategoriaModal" tabindex="-1" aria-labelledby="viewCategoriaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="viewCategoriaModalLabel">
                        <i class="fas fa-eye me-2"></i>Detalle de la Categoría
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">ID</label>
                            <p class="form-control-plaintext" id="view_categoria_id">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Nombre</label>
                            <p class="form-control-plaintext" id="view_categoria_nombre">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Creado por</label>
                            <p class="form-control-plaintext" id="view_categoria_created_by">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Actualizado por</label>
                            <p class="form-control-plaintext" id="view_categoria_updated_by">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Fecha de Creación</label>
                            <p class="form-control-plaintext" id="view_categoria_created_at">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Última Actualización</label>
                            <p class="form-control-plaintext" id="view_categoria_updated_at">-</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear Categoría -->
    <div class="modal fade" id="createCategoriaModal" tabindex="-1" aria-labelledby="createCategoriaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createCategoriaModalLabel">
                        <i class="fas fa-plus me-2"></i>Nueva Categoría
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('inventario.categorias.store') }}" method="POST" id="createCategoriaForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="create_nombre" class="form-label fw-semibold">
                                <i class="fas fa-tag text-primary me-1"></i>Nombre de la categoría *
                            </label>
                            <input type="text" name="nombre" id="create_nombre" 
                                class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                                placeholder="Ej: TECNOLOGÍA, PAPELERÍA..." 
                                value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle text-muted"></i>
                                El nombre debe ser único y descriptivo
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Guardar Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Categoría -->
    <div class="modal fade" id="editCategoriaModal" tabindex="-1" aria-labelledby="editCategoriaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editCategoriaModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Categoría
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="editCategoriaForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label fw-semibold">
                                <i class="fas fa-tag text-warning me-1"></i>Nombre de la categoría *
                            </label>
                            <input type="text" name="nombre" id="edit_nombre" 
                                class="form-control form-control-lg" 
                                placeholder="Ej: TECNOLOGÍA, PAPELERÍA..." required>
                            <div class="form-text">
                                <i class="fas fa-info-circle text-muted"></i>
                                El nombre debe ser único y descriptivo
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        @if(session('success'))
            window.flashSuccess = @json(session('success'));
        @endif
        @if(session('error'))
            window.flashError = @json(session('error'));
        @endif
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/inventario/categorias.js'])
@stop
