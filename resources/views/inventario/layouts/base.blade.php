{{-- Layout base para el módulo de Inventario --}}
{{-- Extiende de adminlte::page y agrega la clase inventario-module al body --}}

@extends('adminlte::page')

{{-- Agregar clase inventario-module al body para aislar los estilos --}}
@section('classes_body')
    @parent inventario-module
@endsection

{{-- Secciones heredables para las vistas de inventario --}}
@section('title', $title ?? 'Inventario')

@section('adminlte_css')
    @parent
    @stack('css')
    @yield('css')
@stop

@section('adminlte_js')
    @parent
    @stack('js')
    @yield('js')
@stop

