@extends('adminlte::page')

@section('title', 'Órdenes Rechazadas')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-times-circle"
        title="Órdenes Rechazadas"
        subtitle="Órdenes rechazadas o canceladas"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Órdenes', 'url' => route('inventario.ordenes.index')],
            ['label' => 'Rechazadas', 'active' => true]
        ]"
    />
@endsection

@section('content')
    @include('inventario._components.filtros', ['estado' => 'RECHAZADA'])
@endsection

@section('footer')
    @include('layout.footer')
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

