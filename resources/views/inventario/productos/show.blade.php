@extends('adminlte::page')

@section('title', 'Ver Producto')

@section('content_header')
    <x-page-header
        icon="fas fa-eye"
        title="Ver Producto"
        subtitle="Detalles del producto"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Productos', 'url' => route('inventario.productos.index')],
            ['label' => $producto->producto, 'active' => true]
        ]"
    />
@endsection

@section('content')
    <div class="product-details-container fade-in">
        {{-- Alertas --}}
        @include('components.session-alerts')

        {{-- Botón Volver --}}
        <div class="mb-3">
            <a href="{{ route('inventario.productos.index') }}" class="btn-action btn-action-secondary">
                <i class="fas fa-arrow-left"></i>
                Volver al Listado
            </a>
        </div>

        <div class="row">
            {{-- Columna de Imagen --}}
            <div class="col-lg-4 col-md-5">
                <div class="product-image-card slide-in">
                    <h5 class="font-weight-bold mb-3 text-gradient">
                        <i class="fas fa-image"></i> Imagen del Producto
                    </h5>
                    <div class="product-image-wrapper">
                        <img src="{{ $producto->imagen ? asset($producto->imagen) : asset('public/img/inventario/producto-default.png') }}" 
                             alt="{{ $producto->producto }}"
                             class="img-fluid"
                             style="cursor: pointer;"
                             onclick="$('#imageModal').modal('show'); $('#expandedImage').attr('src', this.src);">
                    </div>
                    
                    {{-- Estadísticas Rápidas --}}
                    <div class="stats-grid mt-4">
                        <div class="stat-card stat-{{ $producto->cantidad <= 5 ? 'danger' : ($producto->cantidad <= 10 ? 'warning' : 'success') }}">
                            <div class="stat-card-header">
                                <div class="stat-card-icon">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <div>
                                    <div class="stat-card-label">Stock</div>
                                    <div class="stat-card-value">{{ $producto->cantidad }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Widget del Carrito --}}
                @can('AGREGAR AL CARRITO')
                    <div class="card-gradient mt-3 slide-in">
                        <div class="card-gradient-header" style="background: var(--success-gradient)">
                            <i class="fas fa-shopping-cart"></i>
                            Agregar al Carrito
                        </div>
                        <div class="card-gradient-body">
                            @include('inventario._components.cart-widget', ['producto' => $producto])
                        </div>
                    </div>
                @endcan
            </div>

            {{-- Columna de Información --}}
            <div class="col-lg-8 col-md-7">
                <div class="product-info-card slide-in">
                    <div class="card-gradient-header">
                        <i class="fas fa-info-circle"></i>
                        Información del Producto
                    </div>
                    <div class="card-gradient-body p-0">
                        <ul class="product-info-list">
                            <li>
                                <i class="fas fa-tag"></i>
                                <strong>Producto:</strong>
                                <span>{{ $producto->producto }}</span>
                            </li>
                            
                            <li>
                                <i class="fas fa-cubes"></i>
                                <strong>Tipo:</strong>
                                <span>{{ $producto->tipoProducto->parametro->name ?? 'N/A' }}</span>
                            </li>
                            
                            <li>
                                <i class="fas fa-align-left"></i>
                                <strong>Descripción:</strong>
                                <span>{{ $producto->descripcion ?? 'Sin descripción' }}</span>
                            </li>
                            
                            <li>
                                <i class="fas fa-weight"></i>
                                <strong>Magnitud:</strong>
                                <span>{{ $producto->peso }} {{ $producto->unidadMedida->parametro->name ?? '' }}</span>
                            </li>
                            
                            <li>
                                <i class="fas fa-barcode"></i>
                                <strong>Código de Barras:</strong>
                                <span class="badge-modern badge-secondary">{{ $producto->codigo_barras }}</span>
                                @if($producto->codigo_barras)
                                    <div class="mt-2">
                                        <svg id="barcode-show" style="width:100%"></svg>
                                        <div class="mt-1">
                                            <a target="_blank" class="btn btn-xs btn-outline-primary" href="{{ route('inventario.productos.etiqueta', $producto->id) }}">
                                                <i class="fas fa-print"></i> Imprimir etiqueta
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </li>
                            
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <strong>Estado:</strong>
                                <span class="badge-modern {{ $producto->estado->parametro->name === 'DISPONIBLE' ? 'badge-success' : 'badge-warning' }}">
                                    {{ $producto->estado->parametro->name ?? 'N/A' }}
                                </span>
                            </li>
                            
                            <li>
                                <i class="fas fa-folder"></i>
                                <strong>Categoría:</strong>
                                <span class="badge-modern badge-info">
                                    <i class="fas fa-tag"></i>
                                    {{ $producto->categoria->name ?? 'N/A' }}
                                </span>
                            </li>
                            
                            <li>
                                <i class="fas fa-copyright"></i>
                                <strong>Marca:</strong>
                                <span class="badge-modern badge-dark">
                                    {{ $producto->marca->name ?? 'N/A' }}
                                </span>
                            </li>
                            
                            <li>
                                <i class="fas fa-building"></i>
                                <strong>Ambiente:</strong>
                                <span>{{ $producto->ambiente->title ?? 'N/A' }}</span>
                            </li>
                            
                            <li>
                                <i class="fas fa-file-contract"></i>
                                <strong>Contrato/Convenio:</strong>
                                <span>{{ $producto->contratoConvenio->name ?? 'N/A' }}</span>
                            </li>
                            
                            <li>
                                <i class="fas fa-truck"></i>
                                <strong>Proveedor:</strong>
                                <span>{{ $producto->proveedor->proveedor ?? 'N/A' }}</span>
                            </li>

                            
                            <li>
                                <i class="fas fa-calendar-times"></i>
                                <strong>Fecha de Vencimiento:</strong>
                                <span>{{ $producto->fecha_vencimiento ? $producto->fecha_vencimiento->format('d/m/Y') : 'Sin fecha' }}</span>
                            </li>
                            
                            <li>
                                <i class="fas fa-calendar-plus"></i>
                                <strong>Fecha de Registro:</strong>
                                <span>{{ $producto->created_at->format('d/m/Y H:i') }}</span>
                            </li>
                            
                            @if($producto->updated_at != $producto->created_at)
                                <li>
                                    <i class="fas fa-calendar-edit"></i>
                                    <strong>Última Actualización:</strong>
                                    <span>{{ $producto->updated_at->format('d/m/Y H:i') }}</span>
                                </li>
                            @endif
                            
                            @if($producto->userCreate)
                                <li>
                                    <i class="fas fa-user"></i>
                                    <strong>Creado por:</strong>
                                    <span>{{ $producto->userCreate->name }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- Estadísticas Adicionales --}}
                <div class="stats-grid mt-4">
                    <div class="stat-card stat-{{ $producto->cantidad <= 5 ? 'danger' : ($producto->cantidad <= 10 ? 'warning' : 'success') }}">
                        <div class="stat-card-header">
                            <div class="stat-card-icon">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <div>
                                <div class="stat-card-label">Inventario</div>
                                <div class="stat-card-value">{{ $producto->cantidad }} unidades</div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card stat-info">
                        <div class="stat-card-header">
                            <div class="stat-card-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div>
                                <div class="stat-card-label">Registro</div>
                                <div class="stat-card-value" style="font-size: 1.25rem;">
                                    {{ $producto->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card stat-{{ $producto->estado->parametro->name === 'DISPONIBLE' ? 'success' : 'warning' }}">
                        <div class="stat-card-header">
                            <div class="stat-card-icon">
                                <i class="fas fa-toggle-on"></i>
                            </div>
                            <div>
                                <div class="stat-card-label">Estado</div>
                                <div class="stat-card-value" style="font-size: 1rem;">
                                    {{ $producto->estado->parametro->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="action-buttons-container">
                    @can('EDITAR PRODUCTO')
                        <a href="{{ route('inventario.productos.edit', $producto->id) }}" class="btn-action btn-action-primary">
                            <i class="fas fa-edit"></i>
                            Editar Producto
                        </a>
                    @endcan

                    @can('ELIMINAR PRODUCTO')
                        <form action="{{ route('inventario.productos.destroy', $producto->id) }}"
                              method="POST" 
                              class="d-inline formulario-eliminar">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-action-danger">
                                <i class="fas fa-trash"></i>
                                Eliminar Producto
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para imagen expandida --}}
    @include('inventario._components.image-modal')
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
        'resources/css/inventario/inventario.css',
        'resources/css/inventario/imagen.css',
    ])
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite('resources/js/inventario/imagen.js')
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <script>
        // Confirmación de eliminación
        document.querySelectorAll('.formulario-eliminar').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#eb3349',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        popup: 'modal-imagen-custom'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        // Render del código de barras
        @if($producto->codigo_barras)
        JsBarcode("#barcode-show", "{{ $producto->codigo_barras }}", { format: "code128", width: 2, height: 60, displayValue: false });
        @endif
    </script>
@endpush
