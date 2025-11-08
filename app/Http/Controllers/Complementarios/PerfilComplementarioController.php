<?php

namespace App\Http\Controllers\Complementarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ComplementarioService;

class PerfilComplementarioController extends Controller
{
    protected $complementarioService;

    public function __construct(ComplementarioService $complementarioService)
    {
        $this->complementarioService = $complementarioService;
    }

    /**
     * Mostrar perfil propio del usuario autenticado
     */
    public function Perfil()
    {
        $user = Auth::user();

        // Verificar que el usuario esté autenticado
        if (!$user) {
            return redirect('/login')->with('error', 'Debe iniciar sesión para acceder a su perfil.');
        }

        // No se requiere permiso 'VER PERSONA' porque solo se muestra la información propia del usuario

        // Obtener la persona del usuario
        $persona = $user->persona;

        if (!$persona) {
            return redirect()->route('home')->with('error', 'No se encontró información de persona para este usuario.');
        }

        // Si es aspirante, también obtener sus programas complementarios
        $aspirantes = [];
        if ($user->hasRole('ASPIRANTE')) {
            $aspirantes = \App\Models\AspiranteComplementario::with(['persona', 'complementario'])
                ->where('persona_id', $user->persona_id)
                ->get();
        }

        // Obtener tipos de documento y géneros dinámicamente
        $tiposDocumento = $this->complementarioService->getTiposDocumento();
        $generos = $this->complementarioService->getGeneros();

        return view('personas.show', compact('persona', 'aspirantes', 'user', 'tiposDocumento', 'generos'));
    }
}