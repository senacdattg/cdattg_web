@extends('adminlte::page')

@section('title', 'Órdenes Aprobadas')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

@push('css')
    @vite(['resources/css/inventario/shared/base.css'])
@endpush

@section('content_header')
    <x-page-header
        icon="fas fa-check-circle"
        title="Órdenes Aprobadas"
        subtitle="Órdenes completadas y aprobadas"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Órdenes', 'url' => route('inventario.ordenes.index')],
            ['label' => 'Aprobadas', 'active' => true]
        ]"
    />
@endsection

@section('content')
    @include('inventario._components.filtros', ['estado' => 'APROBADA'])
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

