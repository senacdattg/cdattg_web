@extends('adminlte::page')
@push('css')
@vite(['resources/css/style.css'])
@endpush

@section('title', 'Dashboard')
@section('content_header')
<section class="content-header dashboard-header py-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 d-flex align-items-center">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 48px; height: 48px;">
                    <i class="fas fa-tachometer-alt fa-lg text-white"></i>
                </div>
                <div>
                    <h1 class="welcome-title">
                        Bienvenido, <span class="text-primary">Super Administrador</span>
                    </h1>
                    <p class="welcome-subtitle">
                        <i class="far fa-clock mr-1"></i>
                        {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                    </p>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-md-end mb-0">
                        <li class="breadcrumb-item link_right_header">
                            <a href="#"><i class="fas fa-home mr-1"></i>Inicio</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <i class="fas fa-chart-line mr-1"></i>Dashboard
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

@include('components.dashboard.stats-cards')
@include('components.dashboard.charts')
@include('components.dashboard.info-lists')
@include('components.dashboard.widgets')
@include('components.dashboard.charts-scripts')

@endsection

@section('footer')
@include('layout.footer')
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@vite(['resources/js/app.js'])
@endpush