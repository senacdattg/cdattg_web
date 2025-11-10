<?php

namespace App\Http\Controllers\Complementarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplementarioOfertado;
use App\Models\Persona;
use App\Models\AspiranteComplementario;
use App\Models\User;
use App\Models\Tema;
use App\Models\Pais;
use App\Models\Departamento;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use App\Services\ComplementarioService;
use Carbon\Carbon;

class InscripcionComplementarioController extends Controller
{
    protected $complementarioService;

    public function __construct(ComplementarioService $complementarioService)
    {
        $this->complementarioService = $complementarioService;
    }

    /**
     * Mostrar formulario general de inscripción a eventos del SENA
     */
    public function inscripcionGeneral()
    {
        // Obtener temas de caracterización con sus parámetros
        $temasCaracterizacion = Tema::where('name', 'PERSONA CARACTERIZACION')
            ->with(['parametros' => function($query) {
                $query->where('parametros_temas.status', 1);
            }])
            ->where('status', 1)
            ->get();

        $paises = Pais::all();
        $departamentos = Departamento::all();

        // Obtener tipos de documento y géneros dinámicamente
        $tiposDocumento = $this->complementarioService->getTiposDocumento();
        $generos = $this->complementarioService->getGeneros();

        return view('complementarios.inscripcion_general', compact('temasCaracterizacion', 'paises', 'departamentos', 'tiposDocumento', 'generos'));
    }

    /**
     * Procesar la inscripción general (solo datos de persona y caracterización)
     */
    public function procesarInscripcionGeneral(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
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
            'observaciones' => 'nullable|string',
            'parametro_id' => 'nullable|exists:parametros,id',
        ]);

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
            'municipio_id', 'direccion', 'parametro_id'
        ]));

        return redirect()->route('inscripcion.general')->with('success', '¡Registro exitoso! Sus datos han sido guardados correctamente.');
    }

    /**
     * Mostrar formulario de inscripción a programa específico
     */
    public function formularioInscripcion($id)
    {
        // Permitir acceso a usuarios no autenticados - el formulario crea la cuenta automáticamente
        $programa = ComplementarioOfertado::with(['modalidad.parametro', 'jornada'])->findOrFail($id);

        // Obtener temas de caracterización con sus parámetros
        $temasCaracterizacion = Tema::where('name', 'PERSONA CARACTERIZACION')
            ->with(['parametros' => function($query) {
                $query->where('parametros_temas.status', 1);
            }])
            ->where('status', 1)
            ->get();

        $paises = Pais::all();
        $departamentos = Departamento::all();

        // Obtener tipos de documento y géneros dinámicamente
        $tiposDocumento = $this->complementarioService->getTiposDocumento();
        $generos = $this->complementarioService->getGeneros();

        // Inicializar datos del usuario
        $userData = [];

        // Si el usuario está autenticado, cargar sus datos existentes
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->persona) {
                $persona = $user->persona;
                $userData = [
                    'tipo_documento' => $persona->tipo_documento,
                    'numero_documento' => $persona->numero_documento,
                    'primer_nombre' => $persona->primer_nombre,
                    'segundo_nombre' => $persona->segundo_nombre,
                    'primer_apellido' => $persona->primer_apellido,
                    'segundo_apellido' => $persona->segundo_apellido,
                    'email' => $persona->email,
                    'fecha_nacimiento' => $persona->fecha_nacimiento,
                    'genero' => $persona->genero,
                    'telefono' => $persona->telefono,
                    'celular' => $persona->celular,
                    'pais_id' => $persona->pais_id,
                    'departamento_id' => $persona->departamento_id,
                    'municipio_id' => $persona->municipio_id,
                    'direccion' => $persona->direccion,
                ];
            }
        }

        return view('complementarios.formulario_inscripcion', compact('programa', 'temasCaracterizacion', 'paises', 'departamentos', 'tiposDocumento', 'generos', 'userData'));
    }

    /**
     * Procesar la inscripción del aspirante
     */
    public function procesarInscripcion(Request $request, $id)
    {
        // Si el usuario está autenticado, verificar si ya está inscrito en este programa
        if (Auth::check()) {
            $existingInscription = AspiranteComplementario::where('persona_id', Auth::user()->persona_id)
                ->where('complementario_id', $id)
                ->first();

            if ($existingInscription) {
                return redirect()->back()->with('error', 'Ya estás inscrito en este programa complementario.');
            }
        }

        // Validar los datos del formulario
        $request->validate([
            'tipo_documento' => 'required|integer',
            'numero_documento' => 'required|string|max:191',
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
            'email' => 'required|email|max:191',
            'pais_id' => 'required|exists:pais,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'municipio_id' => 'required|exists:municipios,id',
            'direccion' => 'required|string|max:191',
            'observaciones' => 'nullable|string',
            'parametro_id' => 'nullable|exists:parametros,id',
        ]);

        // Verificar si ya existe una persona con el mismo documento o email
        $personaExistente = Persona::where('numero_documento', $request->numero_documento)
            ->orWhere('email', $request->email)
            ->first();

        if ($personaExistente) {
            // Si ya existe, usar esa persona
            $persona = $personaExistente;

            // Actualizar datos si es necesario
            $persona->update($request->only([
                'tipo_documento', 'numero_documento', 'primer_nombre', 'segundo_nombre',
                'primer_apellido', 'segundo_apellido', 'fecha_nacimiento', 'genero',
                'telefono', 'celular', 'email', 'pais_id', 'departamento_id',
                'municipio_id', 'direccion', 'parametro_id'
            ]));
        } else {
            // Crear nueva persona
            $persona = Persona::create($request->only([
                'tipo_documento', 'numero_documento', 'primer_nombre', 'segundo_nombre',
                'primer_apellido', 'segundo_apellido', 'fecha_nacimiento', 'genero',
                'telefono', 'celular', 'email', 'pais_id', 'departamento_id',
                'municipio_id', 'direccion', 'parametro_id', 'status'
            ]) + ['user_create_id' => 1, 'user_edit_id' => 1]);
        }

        // Verificar si ya existe una inscripción para esta persona en este programa
        $existingInscription = AspiranteComplementario::where('persona_id', $persona->id)
            ->where('complementario_id', $id)
            ->first();

        if ($existingInscription) {
            return redirect()->back()->with('error', 'Ya estás inscrito en este programa complementario.');
        }

        // Crear el registro del aspirante
        $aspirante = AspiranteComplementario::create([
            'persona_id' => $persona->id,
            'complementario_id' => $id,
            'observaciones' => $request->observaciones,
            'estado' => 1, // Estado "En proceso"
        ]);

        // Verificar si ya existe un usuario con este email
        $existingUser = User::where('email', $request->email)->first();

        if (!$existingUser) {
            // Crear cuenta de usuario automáticamente
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->numero_documento), // Usar documento como contraseña
                'status' => 1,
                'persona_id' => $persona->id,
            ]);

            // Asignar rol de aspirante
            $user->assignRole('ASPIRANTE');
        } else {
            // Si el usuario ya existe, verificar si tiene rol VISITANTE y cambiarlo a ASPIRANTE
            if ($existingUser->hasRole('VISITANTE')) {
                $existingUser->removeRole('VISITANTE');
                $existingUser->assignRole('ASPIRANTE');
            }
        }

        // Redirigir a la segunda fase (subida de documentos)
        return redirect()->route('programas-complementarios.documentos', ['id' => $id, 'aspirante_id' => $aspirante->id])
            ->with('success', 'Datos personales registrados correctamente. Ahora debe subir su documento de identidad.');
    }
}