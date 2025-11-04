<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GoogleDriveController extends Controller
{
    /**
     * Inicia el flujo OAuth para obtener un refresh_token con los scopes correctos.
     * Redirige a Google para que el usuario otorgue permisos.
     */
    public function connect(Request $request)
    {
        $client = new \Google\Client();
        $client->setClientId(config('filesystems.disks.google.clientId'));
        $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        if (class_exists(\Google\Service\Drive::class)) {
            $client->setScopes([\Google\Service\Drive::DRIVE_FILE, \Google\Service\Drive::DRIVE]);
        } else {
            $client->setScopes([
                'https://www.googleapis.com/auth/drive.file',
                'https://www.googleapis.com/auth/drive',
            ]);
        }

        // IMPORTANTE: Debe coincidir EXACTAMENTE con alguna redirect URI permitida en Google Cloud.
        // Construimos dinámicamente según el host actual (localhost o 127.0.0.1)
        $redirectUri = $request->getSchemeAndHttpHost() . '/google-drive-callback';
        $client->setRedirectUri($redirectUri);

        $authUrl = $client->createAuthUrl();
        return redirect()->away($authUrl);
    }

    /**
     * Callback de Google. Intercambia el "code" por tokens y muestra el refresh_token.
     */
    public function callback(Request $request)
    {
        $code = $request->query('code');
        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Falta el parámetro "code" en la URL'
            ], 400);
        }

        $client = new \Google\Client();
        $client->setClientId(config('filesystems.disks.google.clientId'));
        $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        if (class_exists(\Google\Service\Drive::class)) {
            $client->setScopes([\Google\Service\Drive::DRIVE_FILE, \Google\Service\Drive::DRIVE]);
        } else {
            $client->setScopes([
                'https://www.googleapis.com/auth/drive.file',
                'https://www.googleapis.com/auth/drive',
            ]);
        }

        $redirectUri = $request->getSchemeAndHttpHost() . '/google-drive-callback';
        $client->setRedirectUri($redirectUri);

        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            Log::error('Google OAuth error on callback', ['error' => $token]);
            return response()->json(['success' => false, 'error' => $token], 500);
        }

        $refreshToken = $client->getRefreshToken();
        if (!$refreshToken && isset($token['refresh_token'])) {
            $refreshToken = $token['refresh_token'];
        }

        // Guardar tokens en storage para referencia
        try {
            $toStore = [
                'fetched_at' => now()->toDateTimeString(),
                'access_token' => $token['access_token'] ?? null,
                'expires_in' => $token['expires_in'] ?? null,
                'scope' => $token['scope'] ?? null,
                'refresh_token' => $refreshToken,
                'token_type' => $token['token_type'] ?? null,
            ];
            Storage::disk('local')->put('google-token.json', json_encode($toStore, JSON_PRETTY_PRINT));
        } catch (\Throwable $e) {
            Log::warning('No se pudo escribir storage/app/google-token.json', ['exception' => $e->getMessage()]);
        }

        $message = "Copia y pega el siguiente valor en tu .env como GOOGLE_DRIVE_REFRESH_TOKEN y luego ejecuta: php artisan config:clear";
        $html = "<html><body style='font-family:system-ui; padding:20px;'>
        <h2>Token obtenido correctamente</h2>
        <p>{$message}</p>
        <pre style='background:#f5f5f5; padding:10px; border-radius:6px;'>GOOGLE_DRIVE_REFRESH_TOKEN=\"{$refreshToken}\"</pre>
        <p>Archivo de depuración guardado en storage/app/google-token.json</p>
        </body></html>";

        return response($html);
    }

    /**
     * Prueba rápida de conectividad a Drive (intenta crear un archivo pequeño).
     */
    public function test(Request $request)
    {
        try {
            $path = 'documentos_aspirantes/_connectivity_check_' . time() . '.txt';
            Storage::disk('google')->put($path, 'Drive connectivity OK at ' . now()->toDateTimeString());
            return response()->json(['success' => true, 'path' => $path]);
        } catch (\Throwable $e) {
            Log::error('Drive test upload failed', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
