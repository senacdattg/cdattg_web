<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonaImportRequest;
use App\Models\PersonaImport;
use App\Services\PersonaImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PersonaImportController extends Controller
{
    public function __construct(private readonly PersonaImportService $importService)
    {
        $this->middleware('auth');
        $this->middleware('can:CREAR PERSONA');
    }

    public function create(): View
    {
        $importaciones = PersonaImport::query()
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
        $import = $this->importService->iniciarImportacion(
            $request->file('archivo_excel'),
            $request->user()->id
        );

        return response()->json([
            'message' => 'Importación encolada correctamente.',
            'import_id' => $import->id,
        ], 201);
    }

    public function status(PersonaImport $personaImport): JsonResponse
    {
        $personaImport->loadCount('issues');

        $issues = $personaImport->issues()
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn($issue) => [
                'row_number' => $issue->row_number,
                'issue_type' => $issue->issue_type,
                'numero_documento' => $issue->numero_documento,
                'email' => $issue->email,
                'celular' => $issue->celular,
            ]);

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
}
