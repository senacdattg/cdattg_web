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
            $table->foreignId('pais_id')->after('email')->nullable()->constrained('pais');
            $table->foreignId('departamento_id')->after('pais_id')->nullable()->constrained('departamentos');
            $table->foreignId('municipio_id')->after('departamento_id')->nullable()->constrained('municipios');
            $table->string('direccion')->after('municipio_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropForeign(['pais_id']);
            $table->dropForeign(['departamento_id']);
            $table->dropForeign(['municipio_id']);
            $table->dropColumn(['pais_id', 'departamento_id', 'municipio_id', 'direccion']);
        });
    }
};
