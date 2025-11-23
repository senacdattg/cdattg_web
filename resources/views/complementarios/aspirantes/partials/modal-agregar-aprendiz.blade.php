<!-- Modal Agregar Aprendiz -->
<div class="modal fade" id="modalAgregarAprendiz" tabindex="-1"
    aria-labelledby="modalAgregarAprendizLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarAprendizLabel">
                    <i class="fas fa-user-plus me-2"></i>Agregar Aprendiz
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulario de búsqueda -->
                <div id="busqueda-section">
                    <form id="formBuscarAprendiz">
                        <div class="mb-3">
                            <label for="numero_documento_buscar" class="form-label fw-bold">
                                <i class="fas fa-id-card me-1"></i>Número de Documento
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg" id="numero_documento_buscar"
                                       placeholder="Ingrese el número de documento" required>
                                <button type="submit" class="btn btn-primary" id="btnBuscarAprendiz">
                                    <i class="fas fa-search me-1"></i>Buscar
                                </button>
                            </div>
                            <div class="form-text">
                                Ingrese el número de documento para buscar la persona en el sistema.
                            </div>
                        </div>
                    </form>
                    <div id="loading-busqueda" class="text-center d-none py-4">
                        <img src="{{ asset('dist/img/LogoSena.png') }}" alt="Logo SENA" 
                             class="img-fluid sena-loading-logo mx-auto d-block">
                        <p class="mt-3 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Buscando persona...</p>
                    </div>
                </div>

                <!-- Información de la persona encontrada -->
                <div id="persona-info-section" class="d-none">
                    <div class="alert alert-info">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Persona encontrada en el sistema</strong>
                    </div>
                    <div class="card border-primary shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-user me-2"></i>Información de la Persona</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong>Nombre Completo:</strong>
                                    <p id="persona-nombre" class="mb-0"></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Número de Documento:</strong>
                                    <p id="persona-documento" class="mb-0"></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Correo Electrónico:</strong>
                                    <p id="persona-email" class="mb-0"></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Teléfono/Celular:</strong>
                                    <p id="persona-telefono" class="mb-0"></p>
                                </div>
                            </div>
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                ¿Desea agregar esta persona como aspirante al programa?
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mensaje de error -->
                <div id="error-section" class="alert alert-danger d-none">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <span id="error-message"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCancelar">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary d-none" id="btnAgregarPersonaEncontrada">
                    <i class="fas fa-plus me-1"></i>Agregar al Programa
                </button>
                <button type="button" class="btn btn-secondary d-none" id="btnNuevaBusqueda">
                    <i class="fas fa-redo me-1"></i>Nueva Búsqueda
                </button>
            </div>
        </div>
    </div>
</div>

