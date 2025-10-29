@extends('inventario.layouts.base')

@section('title', 'Dashboard Inventario')

@section('content_header')
    <div class="header-container">
        <div class="header-content">
            <div class="user-welcome">
                <div class="welcome-text">
                    <h1>Bienvenido, <span class="user-name">{{ auth()->user()->name }}</span></h1>
                    <p class="date-text">
                        <i class="far fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@stop

@push('styles')
    @vite([
        'resources/css/inventario/shared/base.css',
        'resources/css/inventario/dashboard.css'
    ])
@endpush

@section('main-content')
    <div class="container-fluid">
        <div class="row">
            {{-- Tarjetas de estadísticas usando componentes --}}
            @include('inventario._components.stats-card', [
                'title' => 'Total Productos',
                'value' => $totalProductos,
                'icon' => 'fas fa-boxes',
                'bgClass' => 'bg-info',
                'link' => route('inventario.productos.index'),
                'linkText' => 'Ver productos'
            ])

            @include('inventario._components.stats-card', [
                'title' => 'Productos por Vencer',
                'value' => $productosPorVencer,
                'icon' => 'fas fa-calendar-alt',
                'bgClass' => 'bg-warning',
                'link' => '#',
                'linkText' => 'Más información'
            ])

            @include('inventario._components.stats-card', [
                'title' => 'Stock Bajo',
                'value' => $productosStockBajo,
                'icon' => 'fas fa-exclamation-triangle',
                'bgClass' => 'bg-danger',
                'link' => '#',
                'linkText' => 'Más información'
            ])

            @include('inventario._components.stats-card', [
                'title' => 'Categorías',
                'value' => $totalCategorias,
                'icon' => 'fas fa-tags',
                'bgClass' => 'bg-success',
                'link' => '#',
                'linkText' => 'Más información'
            ])
        </div>

        <!-- Nueva fila para gráfico de consumibles/no consumibles -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    @include('inventario._components.card-header', [
                        'title' => 'Productos por Tipo',
                        'icon' => 'fas fa-chart-bar',
                        'bgClass' => 'bg-primary',
                        'textClass' => 'text-white'
                    ])
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="productosConsumibles"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    @include('inventario._components.card-header', [
                        'title' => 'Productos Más Solicitados',
                        'icon' => 'fas fa-chart-line',
                        'bgClass' => 'bg-success',
                        'textClass' => 'text-white'
                    ])
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Solicitudes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productosMasSolicitados as $productosMas)
                                    <tr>
                                        <td>{{ $productosMas['nombre'] }}</td>
                                        <td>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-success" style="width: {{ ($productosMas['solicitudes'] / 45) * 100 }}%"></div>
                                            </div>
                                            <span class="badge bg-success">{{ $productosMas['solicitudes'] }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Productos Recientes -->
            <div class="col-md-6">
                <div class="card">
                    @include('inventario._components.card-header', [
                        'title' => 'Productos Recientes',
                        'icon' => 'fas fa-box',
                        'bgClass' => 'bg-info',
                        'textClass' => 'text-white'
                    ])
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productosRecientes as $producto)
                                <tr>
                                    <td>{{ $producto->producto }}</td>
                                    <td>{{ $producto->cantidad }}</td>
                                    <td>
                                        <span class="badge {{ $producto->estado->parametro->name === 'Disponible' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $producto->estado->parametro->name }}
                                        </span>
                                    </td>
                                    <td>{{ $producto->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Productos por Categoría -->
            <div class="col-md-6">
                <div class="card">
                    @include('inventario._components.card-header', [
                        'title' => 'Productos por Categoría',
                        'icon' => 'fas fa-chart-pie',
                        'bgClass' => 'bg-warning',
                        'textClass' => 'text-white'
                    ])
                    <div class="card-body">
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="productosPorCategoria"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Productos Consumibles vs No Consumibles
    var ctxConsumibles = document.getElementById('productosConsumibles').getContext('2d');
    new Chart(ctxConsumibles, {
        type: 'bar',
        data: {
            labels: ['Consumibles', 'No Consumibles'],
            datasets: [{
                label: 'Cantidad de Productos',
                data: [{{ $productosConsumibles }}, {{ $productosNoConsumibles }}],
                backgroundColor: ['#00a65a', '#f39c12'],
                borderColor: ['#00a65a', '#f39c12'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    var ctx = document.getElementById('productosPorCategoria').getContext('2d');
    var data = @json($productosPorCategoria);
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(item => item.categoria),
            datasets: [{
                data: data.map(item => item.total),
                backgroundColor: [
                    '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc',
                    '#d2d6de', '#6c757d', '#007bff', '#17a2b8', '#28a745'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'right'
            }
        }
    });
});
</script>
@endpush

@section('css')
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Productos Consumibles vs No Consumibles
    var ctxConsumibles = document.getElementById('productosConsumibles').getContext('2d');
    new Chart(ctxConsumibles, {
        type: 'bar',
        data: {
            labels: ['Consumibles', 'No Consumibles'],
            datasets: [{
                label: 'Cantidad de Productos',
                data: [{{ $productosConsumibles }}, {{ $productosNoConsumibles }}],
                backgroundColor: ['#00a65a', '#f39c12'],
                borderColor: ['#00a65a', '#f39c12'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    var ctx = document.getElementById('productosPorCategoria').getContext('2d');
    var data = @json($productosPorCategoria);
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(item => item.categoria),
            datasets: [{
                data: data.map(item => item.total),
                backgroundColor: [
                    '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc',
                    '#d2d6de', '#6c757d', '#007bff', '#17a2b8', '#28a745'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'right'
            }
        }
    });
});
</script>
@stop

