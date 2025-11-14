<?php

namespace App\Http\Controllers;

use App\Configuration\UploadLimits;
use App\Http\Requests\PersonaImportRequest;
use App\Models\PersonaImport;
use App\Services\PersonaImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PersonaImportController extends Controller
{
    public function __construct(private readonly PersonaImportService $importService)
    {
        $this->middleware('auth');
        $this->middleware('can:CREAR PERSONA');

        // Validar Content-Length para importaciones (8MB límite)
        $this->middleware('validate.content.length:' . UploadLimits::IMPORT_CONTENT_LENGTH_BYTES)->only('store');
    }

    public function create(): View
    {
        $importaciones = PersonaImport::query()
            ->with(['user.persona'])
            ->withCount('issues')
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        return view('personas.import', [
            'importaciones' => $importaciones,
        ]);
    }

    public function store(PersonaImportRequest $request): JsonResponse
    {
        try {
            $import = $this->importService->iniciarImportacion(
                $request->file('archivo_excel'),
                $request->user()->id
            );

            return response()->json([
                'message' => 'Importación encolada correctamente.',
                'import_id' => $import->id,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al iniciar la importación: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function status(PersonaImport $personaImport): JsonResponse
    {
        $personaImport->loadCount('issues');

        $issues = $personaImport->issues()
            ->latest()
            ->limit(50)
            ->get()
            ->map(function ($issue) {
                return [
                    'row_number' => $issue->row_number,
                    'issue_type' => $this->traducirIssueType($issue->issue_type, $issue->error_message),
                    'numero_documento' => $issue->numero_documento,
                    'email' => $issue->email,
                    'celular' => $issue->celular,
                ];
            });

        return response()->json([
            'import' => [
                'id' => $personaImport->id,
                'status' => $personaImport->status,
                'original_name' => $personaImport->original_name,
                'processed_rows' => $personaImport->processed_rows,
                'total_rows' => $personaImport->total_rows,
                'success_count' => $personaImport->success_count,
                'duplicate_count' => $personaImport->duplicate_count,
                'missing_contact_count' => $personaImport->missing_contact_count,
                'issues_count' => $personaImport->issues_count,
                'error_message' => $personaImport->error_message,
                'created_at' => $personaImport->created_at?->toDateTimeString(),
                'updated_at' => $personaImport->updated_at?->toDateTimeString(),
            ],
            'issues' => $issues,
        ]);
    }

    public function destroy(Request $request, PersonaImport $personaImport): RedirectResponse|JsonResponse
    {
        DB::transaction(function () use ($personaImport) {
            $personaImport->issues()->delete();
            $personaImport->contactAlerts()->delete();

            if ($personaImport->path && $personaImport->disk) {
                Storage::disk($personaImport->disk)->delete($personaImport->path);
            }

            DB::table('jobs')
                ->where('queue', 'persona-import')
                ->where('payload', 'like', '%"importId";i:' . $personaImport->id . '%')
                ->delete();

            DB::table('failed_jobs')
                ->where('queue', 'persona-import')
                ->where('payload', 'like', '%"importId";i:' . $personaImport->id . '%')
                ->delete();

            $personaImport->delete();
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'La importación fue detenida y eliminada correctamente.',
            ]);
        }

        return redirect()
            ->route('personas.import.create')
            ->with('success', 'La importación y su evidencia fueron eliminadas correctamente.');
    }

    private function traducirIssueType(?string $issueType, ?string $errorMessage = null): string
    {
        $labels = [
            'missing_document' => 'Documento de identidad ausente',
            'missing_required_fields' => 'Faltan nombres o apellidos obligatorios',
            'duplicate_document_in_file' => 'Documento repetido en el archivo',
            'duplicate_document_existing' => 'Documento ya registrado en el sistema',
            'duplicate_email_in_file' => 'Correo repetido en el archivo',
            'duplicate_email_existing' => 'Correo ya registrado en el sistema',
            'duplicate_celular_in_file' => 'Celular repetido en el archivo',
            'duplicate_celular_existing' => 'Celular ya registrado en el sistema',
            'duplicate_telefono_in_file' => 'Teléfono repetido en el archivo',
            'duplicate_telefono_existing' => 'Teléfono ya registrado en el sistema',
        ];

        if ($issueType === null) {
            return 'Incidencia sin clasificar';
        }

        if ($issueType === 'persist_error') {
            $detalle = $errorMessage ? Str::limit($errorMessage, 120) : 'detalle no disponible';
            return "Error al guardar el registro ({$detalle})";
        }

        return $labels[$issueType] ?? Str::headline(str_replace('_', ' ', $issueType));
    }
}
