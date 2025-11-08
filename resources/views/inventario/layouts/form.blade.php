{{-- 
    Layout para formularios de creación/edición usando @yield
    Props:
    - $title (string): Título del formulario
    - $icon (string): Icono del título
    - $action (string): URL de acción
    - $method (string): Método HTTP
    - $submitText (string): Texto del botón submit
    - $cancelRoute (string): Ruta para cancelar
--}}
@extends('inventario.layouts.base')

@push('styles')
    @vite([
        'resources/css/inventario/shared/base.css',
        'resources/css/inventario/productos.css'
    ])
@endpush

@section('content_header')
    <h1><i class="{{ $icon ?? 'fas fa-edit' }} mr-2"></i> {{ $title }}</h1>
@endsection

@section('main-content')
    <div class="container inventario-container">
        <div class="card">
            @include('inventario._components.card-header', [
                'title' => $title,
                'icon' => $icon ?? 'fas fa-edit'
            ])
            
            <div class="card-body">
                <form action="{{ $action }}" method="POST">
                    @csrf
                    @if(isset($method) && $method !== 'POST')
                        @method($method)
                    @endif
                    
                    {{-- Contenido del formulario usando @yield --}}
                    @yield('form-content')
                    
                    {{-- Botones de acción --}}
                    @include('inventario._components.form-actions', [
                        'submitText' => $submitText ?? 'Guardar',
                        'submitIcon' => $submitIcon ?? 'fas fa-save',
                        'cancelRoute' => $cancelRoute ?? null,
                        'cancelText' => $cancelText ?? 'Cancelar',
                        'showReset' => $showReset ?? false,
                        'resetText' => $resetText ?? 'Limpiar'
                    ])
                    
                    {{-- Nota informativa --}}
                    @if(isset($showNote) && $showNote)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Nota:</strong> {{ $noteText ?? 'Todos los campos marcados con * son obligatorios.' }}
                                </div>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite([
        'resources/js/inventario/productos.js'
    ])
    @yield('form-scripts')
@endpush

