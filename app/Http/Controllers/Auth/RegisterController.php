<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Persona;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
            'fecha_nacimiento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $fechaNacimiento = Carbon::parse($value);
                    $edadMinima = Carbon::now()->subYears(14);
                    
                    if ($fechaNacimiento->gt($edadMinima)) {
                        $fail('Debe tener al menos 14 años para registrarse.');
                    }
                },
            ],
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
        $personaExistente = Persona::where('numero_documento', $validatedData['documento'])
            ->orWhere('email', $validatedData['email'])
            ->first();

        if ($personaExistente) {
            return back()->withInput()->with('error', 'Ya existe una persona registrada con este número de documento o correo electrónico.');
        }

        // Crear nueva persona usando los datos validados
        $persona = Persona::create([
            'tipo_documento' => $validatedData['tipo_documento'],
            'numero_documento' => $validatedData['documento'],
            'primer_nombre' => $validatedData['primer_nombre'],
            'segundo_nombre' => $validatedData['segundo_nombre'] ?? null,
            'primer_apellido' => $validatedData['primer_apellido'],
            'segundo_apellido' => $validatedData['segundo_apellido'] ?? null,
            'fecha_nacimiento' => $validatedData['fecha_nacimiento'],
            'genero' => $validatedData['genero'],
            'telefono' => $validatedData['telefono'] ?? null,
            'celular' => $validatedData['celular'],
            'email' => $validatedData['email'],
            'pais_id' => $validatedData['pais_id'],
            'departamento_id' => $validatedData['departamento_id'],
            'municipio_id' => $validatedData['municipio_id'],
            'direccion' => $validatedData['direccion'],
            'user_create_id' => 1,
            'user_edit_id' => 1
        ]);

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

        Log::info('RegisterController - Usuario autenticado, verificando redirección', [
            'user_id' => $user->id,
            'tiene_inscripciones' => $tieneInscripciones,
            'tiene_rol_aspirante' => $user->hasRole('ASPIRANTE')
        ]);

        // Verificar si el usuario tiene inscripciones pendientes en programas complementarios
        if ($tieneInscripciones) {
            // Buscar la inscripción más reciente para redirigir al formulario de documentos
            $inscripcionPendiente = \App\Models\AspiranteComplementario::where('persona_id', $persona->id)
                ->where('estado', 1) // Estado "En proceso"
                ->whereNull('documento_identidad_path') // Sin documento subido
                ->orderBy('created_at', 'desc')
                ->first();

            if ($inscripcionPendiente) {
                Log::info('RegisterController - Redirigiendo a formulario de documentos', [
                    'aspirante_id' => $inscripcionPendiente->id,
                    'programa_id' => $inscripcionPendiente->complementario_id
                ]);

                return redirect()->route('programas-complementarios.documentos', [
                    'id' => $inscripcionPendiente->complementario_id,
                    'aspirante_id' => $inscripcionPendiente->id
                ])->with('success', '¡Registro Exitoso! Complete el proceso subiendo su documento de identidad.');
            }
        }

        // Nuevo usuario registrado - redirigir a programas complementarios para que pueda inscribirse
        Log::info('RegisterController - Nuevo usuario registrado, redirigiendo a programas complementarios', [
            'user_id' => $user->id,
            'persona_id' => $persona->id
        ]);

        return redirect()->route('programas-complementarios.publicos')
            ->with('success', '¡Registro Exitoso! Ahora puede inscribirse en los programas complementarios disponibles.');
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
