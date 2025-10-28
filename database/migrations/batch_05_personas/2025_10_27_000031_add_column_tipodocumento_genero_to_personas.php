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
        Schema::table('personas', function (Blueprint $table) {
            $table->foreignId('tipo_documento')->after('id')->nullable()->constrained('parametros_temas');
            $table->foreignId('genero')->after('fecha_nacimiento')->nullable()->constrained('parametros_temas');

            $table->unique(['tipo_documento', 'numero_documento', 'primer_nombre', 'primer_apellido', 'fecha_nacimiento', 'genero'], 'personas_uk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            //
        });
    }
};
