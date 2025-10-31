@extends('adminlte::page')

@section('title', 'Carrito de Compras')

@section('content_header')
    <x-page-header
        icon="fas fa-shopping-cart"
        title="Carrito de Compras"
        subtitle="Productos seleccionados para el inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Carrito', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-data-table
                        title="Lista de Productos en Carrito"
                        searchable="true"
                        searchAction="{{ route('inventario.carrito.index') }}"
                        searchPlaceholder="Buscar producto en carrito..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Producto', 'width' => '40%'],
                            ['label' => 'Cantidad', 'width' => '15%'],
                            ['label' => 'Categoría', 'width' => '20%'],
                            ['label' => 'Estado', 'width' => '10%'],
                            ['label' => 'Opciones', 'width' => '10%', 'class' => 'text-center']
                        ]"
                    >
                        @if(isset($carrito) && count($carrito) > 0)
                            @foreach($carrito as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->producto->producto ?? 'Producto no encontrado' }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $item->cantidad ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            {{ $item->producto->categoria->name ?? 'Sin categoría' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success">ACTIVO</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button"
                                                class="btn btn-sm btn-light"
                                                title="Quitar del carrito"
                                                onclick="eliminarDelCarrito({{ $item->id }})">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="text-center py-4">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">El carrito está vacío</h5>
                                        <p class="text-muted">Agrega productos desde el catálogo para verlos aquí</p>
                                        <a href="{{ route('inventario.productos.index') }}" class="btn btn-primary">
                                            <i class="fas fa-box mr-2"></i> Ver Productos
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </x-data-table>
                    
                    @if(!empty($carrito))
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5>Resumen del Pedido</h5>
                                        <p><strong>Total de productos:</strong> {{ count($carrito) }}</p>
                                        <button type="button" class="btn btn-success" onclick="procesarPedido()">
                                            <i class="fas fa-paper-plane mr-2"></i> Enviar Pedido
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Alertas --}}
    @include('layout.alertas')
@endsection

@section('footer')
    {{-- Footer SENA --}}
    @include('inventario._components.sena-footer')
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
        'resources/css/style.css'
    ])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function eliminarDelCarrito(itemId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Este producto se quitará del carrito",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, quitarlo'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí iría la lógica para eliminar del carrito
                    location.reload();
                }
            });
        }
        
        function procesarPedido() {
            Swal.fire({
                title: 'Procesar Pedido',
                text: "Se enviará el pedido para revisión",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Enviar Pedido',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí iría la lógica para procesar el pedido
                    Swal.fire({
                        title: '¡Pedido Enviado!',
                        text: 'Tu pedido ha sido enviado para revisión',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '{{ route("inventario.productos.index") }}';
                    });
                }
            });
        }
    </script>
@endpush
