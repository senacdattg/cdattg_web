@extends('adminlte::page')

@section('title', 'Gestión de Programas de Formación')

@section('content_header')
    <div class="content-header d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-graduation-cap me-3"></i>Gestión de Programas de Formación</h1>
            <p>Administre los programas de formación complementaria disponibles</p>
        </div>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newProgramModal">
            <i class="fas fa-plus-circle"></i> Nuevo Programa
        </a>
    </div>
@stop

@section('content')
    <!-- Search and Filters -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Buscar programa por nombre o código" aria-label="Search">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="custom-select mb-3" aria-label="Estado">
                        <option selected value="">Todos los estados</option>
                        <option value="activo">Con Oferta</option>
                        <option value="inactivo">Sin Oferta</option>
                        <option value="completo">Cupos llenos</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary">Todos</button>
                        <button type="button" class="btn btn-outline-success">Con cupos disponibles</button>
                        <button type="button" class="btn btn-outline-warning">Próximos a iniciar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Programs Cards View -->
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
        <!-- Program 1 -->
        <div class="col mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="h1 text-primary mb-3">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="d-flex justify-content-center">
                        <h5 class="card-title font-weight-bold">Auxiliar de Cocina</h5>
                    </div>
                    <span class="badge bg-success mb-2 w-20 text-center">Con Oferta</span>
                    <p class="card-text">Fundamentos de cocina, manipulación de alimentos y técnicas básicas de preparación.</p>
                    <div class="d-flex justify-content-center mt-3 pt-3 border-top">
                        <div>
                            <small class="text-muted">Duración</small>
                            <p class="mb-0"><strong>40 horas</strong></p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button class="btn btn-sm btn-outline-primary me-md-2 mr-2" data-bs-toggle="modal" data-bs-target="#viewProgramModal">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button class="btn btn-sm btn-outline-warning me-md-2 mr-2">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Program 2 -->
        <div class="col mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="h1 text-primary mb-3">
                        <i class="fas fa-hammer"></i>
                    </div>
                    <div class="d-flex justify-content-center">
                        <h5 class="card-title font-weight-bold">Acabados en Madera</h5>
                    </div>
                    <span class="badge bg-success mb-2 w-20 text-center">Con Oferta</span>
                    <p class="card-text">Técnicas de acabado, barnizado y restauración de muebles de madera.</p>
                    <div class="d-flex justify-content-center mt-3 pt-3 border-top">
                        <div>
                            <small class="text-muted">Duración</small>
                            <p class="mb-0"><strong>60 horas</strong></p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button class="btn btn-sm btn-outline-primary me-md-2 mr-2">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button class="btn btn-sm btn-outline-warning me-md-2 mr-2">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Program 3 -->
        <div class="col mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="h1 text-primary mb-3">
                        <i class="fas fa-cut"></i>
                    </div>
                    <div class="d-flex justify-content-center">
                        <h5 class="card-title font-weight-bold">Confección de Prendas</h5>
                    </div>
                    <span class="badge bg-success mb-2 w-20 text-center">Con Oferta</span>
                    <p class="card-text">Técnicas básicas de corte, confección y terminado de prendas de vestir.</p>
                    <div class="d-flex justify-content-center mt-3 pt-3 border-top">
                        <div>
                            <small class="text-muted">Duración</small>
                            <p class="mb-0"><strong>50 horas</strong></p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button class="btn btn-sm btn-outline-primary me-md-2 mr-2">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button class="btn btn-sm btn-outline-warning me-md-2 mr-2">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Program 4 -->
        <div class="col mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="h1 text-primary mb-3">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="d-flex justify-content-center">
                        <h5 class="card-title font-weight-bold">Mecánica Básica Automotriz</h5>
                    </div>
                    <span class="badge bg-success mb-2 w-20 text-center">Con Oferta</span>
                    <p class="card-text">Mantenimiento preventivo y diagnóstico básico de vehículos.</p>
                    <div class="d-flex justify-content-center mt-3 pt-3 border-top">
                        <div>
                            <small class="text-muted">Duración</small>
                            <p class="mb-0"><strong>90 horas</strong></p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button class="btn btn-sm btn-outline-primary me-md-2 mr-2">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button class="btn btn-sm btn-outline-warning me-md-2 mr-2">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Program 5 -->
        <div class="col mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="h1 text-primary mb-3">
                        <i class="fas fa-spa"></i>
                    </div>
                    <div class="d-flex justify-content-center">
                        <h5 class="card-title font-weight-bold">Cultivos de Huertas Urbanas</h5>
                    </div>
                    <span class="badge bg-success mb-2 w-20 text-center">Con Oferta</span>
                    <p class="card-text">Técnicas de cultivo y mantenimiento de huertas en espacios urbanos.</p>
                    <div class="d-flex justify-content-center mt-3 pt-3 border-top">
                        <div>
                            <small class="text-muted">Duración</small>
                            <p class="mb-0"><strong>120 horas</strong></p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button class="btn btn-sm btn-outline-primary me-md-2 mr-2">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button class="btn btn-sm btn-outline-warning me-md-2 mr-2">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Program 6 -->
        <div class="col mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="h1 text-primary mb-3">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <div class="d-flex justify-content-center">
                        <h5 class="card-title font-weight-bold">Normatividad Laboral</h5>
                    </div>
                    <span class="badge bg-success mb-2 w-20 text-center">Con Oferta</span>
                    <p class="card-text">Actualización en normatividad laboral y seguridad social.</p>
                    <div class="d-flex justify-content-center mt-3 pt-3 border-top">
                        <div>
                            <small class="text-muted">Duración</small>
                            <p class="mb-0"><strong>60 horas</strong></p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button class="btn btn-sm btn-outline-primary me-md-2 mr-2">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button class="btn btn-sm btn-outline-warning me-md-2 mr-2">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Program Modal -->
    <div class="modal fade" id="newProgramModal" tabindex="-1" aria-labelledby="newProgramModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newProgramModalLabel">
                        <i class="fas fa-plus-circle me-3"></i>Nuevo Programa de Formación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Complete el formulario para registrar un nuevo programa de formación</p>
                    <form id="newProgramForm">
                        <div class="mb-3">
                            <label for="programName" class="form-label">Nombre del Programa</label>
                            <input type="text" class="form-control" id="programName" name="programName" required>
                        </div>
                        <div class="mb-3">
                            <label for="programCode" class="form-label">Código del Programa</label>
                            <input type="text" class="form-control" id="programCode" name="programCode" required>
                        </div>
                        <div class="mb-3">
                            <label for="programDescription" class="form-label">Descripción</label>
                            <textarea class="form-control" id="programDescription" name="programDescription" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="programDuration" class="form-label">Duración (horas)</label>
                                    <input type="number" class="form-control" id="programDuration" name="programDuration" required min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="programCapacity" class="form-label">Cupos</label>
                                    <input type="number" class="form-control" id="programCapacity" name="programCapacity" required min="1">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="programStatus" class="form-label">Estado</label>
                            <select class="form-select" id="programStatus" name="programStatus" required>
                                <option value="activo">Activo</option>
                                <option value="proximo">Próximo a iniciar</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Programa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Program Modal -->
    <div class="modal fade" id="viewProgramModal" tabindex="-1" aria-labelledby="viewProgramModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewProgramModalLabel">Detalles del Programa de Formación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Modal content goes here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editProgramModal">
                        <i class="fas fa-edit"></i> Editar Programa
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newProgramForm = document.getElementById('newProgramForm');
            
            if (newProgramForm) {
                newProgramForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Aquí iría la lógica para guardar el programa
                    // Por ahora, mostramos un mensaje de éxito y cerramos la modal
                    alert('Programa guardado exitosamente');
                    
                    // Cerrar la modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('newProgramModal'));
                    modal.hide();
                    
                    // Limpiar el formulario
                    newProgramForm.reset();
                    
                    // Aquí podrías recargar la página o actualizar la lista de programas
                    // window.location.reload();
                });
            }
        });
    </script>
@stop
