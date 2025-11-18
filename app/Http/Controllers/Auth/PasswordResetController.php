<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function request()
    {
        return view('auth.passwords.email');
    }

    public function email(Request $request)
    {
        $request->validate(
            [
                'email' => ['required', 'email'],
            ],
            [
                'email.required' => 'El correo es obligatorio.',
                'email.email' => 'Ingrese un correo válido.',
            ]
        );

        $errorMessage = null;
        $successMessage = null;

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                $successMessage = 'Hemos enviado un enlace de restablecimiento a su correo.';
            } else {
                $errorMessage = 'No encontramos un usuario con ese correo.';
            }
        } catch (\Exception $e) {
            $errorDetails = [
                'message' => $e->getMessage(),
                'email' => $request->email,
                'mail_config' => [
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                    'username' => config('mail.mailers.smtp.username'),
                ],
            ];

            Log::error('Error al enviar enlace de restablecimiento', $errorDetails);

            // Detectar diferentes tipos de errores de mail
            $errorMessage = 'Ocurrió un error al procesar su solicitud. Por favor, inténtelo de nuevo.';

            $errorMsg = $e->getMessage();

            if (str_contains($errorMsg, 'Connection could not be established') ||
                str_contains($errorMsg, 'mailpit') ||
                str_contains($errorMsg, 'getaddrinfo')) {
                $errorMessage = 'Error de conexión con el servidor de correo. '
                    . 'Verifique la configuración del servidor SMTP.';
            } elseif (str_contains($errorMsg, 'Authentication') ||
                      str_contains($errorMsg, '535') ||
                      str_contains($errorMsg, 'Invalid login') ||
                      str_contains($errorMsg, 'authentication failed')) {
                $errorMessage = 'Error de autenticación con el servidor de correo. '
                    . 'Verifique que el usuario y contraseña sean correctos en el archivo .env.';
            } elseif (str_contains($errorMsg, 'SSL') ||
                      str_contains($errorMsg, 'TLS') ||
                      str_contains($errorMsg, 'certificate')) {
                $errorMessage = 'Error de certificado SSL. Verifique la configuración de encriptación.';
            }
        }

        if ($successMessage) {
            return back()->with('success', $successMessage);
        }

        return back()->withErrors(['email' => $errorMessage])->withInput();
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('vendor.adminlte.auth.passwords.reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate(
            [
                'token' => ['required'],
                'email' => ['required', 'email'],
                'password' => ['required', 'confirmed', 'min:8'],
            ],
            [
                'email.required' => 'El correo es obligatorio.',
                'email.email' => 'Ingrese un correo válido.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            ]
        );

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login.index')->with('success', 'Contraseña restablecida correctamente.');
        }

        return back()->withErrors([
            'email' => 'No se pudo restablecer la contraseña. Verifique el enlace y los datos.',
        ]);
    }
}
