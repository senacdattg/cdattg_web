<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('persona_contact_alerts')) {
            return;
        }

        Schema::create('persona_contact_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->foreignId('persona_import_id')->nullable()->constrained('persona_imports')->nullOnDelete();
            $table->boolean('missing_email')->default(false);
            $table->boolean('missing_celular')->default(false);
            $table->boolean('missing_telefono')->default(false);
            $table->string('observaciones')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['missing_email', 'missing_celular', 'missing_telefono'], 'persona_contact_alerts_missing_contact_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persona_contact_alerts');
    }
};

