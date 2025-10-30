@extends('inventario.layouts.base')

@section('title', 'Dashboard de Inventario')

@section('content_header')
    <x-inventario.page-header
        icon="fas fa-chart-bar"
        title="Dashboard de Inventario"
        subtitle="Resumen general del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('home')],
            ['label' => 'Inventario', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Tarjetas de estadísticas --}}
        <div class="row mb-4">
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
                'link' => route('inventario.categorias.index'),
                'linkText' => 'Ver categorías'
            ])
        </div>

        {{-- Gráficos --}}
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
                        <div class="chart-container" style="height: 300px;">
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
                                    @foreach($productosMasSolicitados as $producto)
                                    <tr>
                                        <td>{{ $producto['nombre'] }}</td>
                                        <td>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-success" style="width: {{ ($producto['solicitudes'] / 45) * 100 }}%"></div>
                                            </div>
                                            <span class="badge bg-success">{{ $producto['solicitudes'] }}</span>
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

        {{-- Productos Recientes y Categorías --}}
        <div class="row">
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

@push('css')
    @vite(['resources/css/inventario/base.css'])
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
