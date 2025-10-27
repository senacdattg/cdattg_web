{{-- Modales para Categorías --}}

{{-- Modal Ver Categoría --}}
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
                        <label class="form-label fw-semibold text-muted">Productos Asociados</label>
                        <p class="form-control-plaintext" id="view_categoria_productos">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Estado</label>
                        <p class="form-control-plaintext" id="view_categoria_estado">-</p>
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

{{-- Modal Crear Categoría --}}
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

{{-- Modal Editar Categoría --}}
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
