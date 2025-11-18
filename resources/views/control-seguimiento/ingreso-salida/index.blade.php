@extends('adminlte::page')

@section('plugins.Chartjs', true)

@section('title', 'Dashboard - Ingreso y Salida')

@section('content_header')
    <x-page-header icon="fa-chart-line" title="Dashboard - Ingreso y Salida"
        subtitle="Estado actual de ingresos y salidas por sede" :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
            ['label' => 'Control y Seguimiento', 'icon' => 'fa-clipboard-check'],
            ['label' => 'Ingreso y Salida', 'icon' => 'fa-sign-in-alt', 'active' => true],
        ]" />
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            @livewire('control-seguimiento.ingreso-salida-dashboard')
        </div>
    </section>
@endsection

@section('js')
    @vite(['resources/js/app.js'])
    @stack('js')
@endsection
