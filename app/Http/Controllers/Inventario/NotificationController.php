<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventario\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        // Temporalmente removemos la autorización para probar
        // $this->authorize('VER NOTIFICACIONES');

        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('inventario.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        // Temporalmente removemos la autorización para probar
        // $this->authorize('MARCAR NOTIFICACIONES LEIDAS');

        $notification = Notification::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $notification->update(['read_at' => now()]);
        return back();
    }
}
