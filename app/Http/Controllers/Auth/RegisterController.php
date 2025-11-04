<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Persona;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    public function create(Request $request)
    {
        Log::info('RegisterController - Método create llamado', [
            'all_data' => $request->all(),
            'method' => $request->method(),
            'has_csrf' => $request->has('_token'),
            'csrf_token' => $request->input('_token')
        ]);

        // Validación completa
        $validatedData = $request->validate([
            'tipo_documento' => 'required|integer',
            'numero_documento' => 'required|string|max:191|unique:personas',
            'primer_nombre' => 'required|string|max:191',
            'segundo_nombre' => 'nullable|string|max:191',
            'primer_apellido' => 'required|string|max:191',
            'segundo_apellido' => 'nullable|string|max:191',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|integer',
            'telefono' => 'nullable|string|max:191',
            'celular' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:personas',
            'pais_id' => 'required|exists:pais,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'municipio_id' => 'required|exists:municipios,id',
            'direccion' => 'required|string|max:191',
        ]);

        Log::info('RegisterController - Validación pasada', ['validated_data' => $validatedData]);

        // Convertir a mayúsculas los campos
        $validatedData['primer_nombre'] = strtoupper($validatedData['primer_nombre']);
        $validatedData['segundo_nombre'] = strtoupper($validatedData['segundo_nombre'] ?? '');
        $validatedData['primer_apellido'] = strtoupper($validatedData['primer_apellido']);
        $validatedData['segundo_apellido'] = strtoupper($validatedData['segundo_apellido'] ?? '');
        $validatedData['password'] = bcrypt($validatedData['numero_documento']);

        // Mapear numero_documento a documento para la base de datos
        $validatedData['documento'] = $validatedData['numero_documento'];
        unset($validatedData['numero_documento']);

        Log::info('RegisterController - Datos procesados', ['processed_data' => $validatedData]);

        // Verificar si ya existe una persona con el mismo documento o email
        $personaExistente = Persona::where('numero_documento', $request->numero_documento)
            ->orWhere('email', $request->email)
            ->first();

        if ($personaExistente) {
            return back()->withInput()->with('error', 'Ya existe una persona registrada con este número de documento o correo electrónico.');
        }

        // Crear nueva persona
        $persona = Persona::create($request->only([
            'tipo_documento', 'numero_documento', 'primer_nombre', 'segundo_nombre',
            'primer_apellido', 'segundo_apellido', 'fecha_nacimiento', 'genero',
            'telefono', 'celular', 'email', 'pais_id', 'departamento_id',
            'municipio_id', 'direccion'
        ]) + ['user_create_id' => 1, 'user_edit_id' => 1]);

        // Crear cuenta de usuario automáticamente
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->numero_documento), // Usar documento como contraseña
            'status' => 1,
            'persona_id' => $persona->id,
        ]);

        // Asignar rol de visitante
        $user->assignRole('VISITANTE');

        // Verificar si el usuario ya tiene alguna inscripción en programas complementarios
        $tieneInscripciones = \App\Models\AspiranteComplementario::where('persona_id', $persona->id)->exists();

        // Si ya tiene inscripciones, cambiar el rol a ASPIRANTE
        if ($tieneInscripciones) {
            $user->removeRole('VISITANTE');
            $user->assignRole('ASPIRANTE');
        }

        // Autenticar al usuario automáticamente
        Auth::login($user);

        return redirect('/')->with('success', '¡Registro Exitoso! Bienvenido a la plataforma.');
    }

    public function mostrarFormulario()
    {
        // Obtener tipos de documento y géneros dinámicamente
        $tiposDocumento = $this->getTiposDocumento();
        $generos = $this->getGeneros();

        return view('user.registro', compact('tiposDocumento', 'generos'));
    }

    /**
     * Método auxiliar para obtener tipos de documento dinámicamente desde el tema-parametro
     */
    private function getTiposDocumento()
    {
        // Buscar el tema "TIPO DE DOCUMENTO"
        $temaTipoDocumento = \App\Models\Tema::where('name', 'TIPO DE DOCUMENTO')->first();

        if (!$temaTipoDocumento) {
            // Fallback: devolver valores hardcodeados si no se encuentra el tema
            return collect([
                ['id' => 3, 'name' => 'CEDULA DE CIUDADANIA'],
                ['id' => 4, 'name' => 'CEDULA DE EXTRANJERIA'],
                ['id' => 5, 'name' => 'PASAPORTE'],
                ['id' => 6, 'name' => 'TARJETA DE IDENTIDAD'],
                ['id' => 7, 'name' => 'REGISTRO CIVIL'],
                ['id' => 8, 'name' => 'SIN IDENTIFICACION'],
            ]);
        }

        // Obtener parámetros activos del tema
        return $temaTipoDocumento->parametros()
            ->where('parametros_temas.status', 1)
            ->orderBy('parametros.name')
            ->get(['parametros.id', 'parametros.name']);
    }

    /**
     * Método auxiliar para obtener géneros dinámicamente desde el tema-parametro
     */
    private function getGeneros()
    {
        // Buscar el tema "GENERO"
        $temaGenero = \App\Models\Tema::where('name', 'GENERO')->first();

        if (!$temaGenero) {
            // Fallback: devolver valores hardcodeados si no se encuentra el tema
            return collect([
                ['id' => 9, 'name' => 'MASCULINO'],
                ['id' => 10, 'name' => 'FEMENINO'],
                ['id' => 11, 'name' => 'NO DEFINE'],
            ]);
        }

        // Obtener parámetros activos del tema
        return $temaGenero->parametros()
            ->where('parametros_temas.status', 1)
            ->orderBy('parametros.name')
            ->get(['parametros.id', 'parametros.name']);
    }
}
