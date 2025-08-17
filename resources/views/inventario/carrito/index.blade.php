{{-- filepath: resources/views/inventario/carrito/index.blade.php --}}
@extends('adminlte::page')

@vite(['resources/css/inventario/carrito.css', 'resources/css/inventario/shared/modal-imagen.css', 'resources/js/inventario/shared/modal-imagen.js'])

@section('content')
@php
    $carrito = [
        (object)[
            'id' => 1,
            'producto' => (object)[
                'producto' => 'Mouse inalámbrico',
                'imagen' => 'img/inventario/imagen_default.png'
            ],
            'cantidad' => 2
        ],
        (object)[
            'id' => 2,
            'producto' => (object)[
                'producto' => 'Teclado mecánico',
                'imagen' => 'img/inventario/imagen_default.png'
            ],
            'cantidad' => 1
        ],
        (object)[
            'id' => 3,
            'producto' => (object)[
                'producto' => 'Teclado',
                'imagen' => 'img/inventario/imagen_default.png'
            ],
            'cantidad' => 1
        ],
        (object)[
            'id' => 4,
            'producto' => (object)[
                'producto' => 'Teclado',
                'imagen' => 'img/inventario/imagen_default.png'
            ],
            'cantidad' => 1
        ],
         (object)[
            'id' => 1,
            'producto' => (object)[
                'producto' => 'Mouse inalámbrico',
                'imagen' => 'img/inventario/imagen_default.png'
            ],
            'cantidad' => 2
        ],
        (object)[
            'id' => 2,
            'producto' => (object)[
                'producto' => 'Teclado mecánico',
                'imagen' => 'img/inventario/imagen_default.png'
            ],
            'cantidad' => 1
        ],
        (object)[
            'id' => 3,
            'producto' => (object)[
                'producto' => 'Teclado',
                'imagen' => 'img/inventario/imagen_default.png'
            ],
            'cantidad' => 1
        ],
        (object)[
            'id' => 4,
            'producto' => (object)[
                'producto' => 'Teclado',
                'imagen' => 'img/inventario/imagen_default.png'
            ],
            'cantidad' => 1
        ],

    ];
@endphp

<div class="div_flex">
    <div class="div_carrito">
        <h2><i class="fas fa-shopping-cart"></i> Carrito </h2>
        <table class="carrito-table mt-3">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($carrito as $item)
                <tr>
                    <td>
                        <img src="{{ asset($item->producto->imagen ?? 'img/inventario/default.png') }}"
                             alt="Foto"
                             class="img img-expandable">
                    <td>{{ $item->producto->producto }}</td>
                    <td>{{ $item->cantidad }}</td>
                    <td>
                        <form method="POST">
                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> 
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    
    <div class="div_lateral">
        <div class="div_pedido">
            <div class="div_titulo">
                <h2><i class="fas fa-receipt"></i> Pedido </h2>
            </div>
            <div class="div_titulo">
                <p><strong>Productos:</strong> {{ count($carrito) }}</p>
            </div>
            <form>
                @csrf
                <div class="div_btn">
                    <button type="submit" class="btn-pedido">
                        <i class="fas fa-paper-plane"></i> Enviar 
                    </button>
                </div>
            </form>
        </div>

        <div class="resumen">
            <div class="div_titulo">
                <h2><i class="fas fa-list-alt"></i> Resumen </h2>
            </div>
            <ul>
                @foreach($carrito as $item)
                    <li>{{ $item->cantidad }} x {{ $item->producto->producto }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<!-- Modal para imagen expandida -->
<div id="modalImagen" class="modal-imagen">
    <span class="cerrar">&times;</span>
    <img class="modal-contenido" id="imgExpandida">
</div>
@endsection
