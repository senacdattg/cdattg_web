<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Modifica la foreign key para eliminar el usuario cuando se elimina la persona
     */
    public function up(): void
    {
        // Obtener el nombre exacto de la foreign key
        $foreignKeyName = $this->getForeignKeyName();

        if ($foreignKeyName) {
            // Si existe, eliminarla usando el nombre exacto
            DB::statement("ALTER TABLE users DROP FOREIGN KEY {$foreignKeyName}");
        }

        // Recrear con onDelete cascade
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('persona_id')
                ->references('id')
                ->on('personas')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar la foreign key con cascade
            $table->dropForeign(['persona_id']);

            // Recrear sin cascade (comportamiento original)
            $table->foreign('persona_id')
                ->references('id')
                ->on('personas');
        });
    }

    /**
     * Obtiene el nombre de la foreign key de persona_id en users
     */
    private function getForeignKeyName(): ?string
    {
        $foreignKeys = DB::select(
            "SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'users'
            AND COLUMN_NAME = 'persona_id'
            AND REFERENCED_TABLE_NAME = 'personas'"
        );

        return $foreignKeys[0]->CONSTRAINT_NAME ?? null;
    }
};
