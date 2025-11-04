<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('aspirantes_complementarios')) {
            Schema::create('aspirantes_complementarios', function (Blueprint $table) {
                $table->id();
                $table->foreignId('persona_id')
                    ->constrained('personas')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();

                // 🔹 Aquí agregamos la columna faltante:
                $table->foreignId('complementario_id')
                    ->constrained('complementarios_ofertados') // 👈 asegúrate de que el nombre coincida con tu tabla real
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();

                $table->text('observaciones')->nullable();
                $table->boolean('estado')->default(1);
                $table->timestamps();

                // 🔹 Restricción única compuesta
                $table->unique(
                    ['persona_id', 'complementario_id'],
                    'aspirantes_complementarios_persona_programa_unique'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('aspirantes_complementarios');
    }
};
