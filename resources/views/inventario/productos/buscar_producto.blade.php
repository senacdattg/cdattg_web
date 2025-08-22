@extends('adminlte::page')

@section('title', 'Buscar Producto')

@section('content')
<div class="content-wrapper">
    <!-- Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-success">
                        <i class="fas fa-search"></i> Buscar Producto
                    </h1>
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
                        <i class="fas fa-search"></i> Búsqueda Avanzada de Productos
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Filtros de búsqueda -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="search-input">Buscar producto:</label>
                            <input type="text" id="search-input" class="form-control" placeholder="Nombre, código o descripción...">
                        </div>
                        <div class="col-md-3">
                            <label for="category-filter">Categoría:</label>
                            <select id="category-filter" class="form-control">
                                <option value="">Todas las categorías</option>
                                <option value="electronica">Electrónica</option>
                                <option value="oficina">Oficina</option>
                                <option value="mobiliario">Mobiliario</option>
                                <option value="herramientas">Herramientas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status-filter">Estado:</label>
                            <select id="status-filter" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="disponible">Disponible</option>
                                <option value="agotado">Agotado</option>
                                <option value="stock-bajo">Stock Bajo</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="location-filter">Ubicación:</label>
                            <select id="location-filter" class="form-control">
                                <option value="">Todas las ubicaciones</option>
                                <option value="almacen-a">Almacén A</option>
                                <option value="almacen-b">Almacén B</option>
                                <option value="oficina-principal">Oficina Principal</option>
                            </select>
                        </div>
                    </div>

                    <!-- Resultados de búsqueda -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="products-table">
                            <thead class="bg-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Stock Actual</th>
                                    <th>Stock Mínimo</th>
                                    <th>Ubicación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="products-tbody">
                                <!-- Los resultados se cargarán aquí via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="row mt-3">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info" id="products-info">
                                Mostrando 0 a 0 de 0 entradas
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers" id="products-pagination">
                                <!-- Paginación se generará aquí -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal de detalles del producto -->
<div class="modal fade" id="product-details-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-white">
                    <i class="fas fa-info-circle"></i> Detalles del Producto
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="product-details-content">
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
<script src="{{ asset('js/buscar-producto.js') }}"></script>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/buscar-producto.css') }}">
@endsection
