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
        Schema::table('programas_formacion', function (Blueprint $table) {
            $table->foreignId('nivel_formacion_id')->constrained('niveles_formacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programa_formacion', function (Blueprint $table) {
            $table->dropForeign(['nivel_formacion_id']);
            $table->dropColumn('nivel_formacion_id');

            $table->dropForeign(['modalidad_formacion_id']);
            $table->dropColumn('modalidad_formacion_id');
        });
    }
};
