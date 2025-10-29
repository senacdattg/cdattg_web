@extends('adminlte::page')

@section('title', 'Ver Producto')

@section('content_header')
    <x-page-header
        icon="fas fa-eye"
        title="Ver Producto"
        subtitle="Información detallada del producto"
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
        'resources/css/inventario/productos.css',
        'resources/css/inventario/carrito.css',
        'resources/css/inventario/shared/modal-imagen.css',
        'resources/js/inventario/productos.js'
    ])
@endpush

@section('classes_body', 'productos-page')

@section('main-content')
    <div class="flex_show">
        <div class="container_show">
            <div class="div_show">
                {{-- Información del producto --}}
                @include('inventario._components.product-info', ['producto' => $producto])
                
                {{-- Imagen del producto --}}
                @include('inventario._components.product-image', ['producto' => $producto])
            </div>
            
            {{-- Botones de acción usando componente --}}
            <div class="div_btn">
                <a href="{{ route('inventario.productos.index') }}" class="btn_show">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                
                @can('EDITAR PRODUCTO')
                    <a href="{{ route('inventario.productos.edit', $producto->id) }}" class="btn_show btn_warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                @endcan
                
                @can('ELIMINAR PRODUCTO')
                    <button type="button" class="btn_show btn_danger" onclick="confirmarEliminacion()">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                    
                    <form id="form-eliminar" 
                            action="{{ route('inventario.productos.destroy', $producto->id) }}" 
                            method="POST" 
                            style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                @endcan
            </div>
        </div>
        
        {{-- Widget del carrito --}}
        @include('inventario._components.cart-widget', ['producto' => $producto])
    </div>

    {{-- Modal para imagen expandible --}}
    @include('inventario._components.image-modal')
@endsection

