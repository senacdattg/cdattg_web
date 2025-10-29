{{-- Layout base para el módulo de inventario --}}
@extends('adminlte::page')

@section('plugins.Sweetalert2', true)

@vite([
    'resources/css/inventario/shared/base.css',
    'resources/css/inventario/sidebar-fix.css',
    'resources/css/inventario/shared/modal-imagen.css',
    'resources/js/inventario/shared/modal-imagen.js'
])

@stack('styles')

@section('content_header')
    @yield('header')
@stop

@section('content')
    {{-- Mensajes de sesión --}}
    @include('inventario._components.alerts')
    
    {{-- Contenido principal --}}
    @yield('main-content')
@stop

@stack('scripts')
