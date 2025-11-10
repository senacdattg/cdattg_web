@extends('adminlte::page')

@section('title', 'Gestión de Productos')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-boxes"
        title="Gestión de Productos"
        subtitle="Administra los productos del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Productos', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-create-card
                        url="{{ route('inventario.productos.create') }}"
                        title="Crear Producto"
                        icon="fa-plus-circle"
                        permission="CREAR PRODUCTO"
                    />
                    <!-- Botón para escanear código de barras -->
                    <div class="mt-3 text-right">
                        <button class="btn btn-secondary btn-lg" data-toggle="modal" data-target="#modalEscanear">
                            <i class="fas fa-barcode"></i> Escanear Código de Barras
                        </button>
                    </div>

                    <x-data-table
                        title="Lista de Productos"
                        searchable="true"
                        searchAction="{{ route('inventario.productos.index') }}"
                        searchPlaceholder="Buscar producto..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '3%'],
                            ['label' => 'Producto', 'width' => '20%'],
                            ['label' => 'Código', 'width' => '14%'],
                            ['label' => 'Categoría', 'width' => '10%'],
                            ['label' => 'Marca', 'width' => '10%'],
                            ['label' => 'Cantidad', 'width' => '8%'],
                            ['label' => 'Peso', 'width' => '8%'],
                            ['label' => 'Estado', 'width' => '8%'],
                            ['label' => 'Contrato', 'width' => '6%'],
                            ['label' => 'Proveedor', 'width' => '7%'],
                            ['label' => 'Opciones', 'width' => '6%', 'class' => 'text-center']
                        ]"
                        :pagination="$productos->links()"
                    >
                        @forelse ($productos as $producto)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $producto->producto }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ Str::limit($producto->descripcion, 30) ?? 'Sin descripción' }}
                                    </small>
                                </td>
                                <td>
                                    @if($producto->codigo_barras)
                                        <div><span class="badge badge-secondary">{{ $producto->codigo_barras }}</span></div>
                                        <div class="mt-1">
                                            <a class="btn btn-xs btn-outline-primary" target="_blank" href="{{ route('inventario.productos.etiqueta', $producto->id) }}">
                                                <i class="fas fa-print"></i> Imprimir
                                            </a>
                                        </div>
                                    @else
                                        <small class="text-muted">N/A</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $producto->categoria->name ?? 'Sin categoría' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge badge-dark">
                                        {{ $producto->marca->name ?? 'Sin marca' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $stockClass = 'success';
                                        if ($producto->cantidad <= 5) $stockClass = 'danger';
                                        elseif ($producto->cantidad <= 10) $stockClass = 'warning';
                                        elseif ($producto->cantidad <= 20) $stockClass = 'info';
                                    @endphp
                                    <span class="badge badge-{{ $stockClass }}">
                                        {{ $producto->cantidad }}
                                    </span>
                                </td>
                                <td>
                                    @if($producto->peso && $producto->unidadMedida)
                                        <small>{{ $producto->peso }} {{ $producto->unidadMedida->parametro->name ?? '' }}</small>
                                    @else
                                        <small class="text-muted">N/A</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $estadoClass = 'success';
                                        $estadoText = 'DISPONIBLE';
                                        $estadoProducto = $producto->estado?->parametro?->name;
                                        if ($estadoProducto === 'AGOTADO') {
                                            $estadoClass = 'danger';
                                            $estadoText = 'AGOTADO';
                                        } elseif ($producto->cantidad <= 0) {
                                            $estadoClass = 'danger';
                                            $estadoText = 'AGOTADO';
                                        } elseif ($producto->cantidad <= 5) {
                                            $estadoClass = 'warning';
                                            $estadoText = 'BAJO STOCK';
                                        }
                                    @endphp
                                    <span class="badge badge-{{ $estadoClass }}">
                                        {{ $estadoText }}
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        {{ $producto->contratoConvenio->name ?? 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        {{ $producto->proveedor->proveedor ?? 'N/A' }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <x-action-buttons
                                        show="true"
                                        edit="true"
                                        delete="true"
                                        showUrl="{{ route('inventario.productos.show', $producto->id) }}"
                                        editUrl="{{ route('inventario.productos.edit', $producto->id) }}"
                                        deleteUrl="{{ route('inventario.productos.destroy', $producto->id) }}"
                                        showTitle="Ver producto"
                                        editTitle="Editar producto"
                                        deleteTitle="Eliminar producto"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-table-empty
                                colspan="10"
                                message="No hay productos registrados"
                                icon="fas fa-box"
                            />
                        @endforelse
                    </x-data-table>
                    <div class="float-left pt-2">
                        <small class="text-muted">
                            Mostrando {{ $productos->firstItem() ?? 0 }} a {{ $productos->lastItem() ?? 0 }}
                            de {{ $productos->total() }} productos
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal para escanear código de barras -->
    <div class="modal fade" id="modalEscanear" tabindex="-1" aria-labelledby="modalEscanearLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalEscanearLabel">
                        <i class="fas fa-barcode"></i> Escanear Código de Barras
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">
                    <p>Escanea el código de barras del producto usando el lector.</p>
                    <input type="text" id="inputCodigoBarras" class="form-control form-control-lg text-center"
                        placeholder="Esperando código..." autocomplete="off" autofocus>
                    <div id="resultadoBusqueda" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de confirmación de eliminación --}}
    <x-confirm-delete-modal />

    {{-- Alertas --}}
    @include('layout.alertas')
@endsection


@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
    ])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/pages/formularios-generico.js'])
@endpush
<script src="{{ asset('js/inventario/escaner.js') }}"></script>

