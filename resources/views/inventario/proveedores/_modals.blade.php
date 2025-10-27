{{-- Modales para Proveedores --}}

{{-- Modal Ver Proveedor --}}
<div class="modal fade" id="viewProveedorModal" tabindex="-1" aria-labelledby="viewProveedorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewProveedorModalLabel">
                    <i class="fas fa-eye me-2"></i>Detalle del Proveedor
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">ID</label>
                        <p class="form-control-plaintext" id="view_proveedor_id">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Nombre</label>
                        <p class="form-control-plaintext" id="view_proveedor_nombre">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">NIT</label>
                        <p class="form-control-plaintext" id="view_proveedor_nit">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Email</label>
                        <p class="form-control-plaintext" id="view_proveedor_email">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Contratos/Convenios</label>
                        <p class="form-control-plaintext" id="view_proveedor_contratos">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Estado</label>
                        <p class="form-control-plaintext" id="view_proveedor_estado">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Creado por</label>
                        <p class="form-control-plaintext" id="view_proveedor_created_by">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Actualizado por</label>
                        <p class="form-control-plaintext" id="view_proveedor_updated_by">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Fecha de Creación</label>
                        <p class="form-control-plaintext" id="view_proveedor_created_at">-</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-muted">Última Actualización</label>
                        <p class="form-control-plaintext" id="view_proveedor_updated_at">-</p>
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

{{-- Modal Crear Proveedor --}}
<div class="modal fade" id="createProveedorModal" tabindex="-1" aria-labelledby="createProveedorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createProveedorModalLabel">
                    <i class="fas fa-plus me-2"></i>Nuevo Proveedor
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('inventario.proveedores.store') }}" method="POST" id="createProveedorForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_proveedor" class="form-label fw-semibold">
                                <i class="fas fa-building text-primary me-1"></i>Nombre del proveedor *
                            </label>
                            <input type="text" name="proveedor" id="create_proveedor" 
                                class="form-control @error('proveedor') is-invalid @enderror" 
                                placeholder="Ej: ACME Corporation..." 
                                value="{{ old('proveedor') }}" required>
                            @error('proveedor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_nit" class="form-label fw-semibold">
                                <i class="fas fa-id-card text-primary me-1"></i>NIT
                            </label>
                            <input type="text" name="nit" id="create_nit" 
                                class="form-control @error('nit') is-invalid @enderror" 
                                placeholder="123456789-0" 
                                value="{{ old('nit') }}">
                            @error('nit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_email" class="form-label fw-semibold">
                                <i class="fas fa-envelope text-primary me-1"></i>Correo electrónico
                            </label>
                            <input type="email" name="email" id="create_email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                placeholder="contacto@proveedor.com" 
                                value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Editar Proveedor --}}
<div class="modal fade" id="editProveedorModal" tabindex="-1" aria-labelledby="editProveedorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editProveedorModalLabel">
                    <i class="fas fa-edit me-2"></i>Editar Proveedor
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" id="editProveedorForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_proveedor" class="form-label fw-semibold">
                                <i class="fas fa-building text-warning me-1"></i>Nombre del proveedor *
                            </label>
                            <input type="text" name="proveedor" id="edit_proveedor" 
                                class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_nit" class="form-label fw-semibold">
                                <i class="fas fa-id-card text-warning me-1"></i>NIT
                            </label>
                            <input type="text" name="nit" id="edit_nit" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label fw-semibold">
                                <i class="fas fa-envelope text-warning me-1"></i>Correo electrónico
                            </label>
                            <input type="email" name="email" id="edit_email" class="form-control">
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
