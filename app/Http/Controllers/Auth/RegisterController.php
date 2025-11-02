<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    public function create(Request $request)
    {
        // Validación completa
        $validatedData = $request->validate([
            'primer_nombre' => 'required|string|max:255',
            'segundo_nombre' => 'nullable|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'numero_documento' => 'required|string|unique:users,documento|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:255',
            'pais_id' => 'nullable|exists:paises,id',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'municipio_id' => 'nullable|exists:municipios,id',
            'direccion' => 'nullable|string|max:255',
        ]);

        // Convertir a mayúsculas los campos
        $validatedData['primer_nombre'] = strtoupper($validatedData['primer_nombre']);
        $validatedData['segundo_nombre'] = strtoupper($validatedData['segundo_nombre'] ?? '');
        $validatedData['primer_apellido'] = strtoupper($validatedData['primer_apellido']);
        $validatedData['segundo_apellido'] = strtoupper($validatedData['segundo_apellido'] ?? '');
        $validatedData['password'] = bcrypt($validatedData['numero_documento']);

        // Mapear numero_documento a documento para la base de datos
        $validatedData['documento'] = $validatedData['numero_documento'];
        unset($validatedData['numero_documento']);

        $user = User::create($validatedData);

        // Autenticar al usuario automáticamente
        Auth::login($user);

        return redirect('/')->with('success', '¡Registro Exitoso! Bienvenido a la plataforma.');
    }

    public function mostrarFormulario()
    {
        return view('user.registro');
    }
}
