@extends('adminlte::page')

@section('title', 'Préstamos y Salidas')

@section('content')
<div class="content-wrapper">
    <!-- Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-success">
                        <i class="fas fa-exchange-alt"></i> Préstamos y Salidas
                    </h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#new-request-modal">
                            <i class="fas fa-plus"></i> Nueva Solicitud
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title text-white">
                        <i class="fas fa-list"></i> Gestión de Préstamos y Salidas
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="search-request">Buscar solicitud:</label>
                            <input type="text" id="search-request" class="form-control" placeholder="ID, producto o solicitante...">
                        </div>
                        <div class="col-md-2">
                            <label for="type-filter">Tipo:</label>
                            <select id="type-filter" class="form-control">
                                <option value="">Todos</option>
                                <option value="prestamo">Préstamo</option>
                                <option value="salida">Salida</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status-filter">Estado:</label>
                            <select id="status-filter" class="form-control">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="aprobada">Aprobada</option>
                                <option value="rechazada">Rechazada</option>
                                <option value="entregada">Entregada</option>
                                <option value="devuelta">Devuelta</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date-from">Fecha desde:</label>
                            <input type="date" id="date-from" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="date-to">Fecha hasta:</label>
                            <input type="date" id="date-to" class="form-control">
                        </div>
                    </div>

                    <!-- Tabla de solicitudes -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="requests-table">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Solicitante</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Fecha Devolución</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="requests-tbody">
                                <!-- Los resultados se cargarán aquí -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal para nueva solicitud -->
<div class="modal fade" id="new-request-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-white">
                    <i class="fas fa-plus"></i> Nueva Solicitud
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="new-request-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="request-type">Tipo de Solicitud *</label>
                                <select id="request-type" name="tipo" class="form-control" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="prestamo">Préstamo Temporal</option>
                                    <option value="salida">Salida Permanente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="request-product">Producto *</label>
                                <select id="request-product" name="producto_id" class="form-control" required>
                                    <option value="">Seleccionar producto</option>
                                    <!-- Se llenarán via AJAX -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="request-quantity">Cantidad *</label>
                                <input type="number" id="request-quantity" name="cantidad" class="form-control" min="1" required>
                                <small class="form-text text-muted">Stock disponible: <span id="available-stock">0</span></small>
                            </div>
                        </div>
                        <div class="col-md-6" id="return-date-group" style="display: none;">
                            <div class="form-group">
                                <label for="return-date">Fecha de Devolución *</label>
                                <input type="date" id="return-date" name="fecha_devolucion" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="request-requester">Solicitante *</label>
                                <input type="text" id="request-requester" name="solicitante" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="request-department">Departamento</label>
                                <select id="request-department" name="departamento" class="form-control">
                                    <option value="">Seleccionar departamento</option>
                                    <option value="administracion">Administración</option>
                                    <option value="sistemas">Sistemas</option>
                                    <option value="mantenimiento">Mantenimiento</option>
                                    <option value="recursos-humanos">Recursos Humanos</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="request-purpose">Propósito/Justificación *</label>
                        <textarea id="request-purpose" name="proposito" class="form-control" rows="3" required placeholder="Describe el propósito de la solicitud..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Crear Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de detalles -->
<div class="modal fade" id="request-details-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title text-white">
                    <i class="fas fa-info-circle"></i> Detalles de la Solicitud
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="request-details-content">
                <!-- Contenido se cargará aquí -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/prestamos-salidas.js') }}"></script>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/prestamos-salidas.css') }}">
@endsection
