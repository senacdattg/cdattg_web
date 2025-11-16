<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ConfirmPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        return view('vendor.adminlte.auth.passwords.confirm');
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors([
                'password' => 'La contraseña no coincide.',
            ]);
        }

        // Marca en sesión la confirmación de contraseña para el middleware 'password.confirm'
        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended();
    }
}
