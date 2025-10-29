@extends('adminlte::page')

@section('title', 'Carrito de Compras')

@section('content_header')
    <x-page-header
        icon="fas fa-shopping-cart"
        title="Carrito de Compras"
        subtitle="Productos seleccionados para el inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true]
        ]"
    />
@endsection

@section('content')
    
    {{-- Alertas --}}
    @include('layout.alertas')
    
    {{-- Footer SENA --}}
    @include('inventario._components.sena-footer')
    
@push('css')
    @vite(['resources/css/style.css'])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('styles')
    @vite([
        'resources/css/inventario/carrito.css'
    ])
@endpush

@section('content_header')
    <h1><i class="fas fa-shopping-cart mr-2"></i> Carrito de Compras</h1>
@endsection

@section('main-content')
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
    ];
@endphp

<div class="div_flex">
    {{-- Tabla del carrito usando componente --}}
    <div class="div_carrito">
        @include('inventario._components.card-header', [
            'title' => 'Carrito',
            'icon' => 'fas fa-shopping-cart',
            'bgClass' => 'bg-primary',
            'textClass' => 'text-white'
        ])
        
        <div class="card-body">
            @component('inventario._components.data-table', [
                'headers' => [
                    'imagen' => 'Foto',
                    'producto' => 'Producto',
                    'cantidad' => 'Cantidad'
                ],
                'data' => $carrito,
                'actions' => [], // El carrito usa local storage, no rutas del servidor
                'emptyMessage' => 'El carrito está vacío.',
                'emptyIcon' => 'fas fa-shopping-cart',
                'tableClass' => 'carrito-table'
            ])
            @endcomponent
        </div>
    </div>

    {{-- Panel lateral --}}
    <div class="div_lateral">
        {{-- Resumen del pedido --}}
        <div class="div_pedido">
            @include('inventario._components.card-header', [
                'title' => 'Pedido',
                'icon' => 'fas fa-receipt',
                'bgClass' => 'bg-success',
                'textClass' => 'text-white'
            ])
            
            <div class="card-body">
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
        </div>

        {{-- Resumen detallado --}}
        <div class="div_resumen">
            @include('inventario._components.card-header', [
                'title' => 'Resumen',
                'icon' => 'fas fa-list-alt',
                'bgClass' => 'bg-info',
                'textClass' => 'text-white'
            ])
            
            <div class="card-body">
                <ul>
                    @foreach($carrito as $item)
                        <li>{{ $item->cantidad }} x {{ $item->producto->producto }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Modal para imagen expandible --}}
@include('inventario._components.image-modal')
@endsection
