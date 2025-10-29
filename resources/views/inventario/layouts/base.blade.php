{{-- Layout base para el módulo de inventario --}}
@extends('adminlte::page')

@section('plugins.Sweetalert2', true)

@vite([
    'resources/css/inventario/shared/base.css'
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
