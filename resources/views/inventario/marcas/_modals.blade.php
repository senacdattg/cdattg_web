{{-- Modales para Marcas --}}

{{-- Modal Ver Marca --}}
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
                        <label class="form-label fw-semibold text-muted">Productos Asociados</label>
                        <p class="form-control-plaintext" id="view_marca_productos">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Estado</label>
                        <p class="form-control-plaintext" id="view_marca_estado">-</p>
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

{{-- Modal Crear Marca --}}
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
                            <i class="fas fa-trademark text-primary me-1"></i>Nombre de la marca *
                        </label>
                        <input type="text" name="nombre" id="create_nombre" 
                            class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                            placeholder="Ej: SAMSUNG, HP, DELL..." 
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
                        <i class="fas fa-save me-1"></i>Guardar Marca
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Editar Marca --}}
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
                            <i class="fas fa-trademark text-warning me-1"></i>Nombre de la marca *
                        </label>
                        <input type="text" name="nombre" id="edit_nombre" 
                            class="form-control form-control-lg" 
                            placeholder="Ej: SAMSUNG, HP, DELL..." required>
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
