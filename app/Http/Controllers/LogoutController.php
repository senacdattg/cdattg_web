<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function cerrarSesion()
    {
        Auth::logout();
        return redirect('/')->with('success', '¡Sesión cerrada exitosamente!');
    }
    public function logout(){
        Auth::logout();
        return response()->json(['message' => 'Sesión cerrada correctamente'], 200);
    }
}
