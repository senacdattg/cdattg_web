@extends('adminlte::page')
@push('css')
    @vite(['resources/css/dashboards/dashboard-superadmin.css'])
@endpush

@section('title', 'Dashboard')
@section('content_header')
    <x-page-header icon="fa-home" title="Página" subtitle="Descripción de la página" :breadcrumb="[]" />
@endsection
@section('content')

    @include('components.dashboard-superadmin.stats-cards')
    @include('components.dashboard-superadmin.charts')
    @include('components.dashboard-superadmin.info-lists')
    @include('components.dashboard-superadmin.widgets')

@endsection

@section('footer')
    @include('layout.footer')
@endsection

@push('js')
    <script src="{{ asset('vendor/chart.js/Chart.bundle.min.js') }}"></script>
    @vite(['resources/js/app.js'])
    @vite(['resources/js/dashboards/superadmin/charts-scripts.js'])
@endpush
