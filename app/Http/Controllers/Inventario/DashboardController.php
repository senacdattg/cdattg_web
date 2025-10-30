<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\Producto;
use App\Models\Inventario\Categoria;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Total de productos
        $totalProductos = Producto::count();

        // Productos consumibles y no consumibles
        $productosConsumibles = Producto::whereHas('tipoProducto', function($query) {
            $query->whereHas('parametro', function($subQuery) {
                $subQuery->where('name', 'CONSUMIBLE');
            });
        })->count();

        $productosNoConsumibles = Producto::whereHas('tipoProducto', function($query) {
            $query->whereHas('parametro', function($subQuery) {
                $subQuery->where('name', 'NO CONSUMIBLE');
            });
        })->count();

        // Productos más y menos solicitados (datos de ejemplo por ahora)
        $productosMasSolicitados = [
            ['nombre' => 'Laptop Dell XPS', 'solicitudes' => 45],
            ['nombre' => 'Monitor HP 24"', 'solicitudes' => 38],
            ['nombre' => 'Teclado Mecánico', 'solicitudes' => 32],
            ['nombre' => 'Mouse Inalámbrico', 'solicitudes' => 30],
            ['nombre' => 'Webcam HD', 'solicitudes' => 25],
        ];

        $productosMenosSolicitados = [
            ['nombre' => 'Cable HDMI', 'solicitudes' => 3],
            ['nombre' => 'Adaptador USB', 'solicitudes' => 4],
            ['nombre' => 'Hub USB', 'solicitudes' => 5],
            ['nombre' => 'Cargador Universal', 'solicitudes' => 6],
            ['nombre' => 'Funda Laptop', 'solicitudes' => 7],
        ];

        // Productos por vencer (próximos 30 días)
        $productosPorVencer = Producto::whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '>', Carbon::now())
            ->whereDate('fecha_vencimiento', '<=', Carbon::now()->addDays(30))
            ->count();

        // Productos con stock bajo (menos de 10 unidades)
        $productosStockBajo = Producto::where('cantidad', '<', 10)->count();

        // Total de categorías
        $totalCategorias = Categoria::count();

        // Productos recientes (últimos 5)
        $productosRecientes = Producto::with(['estado', 'estado.parametro'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Productos por categoría para el gráfico
        $productosPorCategoria = DB::table('productos')
            ->join('parametros', 'productos.categoria_id', '=', 'parametros.id')
            ->select('parametros.name as categoria', DB::raw('count(*) as total'))
            ->groupBy('parametros.id', 'parametros.name')
            ->get();

        return view('inventario.dashboard.index', compact(
            'totalProductos',
            'productosPorVencer',
            'productosStockBajo',
            'totalCategorias',
            'productosRecientes',
            'productosPorCategoria',
            'productosConsumibles',
            'productosNoConsumibles',
            'productosMasSolicitados',
            'productosMenosSolicitados'
        ));
    }
}