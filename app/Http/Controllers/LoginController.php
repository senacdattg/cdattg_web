<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class LoginController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function iniciarSesion(Request $request)
    {
        try {
            // Validamos la solicitud
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            $response = null;
            $remember = $request->boolean('remember');
            if (Auth::attempt($credentials, $remember)) {
                // Regenerar la sesión para proteger contra fixation
                $request->session()->regenerate();
                $user = Auth::user();

                // Verificar si la cuenta está inactiva
                if ($user->status == 0) {
                    Auth::logout();
                    $response = back()
                        ->withInput()
                        ->withErrors(['error' => 'La cuenta se encuentra inactiva']);
                } else {
                    // Verificar si hay un parámetro de redirección (primero del input POST, luego del query string)
                    $redirect = $request->input('redirect') ?: $request->query('redirect');
                    if ($redirect) {
                        // Redirigir al formulario de inscripción con los datos del usuario pre-llenados
                        $response = redirect(
                            '/programas-complementarios/' . $redirect . '/inscripcion'
                        )
                            ->with('user_data', $this->getUserDataForForm($user))
                            ->with(
                                'success',
                                '¡Sesión Iniciada! Complete su información para finalizar la inscripción.'
                            );
                    } else {
                        // Redirigir al dashboard principal (/home) para usuarios normales
                        $response = redirect('/home')->with('success', '¡Sesión Iniciada!');
                    }
                }
            } else {
                $response = back()
                    ->withInput()
                    ->withErrors(['error' => 'Correo o contraseña inválidos']);
            }
            return $response;
        } catch (QueryException $e) {
            // Captura de excepciones de conexión a la base de datos
            return back()
                ->withInput()
                ->withErrors([
                    'error' =>
                    'Error al conectar con la base de datos. Por favor, inténtelo de nuevo más tarde.',
                ]);
        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Error al iniciar sesión. Por favor, inténtelo de nuevo más tarde.',
                ]);
        }
    }

    public function verificarLogin()
    {
        return Auth::check() ? redirect('/home') : redirect('/login');
    }

    public function store(Request $request)
    {
        // Este método maneja el POST a /login desde el resource route
        // Redirigir al método iniciarSesion
        return $this->iniciarSesion($request);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // $token = $user->createToken('Token Name')->plainTextToken; // Comentado para evitar error

            // Buscar la persona asociada
            $personaD = Persona::find($user->persona_id);
            if (!$personaD) {
                return response()->json(['error' => 'No se encontró la persona asociada'], 404);
            }

            // Construcción de datos de la persona
            $persona = [
                "id" => $personaD->id,
                "tipo_documento" => optional($personaD->tipoDocumento)->name,
                "numero_documento" => $personaD->numero_documento,
                "primer_nombre" => $personaD->primer_nombre,
                "segundo_nombre" => $personaD->segundo_nombre,
                "primer_apellido" => $personaD->primer_apellido,
                "segundo_apellido" => $personaD->segundo_apellido,
                "fecha_de_nacimiento" => $personaD->fecha_de_nacimiento,
                "genero" => optional($personaD->tipoGenero)->name,
                "email" => $personaD->email,
                "created_at" => $personaD->created_at,
                "updated_at" => $personaD->updated_at,
                "instructor_id" => optional($personaD->instructor)->id,
                "regional_id" => optional(optional($personaD->instructor)->regional)->id,
            ];

            return response()->json(['user' => $user, 'persona' => $persona], 200);
        }

        return response()->json(['error' => 'Credenciales incorrectas'], 401);
    }

    /**
     * Obtener datos del usuario para pre-llenar el formulario
     */
    private function getUserDataForForm($user)
    {
        $persona = $user->persona;

        if (!$persona) {
            return [];
        }

        return [
            'tipo_documento' => $persona->tipo_documento,
            'numero_documento' => $persona->numero_documento,
            'primer_nombre' => $persona->primer_nombre,
            'segundo_nombre' => $persona->segundo_nombre,
            'primer_apellido' => $persona->primer_apellido,
            'segundo_apellido' => $persona->segundo_apellido,
            'fecha_nacimiento' => $persona->fecha_nacimiento,
            'genero' => $persona->genero,
            'telefono' => $persona->telefono,
            'celular' => $persona->celular,
            'email' => $persona->email,
            'pais_id' => $persona->pais_id,
            'departamento_id' => $persona->departamento_id,
            'municipio_id' => $persona->municipio_id,
            'direccion' => $persona->direccion,
        ];
    }
}
