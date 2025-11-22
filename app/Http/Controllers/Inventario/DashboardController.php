<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\Producto;
use App\Models\Inventario\Categoria;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:VER DASHBOARD INVENTARIO')->only(['index']);
    }

    public function index() : View
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

        // Productos más solicitados desde las órdenes
        $productosMasSolicitados = DB::table('detalle_ordenes')
            ->join('productos', 'detalle_ordenes.producto_id', '=', 'productos.id')
            ->select('productos.producto', DB::raw('SUM(detalle_ordenes.cantidad) as solicitudes'))
            ->groupBy('productos.id', 'productos.producto')
            ->orderBy('solicitudes', 'desc')
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'nombre' => $item->producto,
                    'solicitudes' => $item->solicitudes
                ];
            })
            ->toArray();

        // Si no hay datos, usar array vacío
        if (empty($productosMasSolicitados)) {
            $productosMasSolicitados = [];
        }

        // Productos por vencer (próximos 30 días)
        $productosPorVencer = Producto::whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '>', Carbon::now())
            ->whereDate('fecha_vencimiento', '<=', Carbon::now()->addDays(30))
            ->count();

        // Productos con stock bajo (menos de 10 unidades)
        $productosStockBajo = Producto::where('cantidad', '<', 10)->count();

        // Total de categorías
        $temaCategorias = \App\Models\Tema::where('name', 'CATEGORIAS')->first();
        $totalCategorias = $temaCategorias
            ? $temaCategorias->parametros()->wherePivot('status', 1)->count()
            : 0;

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
            'productosMasSolicitados'
        ));
    }
}