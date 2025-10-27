@extends('inventario.layouts.base')

@push('styles')
    @vite([
        'resources/css/inventario/productos.css',
        'resources/css/inventario/carrito.css',
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
            
            {{-- Botones de acción --}}
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

