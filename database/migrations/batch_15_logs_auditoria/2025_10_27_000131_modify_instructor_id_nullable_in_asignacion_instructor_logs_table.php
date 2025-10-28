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
        Schema::table('asignacion_instructor_logs', function (Blueprint $table) {
            // Modificar la columna instructor_id para que permita null
            // Esto es necesario para registrar errores generales sin un instructor específico
            $table->unsignedBigInteger('instructor_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignacion_instructor_logs', function (Blueprint $table) {
            // Revertir el cambio (volver a NOT NULL)
            $table->unsignedBigInteger('instructor_id')->nullable(false)->change();
        });
    }
};

