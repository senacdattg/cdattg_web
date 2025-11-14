<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function toggleStatus(User $user)
    {
        try {
            $this->userService->cambiarEstado($user->id, Auth::id());

            return redirect()->back()->with('success', 'Estado actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado: ' . $e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function assignRoles(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'roles' => 'nullable|array',
                'roles.*' => 'required|string|exists:roles,name',
                'available_roles' => 'nullable|array',
                'available_roles.*' => 'required|string|exists:roles,name',
            ]);

            // Prevenir que el usuario modifique sus propios roles
            if ($data['user_id'] == auth()->id()) {
                return redirect()->back()->with('error', 'No puedes modificar tus propios roles.');
            }

            $roles = collect($request->input('roles', []))
                ->merge($request->input('available_roles', []))
                ->filter()
                ->map(static fn(string $role) => strtoupper($role))
                ->unique()
                ->values()
                ->all();

            $this->userService->agregarRoles($data['user_id'], $roles);

            return redirect()->back()->with('success', 'Roles asignados correctamente');
        } catch (\Exception $e) {
            Log::error('Error al asignar roles: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudieron asignar los roles: ' . $e->getMessage());
        }
    }
}
