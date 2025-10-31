<?php

/**
 * Script para corregir las claves foráneas de categoria_id y marca_id en la tabla productos
 * Estas deben apuntar a 'parametros' en lugar de 'parametros_temas'
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "🔧 Iniciando corrección de claves foráneas en tabla productos...\n\n";

    // Verificar si las columnas existen
    if (Schema::hasColumn('productos', 'categoria_id') && Schema::hasColumn('productos', 'marca_id')) {
        echo "✓ Las columnas categoria_id y marca_id existen\n";

        // Eliminar las claves foráneas incorrectas si existen
        try {
            Schema::table('productos', function ($table) {
                $table->dropForeign(['categoria_id']);
                echo "✓ Clave foránea categoria_id eliminada\n";
            });
        } catch (Exception $e) {
            echo "⚠ No se pudo eliminar la clave foránea categoria_id (puede que no exista): " . $e->getMessage() . "\n";
        }

        try {
            Schema::table('productos', function ($table) {
                $table->dropForeign(['marca_id']);
                echo "✓ Clave foránea marca_id eliminada\n";
            });
        } catch (Exception $e) {
            echo "⚠ No se pudo eliminar la clave foránea marca_id (puede que no exista): " . $e->getMessage() . "\n";
        }

        // Crear las claves foráneas correctas
        Schema::table('productos', function ($table) {
            $table->foreign('categoria_id')->references('id')->on('parametros')->onDelete('restrict');
            $table->foreign('marca_id')->references('id')->on('parametros')->onDelete('restrict');
        });

        echo "✓ Claves foráneas recreadas correctamente apuntando a 'parametros'\n";
    } else {
        echo "⚠ Las columnas categoria_id y/o marca_id no existen en la tabla productos\n";
    }

    echo "\n✅ Corrección completada exitosamente!\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}