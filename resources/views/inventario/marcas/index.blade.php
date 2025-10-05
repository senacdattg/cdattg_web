@extends('adminlte::page')

@section('title', 'Marcas')

@section('css')
    @vite(['resources/css/inventario/shared/base.css', 'resources/css/inventario/marcas.css'])
@stop

@section('content_header')
    <div class="marcas-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="mb-1">
                    <i></i>Gestión de Marcas
                </h1>
                <p class="subtitle mb-0">Administra las marcas del inventario</p>
            </div>
            <button type="button" class="btn btn-light btn-lg" data-toggle="modal" data-target="#createMarcaModal">
                <i class="fas fa-plus me-2"></i> Nueva Marca
            </button>
        </div>
    </div>
@stop

@section('content')
    <div class="search-filter-container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <input type="text" id="filtro-marcas" class="form-control" placeholder=" Buscar marcas...">
            </div>
            <div class="col-md-6 text-end">
                <span id="filter-counter" class="filter-counter"></span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table marcas-table mb-0">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Marca</th>
                        <th style="width:120px">Agregado por</th>
                        <th style="width:120px">Actualizado por</th>
                        <th style="width:130px">Fecha creación</th>
                        <th style="width:130px">Última actualización</th>
                        <th class="actions-cell text-center" style="width:180px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($marcas as $marca)
                        <tr>
                            <td>
                                <div class="marca-inicial">{{ strtoupper(substr($marca->nombre ?? $marca->marca, 0, 1)) }}</div>
                            </td>
                            <td class="fw-semibold">{{ $marca->nombre ?? $marca->marca }}</td>
                            <td>
                                <div class="audit-info">
                                    <span class="user-id">{{ $marca->userCreate->name ?? 'ID: '.$marca->user_create_id }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="audit-info">
                                    <span class="user-id">{{ $marca->userUpdate->name ?? 'ID: '.$marca->user_update_id }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="audit-info">
                                    <span class="badge badge-created">{{ $marca->created_at?->format('d/m/Y') }}</span>
                                    <span class="date">{{ $marca->created_at?->format('H:i') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="audit-info">
                                    <span class="badge badge-updated">{{ $marca->updated_at?->format('d/m/Y') }}</span>
                                    <span class="date">{{ $marca->updated_at?->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="text-center actions-cell">
                                <button type="button" class="btn btn-xs btn-info" title="Ver" 
                                    onclick="viewMarca({{ $marca->id }}, '{{ addslashes($marca->nombre ?? $marca->marca) }}', '{{ $marca->userCreate->name ?? 'Usuario desconocido' }}', '{{ $marca->userUpdate->name ?? 'Usuario desconocido' }}', '{{ $marca->created_at?->format('d/m/Y H:i') }}', '{{ $marca->updated_at?->format('d/m/Y H:i') }}')"
                                    data-toggle="modal" data-target="#viewMarcaModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-warning" title="Editar" 
                                    onclick="editMarca({{ $marca->id }}, '{{ $marca->nombre ?? $marca->marca }}')"
                                    data-toggle="modal" data-target="#editMarcaModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('inventario.marcas.destroy', $marca) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-xs btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-tags fa-2x mb-2 d-block"></i>
                                Sin marcas registradas.
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

    <!-- Modal Ver Marca -->
    <div class="modal fade" id="viewMarcaModal" tabindex="-1" aria-labelledby="viewMarcaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="viewMarcaModalLabel">
                        <i class="fas fa-eye me-2"></i>Detalle de la Marca
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">ID</label>
                            <p class="form-control-plaintext" id="view_marca_id">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Nombre</label>
                            <p class="form-control-plaintext" id="view_marca_nombre">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Creado por</label>
                            <p class="form-control-plaintext" id="view_marca_created_by">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Actualizado por</label>
                            <p class="form-control-plaintext" id="view_marca_updated_by">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Fecha de Creación</label>
                            <p class="form-control-plaintext" id="view_marca_created_at">-</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Última Actualización</label>
                            <p class="form-control-plaintext" id="view_marca_updated_at">-</p>
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

    <!-- Modal Crear Marca -->
    <div class="modal fade" id="createMarcaModal" tabindex="-1" aria-labelledby="createMarcaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createMarcaModalLabel">
                        <i class="fas fa-plus me-2"></i>Nueva Marca
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('inventario.marcas.store') }}" method="POST" id="createMarcaForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="create_nombre" class="form-label fw-semibold">
                                <i class="fas fa-tags text-primary me-1"></i>Nombre de la marca *
                            </label>
                            <input type="text" name="nombre" id="create_nombre" 
                                class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                                placeholder="Ej: Samsung, Apple, HP..." 
                                value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle text-muted"></i>
                                El nombre de la marca debe ser único
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Guardar Marca
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Marca -->
    <div class="modal fade" id="editMarcaModal" tabindex="-1" aria-labelledby="editMarcaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editMarcaModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Marca
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="editMarcaForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label fw-semibold">
                                <i class="fas fa-tags text-warning me-1"></i>Nombre de la marca *
                            </label>
                            <input type="text" name="nombre" id="edit_nombre" 
                                class="form-control form-control-lg" 
                                placeholder="Ej: Samsung, Apple, HP..." required>
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
    @vite(['resources/js/inventario/marcas.js'])
@stop
