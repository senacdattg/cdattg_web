<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Esta tabla registra la entrada y salida de TODAS las personas
     * (instructores, aprendices, visitantes, administrativos, etc.)
     * desde cualquier sede, para poder contar estadísticas de quién está dentro del edificio.
     */
    public function up(): void
    {
        Schema::create('persona_ingreso_salida', function (Blueprint $table) {
            $table->id();
            
            // Relación con la persona
            $table->foreignId('persona_id')->constrained('personas')->onDelete('cascade');
            
            // Relación con la sede (OBLIGATORIO - para saber desde qué sede se registra)
            $table->foreignId('sede_id')->constrained('sedes')->onDelete('restrict');
            
            // Tipo de persona: instructor, aprendiz, visitante, administrativo, aspirante
            $table->enum('tipo_persona', [
                'instructor',
                'aprendiz', 
                'visitante',
                'administrativo',
                'aspirante',
                'super_administrador'
            ])->index();
            
            // Fecha y hora de entrada
            $table->date('fecha_entrada')->index();
            $table->time('hora_entrada');
            $table->timestamp('timestamp_entrada')->index();
            
            // Fecha y hora de salida (nullable - si es null, la persona está dentro)
            $table->date('fecha_salida')->nullable()->index();
            $table->time('hora_salida')->nullable();
            $table->timestamp('timestamp_salida')->nullable();
            
            // Campos opcionales para contexto
            $table->foreignId('ambiente_id')->nullable()->constrained('ambientes')->onDelete('set null');
            $table->foreignId('ficha_caracterizacion_id')->nullable()->constrained('fichas_caracterizacion')->onDelete('set null');
            
            // Observaciones o notas adicionales
            $table->text('observaciones')->nullable();
            
            // Auditoría
            $table->foreignId('user_create_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('user_edit_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Índices para optimizar consultas de estadísticas
            $table->index(['tipo_persona', 'fecha_entrada']);
            $table->index(['persona_id', 'fecha_entrada']);
            $table->index(['sede_id', 'fecha_entrada']);
            $table->index(['sede_id', 'tipo_persona', 'fecha_entrada']);
            $table->index(['timestamp_entrada', 'timestamp_salida']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persona_ingreso_salida');
    }
};

