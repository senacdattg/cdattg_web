{{-- filepath: resources/views/inventario/carrito/index.blade.php --}}
@extends('adminlte::page')

@vite(['resources/css/inventario/carrito.css', 'resources/js/inventario/carrito.js'])

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
        ]
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
                        style="width:50px; height:50px; border-radius:8px;">
                    </td>
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

    <div class="div_pedido">
        <div class="div_titulo">
            <h2><i class="fas fa-receipt"></i> Pedido </h2>
        </div>
        <div class="div_producto_titulo">
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
</div>

@endsection