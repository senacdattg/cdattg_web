@extends('adminlte::page')

@section('title', 'Dashboard de Inventario')

@section('content_header')
    <x-page-header
        icon="fas fa-chart-bar"
        title="Dashboard de Inventario"
        subtitle="Resumen general del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '/home'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Dashboard', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Tarjetas de estadísticas --}}
        <div class="row mb-4">
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

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $productosPorVencer }}</h3>
                        <p>Productos por Vencer</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <a href="{{ route('inventario.productos.index') }}" class="small-box-footer">
                        Más información <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $productosStockBajo }}</h3>
                        <p>Stock Bajo</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <a href="{{ route('inventario.productos.index') }}" class="small-box-footer">
                        Más información <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $totalCategorias }}</h3>
                        <p>Categorías</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <a href="{{ route('inventario.categorias.index') }}" class="small-box-footer">
                        Ver categorías <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Gráficos --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-2"></i>
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
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-2"></i>
                            Productos Más Solicitados
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <caption id="dashboard-description" class="sr-only">
                                    Estadisticas de productos más solicitados con información de producto y cantidad de solicitudes.
                                </caption>
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Solicitudes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productosMasSolicitados as $producto)
                                    <tr>
                                        <td>{{ $producto['nombre'] }}</td>
                                        <td>
                                            @php
                                                $maxSolicitudes = collect($productosMasSolicitados)->max('solicitudes') ?: 1;
                                            @endphp
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-success" style="width: {{ ($producto['solicitudes'] / $maxSolicitudes) * 100 }}%"></div>
                                            </div>
                                            <span class="badge bg-success">{{ $producto['solicitudes'] }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No hay datos de solicitudes</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Productos Recientes y Categorías --}}
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title">
                            <i class="fas fa-box mr-2"></i>
                            Productos Recientes
                        </h3>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-hover">
                            <caption id="dashboard-description" class="sr-only">
                                Lista de productos recientes con información de producto, cantidad, estado y fecha de creación.
                            </caption>
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productosRecientes as $producto)
                                <tr>
                                    <td>{{ $producto->producto }}</td>
                                    <td>{{ $producto->cantidad }}</td>
                                    <td>
                                        @if($producto->estado && $producto->estado->parametro)
                                            <span class="badge {{ $producto->estado->parametro->name === 'DISPONIBLE' ? 'bg-success' : 'bg-warning' }}">
                                                {{ $producto->estado->parametro->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Sin estado</span>
                                        @endif
                                    </td>
                                    <td>{{ $producto->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay productos recientes</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-2"></i>
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

    {{-- Footer SENA --}}
    @include('layout.footer')
@endsection

@push('css')
    @vite([
        'resources/css/inventario/shared/base.css',
    ])
    <style>
        .chart-container {
            position: relative;
            height: 300px;
        }
        .progress-xs {
            height: 10px;
        }
    </style>
@endpush

@push('js')
<script src="{{ asset('vendor/chart.js/Chart.bundle.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Productos Consumibles vs No Consumibles
    const ctxConsumibles = document.getElementById('productosConsumibles');
    if (ctxConsumibles) {
        new Chart(ctxConsumibles.getContext('2d'), {
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
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Gráfico de Productos por Categoría
    const ctxCategoria = document.getElementById('productosPorCategoria');
    if (ctxCategoria) {
        const data = @json($productosPorCategoria);
        
        if (data && data.length > 0) {
            new Chart(ctxCategoria.getContext('2d'), {
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
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush

