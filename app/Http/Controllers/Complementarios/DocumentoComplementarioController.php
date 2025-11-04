<?php

namespace App\Http\Controllers\Complementarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplementarioOfertado;
use App\Models\AspiranteComplementario;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentoComplementarioController extends Controller
{
    /**
     * Mostrar formulario para subir documentos
     */
    public function formularioDocumentos(Request $request, $id)
    {
        $programa = ComplementarioOfertado::findOrFail($id);

        // Obtener aspirante_id de la URL
        $aspirante_id = $request->query('aspirante_id');

        return view('complementarios.formulario_documentos', compact('programa', 'aspirante_id'));
    }

    /**
     * Procesar la subida de documentos
     */
    public function subirDocumento(Request $request, $id)
    {
        Log::info('=== subirDocumento method reached ===', [
            'request_data' => $request->all(),
            'files' => $request->files->all(),
            'aspirante_id' => $request->aspirante_id,
            'has_file' => $request->hasFile('documento_identidad'),
            'file_info' => $request->hasFile('documento_identidad') ? [
                'name' => $request->file('documento_identidad')->getClientOriginalName(),
                'size' => $request->file('documento_identidad')->getSize(),
                'mime' => $request->file('documento_identidad')->getMimeType()
            ] : null
        ]);

        // Validar el archivo
        Log::info('Antes de validación - campos recibidos:', $request->all());
        $request->validate([
            'documento_identidad' => 'required|file|mimes:pdf|max:5120', // 5MB máximo
            'aspirante_id' => 'required|exists:aspirantes_complementarios,id',
            'acepto_privacidad' => 'required',
        ]);
        Log::info('Después de validación - validación pasó');

        try {
            // Obtener el aspirante
            Log::info('Buscando aspirante con ID: ' . $request->aspirante_id);
            $aspirante = AspiranteComplementario::findOrFail($request->aspirante_id);

            Log::info('Aspirante found', [
                'aspirante_id' => $aspirante->id,
                'persona_id' => $aspirante->persona_id,
                'numero_documento' => $aspirante->persona->numero_documento
            ]);

            // Procesar el archivo y subirlo a Google Drive
            if ($request->hasFile('documento_identidad')) {
                $file = $request->file('documento_identidad');

                // Crear nombre de archivo con formato: tipo_documento_NumeroDocumento_PrimerNombre_PrimerApellido_timestamp.pdf
                $tipoDocumento = $aspirante->persona->tipoDocumento->name ?? 'DOC';
                $numeroDocumento = $aspirante->persona->numero_documento;
                $primerNombre = $aspirante->persona->primer_nombre;
                $primerApellido = $aspirante->persona->primer_apellido;
                $timestamp = now()->format('d-m-y-H-i-s');

                $fileName = "{$tipoDocumento}_{$numeroDocumento}_{$primerNombre}_{$primerApellido}_{$timestamp}.{$file->getClientOriginalExtension()}";

                Log::info('Attempting to upload file to Google Drive', [
                    'file_name' => $fileName,
                    'file_size' => $file->getSize(),
                    'disk_config' => config('filesystems.disks.google'),
                    'google_credentials_path' => storage_path('app/google-credentials.json'),
                    'credentials_exist' => file_exists(storage_path('app/google-credentials.json'))
                ]);

                // Verificar configuración de Google Drive
                Log::info('Google Drive config check', [
                    'client_id' => env('GOOGLE_DRIVE_CLIENT_ID') ? 'SET' : 'NOT SET',
                    'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET') ? 'SET' : 'NOT SET',
                    'refresh_token' => env('GOOGLE_DRIVE_REFRESH_TOKEN') ? 'SET' : 'NOT SET',
                    'folder_id' => env('GOOGLE_DRIVE_FOLDER_ID') ? 'SET' : 'NOT SET'
                ]);

                // Subir a Google Drive
                $path = Storage::disk('google')->putFileAs('documentos_aspirantes', $file, $fileName);

                Log::info('File uploaded successfully', ['path' => $path]);

                // Actualizar el registro del aspirante con la información del documento
                $aspirante->update([
                    'documento_identidad_path' => $path,
                    'documento_identidad_nombre' => $fileName,
                    'estado' => 2, // Estado "Documento subido"
                ]);
            }

            return redirect()->route('login.index')
                ->with('success', 'Documento subido exitosamente. Su cuenta de usuario ha sido creada. Puede iniciar sesión con su correo electrónico y número de documento como contraseña.');

        } catch (\Exception $e) {
            Log::error('Error al subir documento: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error al subir el documento. Por favor intente nuevamente.');
        }
    }

    /**
     * Procesar documentos (método legacy)
     */
    public function procesarDocumentos()
    {
        return view('complementarios.procesamiento_documentos');
    }
}
