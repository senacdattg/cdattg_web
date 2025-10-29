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
        Schema::table('red_conocimientos', function (Blueprint $table) {
            // Campo para relacionar con regional
            $table->unsignedBigInteger('regionals_id')->nullable()->after('nombre');
            $table->foreign('regionals_id')->references('id')->on('regionals')->onDelete('set null');
            
            // Campos de auditoría
            $table->unsignedBigInteger('user_create_id')->nullable()->after('regionals_id');
            $table->foreign('user_create_id')->references('id')->on('users')->onDelete('set null');
            
            $table->unsignedBigInteger('user_edit_id')->nullable()->after('user_create_id');
            $table->foreign('user_edit_id')->references('id')->on('users')->onDelete('set null');
            
            // Campo de estado
            $table->tinyInteger('status')->default(1)->after('user_edit_id')->comment('1=Activo, 0=Inactivo');
            
            // Hacer el campo nombre obligatorio y eliminar nullable
            $table->string('nombre', 255)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('red_conocimientos', function (Blueprint $table) {
            // Eliminar foreign keys primero
            $table->dropForeign(['regionals_id']);
            $table->dropForeign(['user_create_id']);
            $table->dropForeign(['user_edit_id']);
            
            // Eliminar columnas
            $table->dropColumn([
                'regionals_id',
                'user_create_id', 
                'user_edit_id',
                'status'
            ]);
            
            // Restaurar el campo nombre como nullable
            $table->string('nombre', 255)->nullable()->change();
        });
    }
};
