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
        Schema::table('complementarios_ofertados', function (Blueprint $table) {
            $table->dropColumn('descripcion');
            $table->text('justificacion')->nullable()->after('nombre');
            $table->text('requisitos_ingreso')->nullable()->after('justificacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complementarios_ofertados', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('nombre');
            $table->dropColumn(['justificacion', 'requisitos_ingreso']);
        });
    }
};
