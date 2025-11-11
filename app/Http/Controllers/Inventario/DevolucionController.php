<?php

namespace App\Http\Controllers\Inventario;

use App\Exceptions\OrdenException;
use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Devolucion;
use App\Models\ParametroTema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevolucionController extends InventarioController
{
    private const THEME_ORDER_STATES = 'ESTADOS DE ORDEN';
    private const STATE_APPROVED = 'APROBADA';

    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:DEVOLVER PRESTAMO')->only(['index', 'create', 'store']);
    }

    // Mostrar lista de préstamos pendientes de devolución
    public function index()
    {
        $estadoAprobadaId = $this->getEstadoOrdenAprobadaId();

        $prestamos = DetalleOrden::with(['orden.tipoOrden', 'producto', 'devoluciones'])
            ->whereHas('orden', function ($query) {
                $query->whereNotNull('fecha_devolucion'); // Solo préstamos
            })
            ->where('estado_orden_id', $estadoAprobadaId)
            ->paginate(10)
            ->filter(function ($detalle) {
                return !$detalle->estaCompletamenteDevuelto();
            });

        return view('inventario.devoluciones.index', compact('prestamos'));
    }


    // Mostrar formulario de devolución
    public function create($detalleOrdenId)
    {
        $detalleOrden = DetalleOrden::with(['orden', 'producto'])->findOrFail($detalleOrdenId);
        
        if ($detalleOrden->estaCompletamenteDevuelto()) {
            return redirect()->route('inventario.devoluciones.index')
                ->with('error', 'Este préstamo ya fue completamente devuelto.');
        }

        return view('inventario.devoluciones.create', compact('detalleOrden'));
    }

    
    // Registrar devolució
    public function store(Request $request)
    {
        $validated = $request->validate([
            'detalle_orden_id' => 'required|exists:detalle_ordenes,id',
            'cantidad_devuelta' => 'required|integer|min:1',
            'observaciones' => 'nullable|string|max:500'
        ]);

        try {
            $devolucion = Devolucion::registrarDevolucion(
                $validated['detalle_orden_id'],
                $validated['cantidad_devuelta'],
                $validated['observaciones'] ?? null
            );

            $mensaje = 'Devolución registrada exitosamente.';
            
            // Verificar si fue devuelto con retraso
            if ($devolucion->getDiasRetrasoDevolucion() > 0) {
                $mensaje .= ' NOTA: La devolución se realizó con ' . $devolucion->getDiasRetrasoDevolucion() . ' días de retraso.';
            }

            return redirect()->route('inventario.devoluciones.index')
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al registrar la devolución: ' . $e->getMessage());
        }
    }

    
    // Mostrar historial de devoluciones
    public function historial()
    {
        $devoluciones = Devolucion::with(['detalleOrden.producto', 'detalleOrden.orden', 'userCreate'])
            ->orderBy('fecha_devolucion', 'desc')
            ->paginate(20);

        return view('inventario.devoluciones.historial', compact('devoluciones'));
    }


    // Ver detalle de una devolución
    public function show($id)
    {
        $devolucion = Devolucion::with([
            'detalleOrden.producto',
            'detalleOrden.orden',
            'userCreate',
            'userUpdate'
        ])->findOrFail($id);

        return view('inventario.devoluciones.show', compact('devolucion'));
    }
    // Mostrar préstamos activos del usuario actual
    public function misPrestamos()
    {
        $userId = Auth::id();
        $estadoAprobadaId = $this->getEstadoOrdenAprobadaId();

        $prestamos = DetalleOrden::with(['orden.tipoOrden', 'producto', 'devoluciones'])
            ->whereHas('orden', function ($query) use ($userId) {
                // Solo préstamos
                $query->where('user_create_id', $userId)
                      ->whereNotNull('fecha_devolucion');
            })
            ->where('estado_orden_id', $estadoAprobadaId)
            ->paginate(10)
            ->filter(function ($detalle) {
                return !$detalle->estaCompletamenteDevuelto();
            });

        return view('inventario.prestamos.mis', compact('prestamos'));
    }

    // Historial de préstamos del usuario
    public function historialPrestamos()
    {
        $userId = Auth::id();

        $prestamos = DetalleOrden::with(['orden.tipoOrden', 'producto', 'devoluciones'])
            ->whereHas('orden', function ($query) use ($userId) {
                $query->where('user_create_id', $userId)
                      ->whereNotNull('fecha_devolucion'); // Solo préstamos
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('inventario.prestamos.historial', compact('prestamos'));
    }

    private function getEstadoOrdenAprobadaId(): int
    {
        $estadoAprobada = ParametroTema::whereHas('tema', function ($query) {
                $query->where('name', self::THEME_ORDER_STATES);
            })
            ->whereHas('parametro', function ($query) {
                $query->where('name', self::STATE_APPROVED);
            })
            ->first();

        if (!$estadoAprobada) {
            throw new OrdenException("No se encontró el estado '" . self::STATE_APPROVED . "' en los parámetros configurados.");
        }

        return (int) $estadoAprobada->id;
    }
}
