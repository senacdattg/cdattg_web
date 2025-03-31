@extends('adminlte::page')
@push('css')
<link rel="stylesheet" href="{{ asset('admin-lte/dist/css/adminlte.min.css') }}">
<style>
    .card {
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .dashboard-card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid rgba(0, 0, 0, .125);
    }

    .card-title {
        font-weight: 500;
        color: #2d3748;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
    }

    .dashboard-header {
        background: linear-gradient(to right, #fff, #f8f9fa);
        border-bottom: 1px solid rgba(0, 0, 0, .05);
        box-shadow: 0 2px 4px rgba(0, 0, 0, .03);
    }

    .welcome-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .welcome-subtitle {
        color: #718096;
        font-size: 0.95rem;
        margin-bottom: 0;
    }

    .breadcrumb-item {
        font-size: 0.875rem;
    }

    .breadcrumb-item a {
        color: #4a5568;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #718096;
    }

    .breadcrumb-item+.breadcrumb-item::before {
        content: "›";
        color: #a0aec0;
    }
</style>
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
                        {{ now()->format('l, d \d\e F \d\e Y') }}
                    </p>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-md-end mb-0">
                        <li class="breadcrumb-item">
                            <a href="#"><i class="fas fa-home mr-1"></i>Home</a>
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

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $('.collapse').collapse();
    });
</script>
@endpush