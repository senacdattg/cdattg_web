<?php

namespace App\Http\Controllers;

use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index()
    {
        $user = Auth::user();
        $persona = $user->persona;
        $rolesAsignados = $user->roles->pluck('name');

        return view('profile.index', compact('user', 'persona', 'rolesAsignados'));
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            $datos = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'current_password' => 'nullable|required_with:password',
                'password' => 'nullable|min:8|confirmed',
            ]);

            $this->profileService->actualizarPerfil($user, $datos);

            return redirect()->route('profile.index')->with('success', 'Perfil actualizado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function showChangePassword()
    {
        $user = Auth::user();
        return view('profile.change-password', compact('user'));
    }

    public function changePassword(Request $request)
    {
        try {
            $datos = $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed|different:current_password',
            ]);

            $user = Auth::user();

            $this->profileService->cambiarContrasena(
                $user,
                $datos['current_password'],
                $datos['password']
            );

            return back()->with('success', 'Contraseña actualizada correctamente');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
