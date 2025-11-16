<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
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

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Hemos enviado un enlace de restablecimiento a su correo.');
        }

        return back()->withErrors([
            'email' => 'No encontramos un usuario con ese correo.',
        ]);
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
