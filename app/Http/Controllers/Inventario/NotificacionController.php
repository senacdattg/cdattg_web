<?php

namespace App\Http\Controllers\Inventario;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventario\Notificacion;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class NotificacionController extends InventarioController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('can:VER NOTIFICACION')->only(['index']);
    }

    /**
     * Mostrar todas las notificaciones del usuario
     */
    public function index() : View
    {
        $notificaciones = Auth::user()->notifications()
            ->paginate(10);
        
        return view('inventario.notificaciones.index', compact('notificaciones'));
    }

    /**
     * Obtener notificaciones no leídas para el dropdown
     */
    public function getUnread() : JsonResponse
    {
        $notificaciones = Auth::user()->unreadNotifications()
            ->take(5)
            ->get();
        
        $count = Auth::user()->unreadNotifications()->count();
        
        return response()->json([
            'notificaciones' => $notificaciones,
            'count' => $count
        ]);
    }

    /**
     * Marcar una notificación como leída
     */
    public function markAsRead($id) : JsonResponse
    {
        $notificacion = Auth::user()->notifications()
            ->where('id', $id)
            ->first();
        
        if ($notificacion) {
            $notificacion->markAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'Notificación marcada como leída'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Notificación no encontrada'
        ], 404);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllAsRead() : JsonResponse
    {
        Auth::user()->unreadNotifications->each(function ($notification) {
            $notification->markAsRead();
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones marcadas como leídas'
        ]);
    }

    /**
     * Eliminar una notificación
     */
    public function destroy($id) : RedirectResponse
    {
        $notificacion = Auth::user()->notifications()
            ->where('id', $id)
            ->first();
        
        if ($notificacion) {
            $notificacion->delete();
            
            return back()->with('success', 'Notificación eliminada exitosamente');
        }
        
        return back()->with('error', 'Notificación no encontrada');
    }

    /**
     * Eliminar todas las notificaciones del usuario
     */
    public function destroyAll()  : JsonResponse
    {
        $count = Auth::user()->notifications()->count();
        
        Auth::user()->notifications()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones han sido eliminadas',
            'deleted' => $count
        ]);
    }
}
