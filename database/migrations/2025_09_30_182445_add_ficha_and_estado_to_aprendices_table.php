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
        Schema::table('aprendices', function (Blueprint $table) {
            $table->foreignId('ficha_caracterizacion_id')->nullable()->after('persona_id')->constrained('fichas_caracterizacion');
            $table->boolean('estado')->default(1)->after('ficha_caracterizacion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aprendices', function (Blueprint $table) {
            $table->dropForeign(['ficha_caracterizacion_id']);
            $table->dropColumn(['ficha_caracterizacion_id', 'estado']);
        });
    }
};
