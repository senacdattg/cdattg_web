<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\AspiranteComplementario;
use App\Models\Persona;
use App\Models\User;
use App\Repositories\TemaRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Throwable;

class RegisterController extends Controller
{
    public function __construct(private readonly TemaRepository $temaRepository)
    {
        $this->middleware('guest');
    }

    public function create(): View
    {
        return view('user.registro', [
            'documentos' => $this->resolveDocumentos(),
            'generos' => $this->resolveGeneros(),
            'caracterizaciones' => $this->resolveCaracterizaciones(),
            'vias' => $this->resolveVias(),
            'cardinales' => $this->resolveCardinales(),
            'letras' => $this->resolveLetras(),

            'paises' => collect(),
            'departamentos' => collect(),
            'municipios' => collect(),
        ]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($this->personaExists($validated['numero_documento'], $validated['email'])) {
            return back()
                ->withInput()
                ->with('error', 'Ya existe una persona registrada con este número de documento o correo electrónico.');
        }

        try {
            [$persona, $user] = DB::transaction(function () use ($validated) {
                return $this->createPersonaYUsuario($validated);
            });
        } catch (Throwable) {
            return back()->withInput()->with('error', 'No fue posible completar el registro. Intente nuevamente.');
        }

        $this->actualizarRolesSegunInscripcion($persona, $user);

        Auth::login($user);

        return $this->resolvePostRegistroRedirect($persona, $this->tieneInscripciones($persona));
    }

    private function personaExists(string $numeroDocumento, string $email): bool
    {
        return Persona::where('numero_documento', $numeroDocumento)
            ->orWhere('email', $email)
            ->exists()
            || User::where('email', $email)->exists();
    }

    private function createPersonaYUsuario(array $data): array
    {
        $caracterizacionIds = $this->extractCaracterizacionIds($data);

        $persona = Persona::create([
            'tipo_documento' => $data['tipo_documento'],
            'numero_documento' => $data['numero_documento'],
            'primer_nombre' => strtoupper($data['primer_nombre']),
            'segundo_nombre' => $this->normalizeOptionalUpper($data['segundo_nombre'] ?? null),
            'primer_apellido' => strtoupper($data['primer_apellido']),
            'segundo_apellido' => $this->normalizeOptionalUpper($data['segundo_apellido'] ?? null),
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'genero' => $data['genero'],
            'telefono' => $data['telefono'] ?? null,
            'celular' => $data['celular'],
            'email' => strtolower($data['email']),
            'pais_id' => $data['pais_id'],
            'departamento_id' => $data['departamento_id'],
            'municipio_id' => $data['municipio_id'],
            'direccion' => $data['direccion'],
            'user_create_id' => $this->resolveAuditableUserId(),
            'user_edit_id' => $this->resolveAuditableUserId(),
        ]);

        $user = User::create([
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['numero_documento']),
            'status' => 1,
            'persona_id' => $persona->id,
        ]);

        $user->assignRole('VISITANTE');

        $this->syncCaracterizaciones($persona, $caracterizacionIds);

        return [$persona, $user];
    }

    private function actualizarRolesSegunInscripcion(Persona $persona, User $user): void
    {
        if (!$this->tieneInscripciones($persona)) {
            return;
        }

        $user->removeRole('VISITANTE');
        $user->assignRole('ASPIRANTE');
    }

    private function tieneInscripciones(Persona $persona): bool
    {
        return AspiranteComplementario::where('persona_id', $persona->id)->exists();
    }

    private function normalizeOptionalUpper(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : strtoupper($trimmed);
    }

    private function resolvePostRegistroRedirect(Persona $persona, bool $tieneInscripciones): RedirectResponse
    {
        if ($tieneInscripciones) {
            $inscripcionPendiente = AspiranteComplementario::where('persona_id', $persona->id)
                ->where('estado', 1)
                ->whereNull('documento_identidad_path')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($inscripcionPendiente) {
                return redirect()
                    ->route('programas-complementarios.documentos', [
                        'id' => $inscripcionPendiente->complementario_id,
                        'aspirante_id' => $inscripcionPendiente->id,
                    ])
                    ->with(
                        'success',
                        '¡Registro Exitoso! Complete el proceso subiendo su documento de identidad.'
                    );
            }
        }

        return redirect()
            ->route('programas-complementarios.index')
            ->with(
                'success',
                '¡Registro Exitoso! Ahora puede inscribirse en los programas complementarios disponibles.'
            );
    }

    /**
     * @param array<string,mixed> $data
     * @return array<int,int>
     */
    private function extractCaracterizacionIds(array $data): array
    {
        return collect($data['caracterizacion_ids'] ?? [])
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param array<int,int> $caracterizacionIds
     */
    private function syncCaracterizaciones(Persona $persona, array $caracterizacionIds): void
    {
        $persona->caracterizacionesComplementarias()->sync($caracterizacionIds);

        if (!empty($caracterizacionIds)) {
            $persona->updateQuietly([
                'parametro_id' => $caracterizacionIds[0],
            ]);
        }
    }

    private function resolveAuditableUserId(): ?int
    {
        if (Auth::check()) {
            return Auth::id();
        }

        $candidates = collect([
            config('app.audit_default_user_id'),
            config('registro.audit_default_user_id'),
        ])->filter(fn($id) => $id !== null)
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $resolvedId = $candidates
            ->first(fn($candidateId) => User::whereKey($candidateId)->exists());

        if (!$resolvedId) {
            $resolvedId = User::role('ADMIN')->value('id');
        }

        if (!$resolvedId) {
            $resolvedId = User::orderBy('id')->value('id');
        }

        return $resolvedId ? (int) $resolvedId : null;
    }

    private function resolveDocumentos(): object
    {
        $tema = $this->temaRepository->obtenerTiposDocumento();

        if ($tema && $tema->parametros?->count()) {
            return $tema;
        }

        return (object) [
            'parametros' => collect(config('registro.fallback_documentos', [])),
        ];
    }

    private function resolveGeneros(): object
    {
        $tema = $this->temaRepository->obtenerGeneros();

        if ($tema && $tema->parametros?->count()) {
            return $tema;
        }

        return (object) [
            'parametros' => collect(config('registro.fallback_generos', [])),
        ];
    }

    private function resolveCaracterizaciones(): object
    {
        $tema = $this->temaRepository->obtenerCaracterizacionesComplementarias();

        if ($tema && $tema->parametros?->count()) {
            return $tema;
        }

        return (object) ['parametros' => collect()];
    }

    private function resolveVias(): object
    {
        $tema = $this->temaRepository->obtenerVias();

        if ($tema && $tema->parametros?->count()) {
            return $tema;
        }

        return (object) ['parametros' => collect()];
    }

    private function resolveLetras(): object
    {
        $tema = $this->temaRepository->obtenerLetras();

        if ($tema && $tema->parametros?->count()) {
            return $tema;
        }

        return (object) ['parametros' => collect()];
    }

    private function resolveCardinales(): object
    {
        $tema = $this->temaRepository->obtenerCardinales();

        if ($tema && $tema->parametros?->count()) {
            return $tema;
        }

        return (object) ['parametros' => collect()];
    }
}
