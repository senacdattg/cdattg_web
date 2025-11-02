@extends('adminlte::page')

@section('title', 'Salidas')

@section('content_header')
    <x-page-header
        icon="fas fa-sign-out-alt"
        title="Salidas"
        subtitle="Órdenes de tipo salida"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Préstamos y Salidas', 'url' => route('inventario.prestamos-salidas')],
            ['label' => 'Salidas', 'active' => true]
        ]"
    />
@endsection

@section('content')
    @include('inventario._components.filtros', ['tipo' => 'SALIDA'])
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@push('css')
    @vite(['public/css/inventario/shared/base.css'])
@endpush

@section('footer')
    @include('layout.footer')
@endsection

@push('css')
    @vite(['public/css/inventario/shared/base.css'])
@endpush
