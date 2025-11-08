<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (!Schema::hasColumn('regionals', 'departamento_id')) {
            Schema::table('regionals', function (Blueprint $table) use ($driver) {
                $column = $table->unsignedBigInteger('departamento_id')->nullable();

                if ($driver !== 'sqlite') {
                    $column->after('nombre');
                }
            });
        } elseif ($driver !== 'sqlite') {
            // Make sure it's nullable
            Schema::table('regionals', function (Blueprint $table) {
                $table->unsignedBigInteger('departamento_id')->nullable()->change();
            });
        }

        // Update invalid departamento_id to null
        DB::table('regionals')->whereNotIn('departamento_id', DB::table('departamentos')->pluck('id'))->update(['departamento_id' => null]);

        if ($driver !== 'sqlite' && !collect(DB::select("SHOW KEYS FROM regionals WHERE Column_name='departamento_id' AND Key_name LIKE '%foreign%'"))->isNotEmpty()) {
            Schema::table('regionals', function (Blueprint $table) {
                $table->foreign('departamento_id')->references('id')->on('departamentos');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regionals', function (Blueprint $table) {
            $table->dropForeign(['departamento_id']);
            $table->dropColumn('departamento_id');
        });
    }
};
