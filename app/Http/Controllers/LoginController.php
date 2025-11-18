<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\User;
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
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            $remember = $request->boolean('remember');
            $user = User::where('email', $credentials['email'])->first();
            $response = null;

            // Validar usuario antes de intentar autenticar
            if ($user && !$this->validarUsuarioAntesLogin($user)) {
                $response = $this->getRespuestaValidacionUsuario($user);
            } elseif (!Auth::attempt($credentials, $remember)) {
                // Intentar autenticar
                $response = back()
                    ->withInput()
                    ->withErrors(['error' => 'Correo o contraseña inválidos']);
            } else {
                // Procesar autenticación exitosa
                $request->session()->regenerate();
                $response = $this->procesarLoginExitoso($request, Auth::user());
            }

            return $response;
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Error al conectar con la base de datos. '
                        . 'Por favor, inténtelo de nuevo más tarde.',
                ]);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Error al iniciar sesión. Por favor, inténtelo de nuevo más tarde.',
                ]);
        }
    }

    /**
     * Valida si el usuario puede iniciar sesión antes de intentar autenticar
     */
    private function validarUsuarioAntesLogin(?User $user): bool
    {
        if (!$user) {
            return true; // Permitir intento de autenticación
        }

        return $user->hasVerifiedEmail() && $user->status == 1;
    }

    /**
     * Obtiene la respuesta de validación cuando el usuario no puede iniciar sesión
     */
    private function getRespuestaValidacionUsuario(User $user)
    {
        $response = null;

        if (!$user->hasVerifiedEmail()) {
            // Enviar correo de verificación automáticamente
            try {
                \Illuminate\Support\Facades\Log::info('Enviando correo de verificación automático al intentar login', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                
                $user->sendEmailVerificationNotification();
                
                \Illuminate\Support\Facades\Log::info('Correo de verificación enviado automáticamente', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                
                $response = back()
                    ->withInput()
                    ->with(
                        'error',
                        'Debes verificar tu correo electrónico antes de iniciar sesión. '
                        . 'Se ha enviado un nuevo enlace de verificación a tu correo. '
                        . 'Revisa tu bandeja de entrada y spam.'
                    );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error al enviar correo de verificación automático', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
                
                $response = back()
                    ->withInput()
                    ->with(
                        'error',
                        'Debes verificar tu correo electrónico antes de iniciar sesión. '
                        . 'No se pudo enviar el correo automáticamente. '
                        . 'Solicita un nuevo enlace de verificación usando el formulario a continuación.'
                    );
            }
        } elseif ($user->status == 0) {
            $response = back()
                ->withInput()
                ->withErrors(['email' => 'Tu cuenta se encuentra inactiva. Contacta al administrador.']);
        } else {
            $response = back()
                ->withInput()
                ->withErrors(['error' => 'No se puede iniciar sesión']);
        }

        return $response;
    }

    /**
     * Procesa el login exitoso verificando estado y redirigiendo
     */
    private function procesarLoginExitoso(Request $request, User $user)
    {
        if ($user->status == 0) {
            Auth::logout();
            return back()
                ->withInput()
                ->withErrors(['error' => 'La cuenta se encuentra inactiva']);
        }

        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            
            // Enviar correo de verificación automáticamente
            try {
                \Illuminate\Support\Facades\Log::info(
                    'Enviando correo de verificación automático después de login exitoso',
                    [
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ]
                );
                
                $user->sendEmailVerificationNotification();
                
                \Illuminate\Support\Facades\Log::info(
                    'Correo de verificación enviado automáticamente después de login',
                    [
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ]
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error(
                    'Error al enviar correo de verificación automático después de login',
                    [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'error' => $e->getMessage(),
                    ]
                );
            }
            
            return redirect()
                ->route('verification.notice')
                ->with(
                    'warning',
                    'Por favor, verifica tu correo electrónico antes de iniciar sesión. '
                    . 'Se ha enviado un enlace de verificación a tu correo. Revisa tu bandeja de entrada y spam.'
                );
        }

        return $this->redirigirDespuesLogin($request, $user);
    }

    /**
     * Redirige al usuario después de un login exitoso
     */
    private function redirigirDespuesLogin(Request $request, User $user)
    {
        $redirect = $request->input('redirect') ?: $request->query('redirect');

        if ($redirect) {
            return redirect('/programas-complementarios/' . $redirect . '/inscripcion')
                ->with('user_data', $this->getUserDataForForm($user))
                ->with(
                    'success',
                    '¡Sesión Iniciada! Complete su información para finalizar la inscripción.'
                );
        }

        return redirect('/home')->with('success', '¡Sesión Iniciada!');
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
     * Reenvía el correo de verificación sin autenticación (desde el login)
     */
    public function reenviarCorreoVerificacion(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No existe una cuenta registrada con este correo electrónico.',
        ]);

        $user = User::where('email', $validated['email'])->first();
        $response = null;

        if (!$user) {
            $response = back()
                ->withInput()
                ->withErrors(['email' => 'No existe una cuenta registrada con este correo electrónico.']);
        } elseif ($user->hasVerifiedEmail()) {
            $response = back()
                ->withInput()
                ->with('info', 'Tu correo electrónico ya está verificado. Puedes iniciar sesión.');
        } else {
            try {
                \Illuminate\Support\Facades\Log::info('Intentando enviar correo de verificación', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                
                $user->sendEmailVerificationNotification();
                
                \Illuminate\Support\Facades\Log::info('Correo de verificación enviado exitosamente', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                
                $response = back()
                    ->withInput()
                    ->with(
                        'success',
                        'Se ha enviado un nuevo enlace de verificación a tu correo electrónico. '
                        . 'Por favor, revisa tu bandeja de entrada y spam.'
                    );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error al enviar correo de verificación', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'class' => get_class($e),
                ]);
                
                $errorMessage = 'No se pudo enviar el correo de verificación.';
                if (str_contains($e->getMessage(), 'Connection') || str_contains($e->getMessage(), 'SMTP')) {
                    $errorMessage .= ' Verifica la configuración SMTP.';
                }
                $errorMessage .= ' Error: ' . $e->getMessage();
                
                $response = back()
                    ->withInput()
                    ->withErrors(['error' => $errorMessage]);
            }
        }

        return $response;
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
