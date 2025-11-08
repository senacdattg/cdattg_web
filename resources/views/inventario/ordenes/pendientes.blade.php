@extends('adminlte::page')

@section('title', 'Órdenes Pendientes')

@section('content_header')
    <x-page-header
        icon="fas fa-hourglass-half"
        title="Órdenes Pendientes"
        subtitle="Órdenes en espera de aprobación"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Órdenes', 'url' => route('inventario.ordenes.index')],
            ['label' => 'Pendientes', 'active' => true]
        ]"
    />
@endsection

@section('content')
    @include('inventario._components.filtros', ['estado' => 'EN ESPERA'])
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush
