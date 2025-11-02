@extends('adminlte::page')

@section('title', 'Préstamos')

@section('content_header')
    <x-page-header
        icon="fas fa-sign-in-alt"
        title="Préstamos"
        subtitle="Órdenes de tipo préstamo"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Préstamos y Salidas', 'url' => route('inventario.prestamos-salidas')],
            ['label' => 'Préstamos', 'active' => true]
        ]"
    />
@endsection

@section('content')
    @include('inventario._components.filtros', ['tipo' => 'PRÉSTAMO'])
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@push('css')
    @vite(['public/css/inventario/shared/base.css'])
@endpush
