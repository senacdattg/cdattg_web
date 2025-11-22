@extends('inventario.layouts.base')

@section('title', 'Carrito de Compras')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark">
                <i class="fas fa-shopping-cart"></i> Mi Carrito de Compras
            </h1>
            <small class="text-muted">Gestiona los productos de tu solicitud</small>
        </div>
        <div>
            <a href="{{ route('inventario.productos.catalogo') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Catálogo
            </a>
        </div>
    </div>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                {{-- Carrito de productos --}}
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">
                                <i class="fas fa-shopping-basket"></i> Productos en el Carrito
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" id="btn-empty-cart" title="Vaciar carrito">
                                    <i class="fas fa-trash"></i> Vaciar Carrito
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            {{-- Lista de productos en el carrito --}}
                            <div id="cart-items-container">
                                <div class="text-center p-5" id="empty-cart-message">
                                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                                    <h4>Tu carrito está vacío</h4>
                                    <p class="text-muted">Agrega productos desde el catálogo</p>
                                    <a href="{{ route('inventario.productos.catalogo') }}" class="btn btn-primary">
                                        <i class="fas fa-store"></i> Ir al Catálogo
                                    </a>
                                </div>

                                {{-- Tabla de productos (se llenará dinámicamente) --}}
                                <div class="table-responsive d-none" id="cart-items-table">
                                    <table class="table table-hover">
                                        <caption id="carrito-description" class="sr-only">
                                            Vista del carrito de compras con información de producto, stock, cantidad y acciones disponibles.
                                        </caption>
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">Imagen</th>
                                                <th style="width: 35%;">Producto</th>
                                                <th style="width: 15%;" class="text-center">Disponible</th>
                                                <th style="width: 20%;" class="text-center">Cantidad</th>
                                                <th style="width: 10%;" class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cart-items-body">
                                            {{-- Se llenará dinámicamente con JavaScript --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Resumen del carrito --}}
                <div class="col-lg-4">
                    {{-- Resumen de la orden --}}
                    <div class="card sticky-top" style="top: 20px;">
                        <div class="card-header bg-success">
                            <h3 class="card-title">
                                <i class="fas fa-receipt"></i> Resumen de la Solicitud
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong>Total de Productos:</strong>
                                </div>
                                <div class="col-6 text-right">
                                    <span id="total-products" class="badge badge-primary badge-lg">0</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong>Total de Items:</strong>
                                </div>
                                <div class="col-6 text-right">
                                    <span id="total-items" class="badge badge-info badge-lg">0</span>
                                </div>
                            </div>
                            <hr>
                            
                            {{-- Información adicional --}}
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Información:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Verifica las cantidades antes de confirmar</li>
                                    <li>Las solicitudes serán revisadas por el personal autorizado</li>
                                    <li>El stock se reservará al confirmar la solicitud</li>
                                </ul>
                            </div>

                            {{-- Datos del solicitante --}}
                            <div class="form-group">
                                <strong>
                                    <i class="fas fa-user"></i> Solicitante:
                                </strong>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ auth()->user()->name }}"
                                    readonly
                                >
                            </div>

                            <div class="form-group">
                                <strong>
                                    <i class="fas fa-envelope"></i> Correo:
                                </strong>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ auth()->user()->email }}"
                                    readonly
                                >
                            </div>

                            {{-- Botones de acción --}}
                            <button
                                type="button"
                                class="btn btn-success btn-block btn-lg"
                                id="btn-confirm-order"
                                disabled
                            >
                                <i class="fas fa-check"></i> Confirmar Solicitud
                            </button>

                            <button
                                type="button"
                                class="btn btn-outline-secondary btn-block mt-2"
                                id="btn-save-draft"
                            >
                                <i class="fas fa-save"></i> Guardar Borrador
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal de confirmación de orden --}}


    {{-- Modal de confirmación --}}
    <div
        class="modal fade"
        id="confirmOrderModal"
        tabindex="-1"
        aria-labelledby="confirmOrderModalLabel"
        aria-hidden="true">
        <div
            class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="confirmOrderModalLabel">
                        <i class="fas fa-check-circle"></i> Confirmar Orden de Requisición
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        Revisa el detalle de los productos antes de confirmar la solicitud.
                    </p>
                    <div id="order-summary-content"></div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        Cancelar
                    </button>
                    <button
                        type="button"
                        class="btn btn-success"
                        id="btn-final-confirm">
                        <i class="fas fa-paper-plane"></i> Enviar solicitud
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de advertencia de stock --}}
    <div
        class="modal fade"
        id="stockWarningModal"
        tabindex="-1"
        aria-labelledby="stockWarningModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="stockWarningModalLabel">
                        <i class="fas fa-exclamation-triangle"></i> Stock Insuficiente
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="stock-warning-content">
                    {{-- Se llenará dinámicamente --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- Alertas --}}
    {{-- Notificaciones manejadas globalmente por sweetalert2-notifications --}}
@endsection

@section('footer')
    {{-- Footer SENA --}}
    @include('layouts.footer')
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
    <style>
        .sticky-top {
            z-index: 1020;
        }
    </style>
@endpush

@push('js')
    @vite('resources/js/inventario/carrito.js')
@endpush
