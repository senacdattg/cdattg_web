<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\URL;

class VerificationController extends Controller
{
    /**
     * Muestra la página de verificación de email
     */
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        return view('user.verify-email');
    }

    /**
     * Verifica el email del usuario (puede ejecutarse sin autenticación)
     * El middleware 'signed' ya validó la firma de la URL, solo validamos el hash
     */
    public function verify(Request $request, $id, $hash): RedirectResponse
    {
        $user = User::find($id);

        if (!$user) {
            \Illuminate\Support\Facades\Log::warning('Intento de verificación con ID inválido', [
                'id' => $id,
                'hash' => $hash,
            ]);
            
            return redirect()->route('login.index')
                ->with('error', 'El enlace de verificación no es válido.');
        }

        // Validar que el hash coincida con el email del usuario
        // El middleware 'signed' ya validó que la URL esté firmada correctamente
        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            \Illuminate\Support\Facades\Log::warning('Hash de verificación no coincide', [
                'user_id' => $user->id,
                'email' => $user->email,
                'hash_esperado' => sha1($user->getEmailForVerification()),
                'hash_recibido' => $hash,
            ]);
            
            return redirect()->route('login.index')
                ->with('error', 'El enlace de verificación no es válido o ha expirado.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login.index')
                ->with('success', 'Tu correo electrónico ya está verificado. Puedes iniciar sesión.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            
            \Illuminate\Support\Facades\Log::info('Correo verificado exitosamente', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        return redirect()->route('login.index')
            ->with('success', '¡Correo electrónico verificado exitosamente! Ya puedes iniciar sesión.');
    }

    /**
     * Reenvía el email de verificación
     */
    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home')
                ->with('info', 'Tu correo electrónico ya está verificado.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()
            ->with('success', 'Se ha enviado un nuevo enlace de verificación a tu correo electrónico.');
    }
}

