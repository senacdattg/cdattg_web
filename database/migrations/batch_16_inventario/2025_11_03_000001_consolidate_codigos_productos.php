<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Consolidar datos: priorizar codigo_barras_sena si existe
        if (Schema::hasTable('productos')) {
            DB::statement("UPDATE productos SET codigo_barras = codigo_barras_sena WHERE codigo_barras_sena IS NOT NULL AND codigo_barras <> codigo_barras_sena");

            Schema::table('productos', function (Blueprint $table) {
                if (Schema::hasColumn('productos', 'codigo_barras_sena')) {
                    // Eliminar índice único si existe y luego la columna
                    try { $table->dropUnique('productos_codigo_barras_sena_unique'); } catch (\Throwable $e) {}
                    $table->dropColumn('codigo_barras_sena');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('productos')) {
            Schema::table('productos', function (Blueprint $table) {
                if (!Schema::hasColumn('productos', 'codigo_barras_sena')) {
                    $table->string('codigo_barras_sena', 11)->nullable()->unique()->after('codigo_barras');
                }
            });
            // No hay forma de recuperar valores previos de sena; se deja nulo
        }
    }
};


