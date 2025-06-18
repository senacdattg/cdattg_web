<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('competencias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->nullable(false);
            $table->string('nombre', 255)->nullable(false);
            $table->integer('duracion')->nullable(false);
            $table->text('descripcion')->nullable(false);
            $table->date('fecha_inicio')->nullable(false);
            $table->date('fecha_fin')->nullable(false);
            $table->foreignId('user_create_id')->nullable()->constrained('users');
            $table->foreignId('user_edit_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        $this->competencia_programa();
    }

    public function competencia_programa(): void
    {
        Schema::create('competencia_programa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competencia_id')->nullable()->constrained('competencias');
            $table->foreignId('programa_id')->nullable()->constrained('programas_formacion');
            $table->foreignId('user_create_id')->nullable()->constrained('users');
            $table->foreignId('user_edit_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competencias');
        Schema::dropIfExists('competencia_programa');
    }
};
