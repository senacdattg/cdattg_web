<?php

namespace Tests\Feature;

use App\Jobs\ProcessPersonaImportJob;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PersonaImportControllerTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $exitCode = Artisan::call('migrate:module', ['--all' => true, '--fresh' => true]);

        if ($exitCode !== 0) {
            $this->fail('Fallo al ejecutar las migraciones para pruebas: ' . Artisan::output());
        }

        $this->withoutMiddleware('permission');
        $this->withoutMiddleware(Authorize::class);
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    public function test_usuario_con_permiso_puede_iniciar_importacion(): void
    {
        Queue::fake();
        Storage::fake('local');

        $user = $this->crearUsuario();

        $archivo = UploadedFile::fake()->create('personas.xlsx', 10, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $response = $this->actingAs($user)
            ->post('/personas/importar', [
                'archivo_excel' => $archivo,
            ], ['HTTP_ACCEPT' => 'application/json']);

        $response->assertCreated();
        $response->assertJsonStructure(['message', 'import_id']);

        Queue::assertPushed(ProcessPersonaImportJob::class);
        $this->assertDatabaseCount('persona_imports', 1);
    }

    public function test_validacion_archivo_es_obligatoria(): void
    {
        $request = new \App\Http\Requests\PersonaImportRequest();

        $validator = Validator::make([], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('archivo_excel', $validator->errors()->toArray());
    }

    private function crearUsuario(): User
    {
        $persona = Persona::create([
            'numero_documento' => uniqid('DOC'),
            'primer_nombre' => 'TEST',
            'primer_apellido' => 'USER',
            'email' => uniqid('user') . '@example.com',
            'status' => 1,
        ]);

        return User::create([
            'email' => $persona->email,
            'password' => 'password',
            'status' => 1,
            'persona_id' => $persona->id,
        ]);
    }
}
