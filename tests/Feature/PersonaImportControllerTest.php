<?php

namespace Tests\Feature;

use App\Jobs\ProcessPersonaImportJob;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Persona;
use App\Models\PersonaImport;
use App\Models\User;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PersonaImportControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->refreshTestDatabase();

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
            ], [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_LENGTH' => $archivo->getSize(),
            ]);

        $response->assertCreated();
        $response->assertJsonStructure(['message', 'import_id']);

        Queue::assertPushed(ProcessPersonaImportJob::class);
        $this->assertDatabaseCount('persona_imports', 1);

        $import = PersonaImport::first();
        $this->assertNotNull($import);
        $this->assertSame('Importación encolada correctamente.', $response->json('message'));
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('local');
        $disk->assertExists($import->path);
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
            'segundo_nombre' => '',
            'primer_apellido' => 'USER',
            'segundo_apellido' => '',
            'direccion' => '',
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

    private function refreshTestDatabase(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('persona_contact_alerts');
        Schema::dropIfExists('persona_import_issues');
        Schema::dropIfExists('persona_imports');
        Schema::dropIfExists('users');
        Schema::dropIfExists('personas');
        Schema::enableForeignKeyConstraints();

        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento')->nullable();
            $table->string('numero_documento')->unique();
            $table->string('primer_nombre');
            $table->string('segundo_nombre')->nullable();
            $table->string('primer_apellido');
            $table->string('segundo_apellido')->nullable();
            $table->string('telefono')->nullable();
            $table->string('celular')->nullable();
            $table->string('email')->nullable()->unique();
            $table->unsignedTinyInteger('status')->default(1);
            $table->unsignedBigInteger('user_create_id')->nullable();
            $table->unsignedBigInteger('user_edit_id')->nullable();
            $table->string('direccion')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->nullable()->constrained('personas')->cascadeOnDelete();
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedTinyInteger('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('persona_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('original_name');
            $table->string('disk')->default('local');
            $table->string('path');
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('processed_rows')->default(0);
            $table->unsignedInteger('success_count')->default(0);
            $table->unsignedInteger('duplicate_count')->default(0);
            $table->unsignedInteger('missing_contact_count')->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('persona_import_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_import_id')->constrained('persona_imports')->cascadeOnDelete();
            $table->unsignedInteger('row_number')->nullable();
            $table->string('issue_type')->nullable();
            $table->string('numero_documento')->nullable();
            $table->string('email')->nullable();
            $table->string('celular')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });

        Schema::create('persona_contact_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->foreignId('persona_import_id')->constrained('persona_imports')->cascadeOnDelete();
            $table->boolean('missing_email')->default(false);
            $table->boolean('missing_celular')->default(false);
            $table->boolean('missing_telefono')->default(false);
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }
}
