@extends('adminlte::page')

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

@vite([
    'resources/css/inventario/shared/base.css',
    'resources/css/inventario/dashboard.css'
])

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Tarjeta de Total de Productos -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalProductos }}</h3>
                    <p>Total Productos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <a href="{{ route('inventario.productos.index') }}" class="small-box-footer">
                    Ver productos <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Tarjeta de Productos por Vencer -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $productosPorVencer }}</h3>
                    <p>Productos por Vencer</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Tarjeta de Stock Bajo -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $productosStockBajo }}</h3>
                    <p>Stock Bajo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Tarjeta de Categorías -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalCategorias }}</h3>
                    <p>Categorías</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tags"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Nueva fila para gráfico de consumibles/no consumibles -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Productos por Tipo
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="productosConsumibles"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-1"></i>
                        Productos Más Solicitados
                    </h3>
                </div>
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
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-box mr-1"></i>
                        Productos Recientes
                    </h3>
                </div>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <table class="table table-head-fixed text-nowrap">
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
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Productos por Categoría
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="productosPorCategoria"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

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