<?php

namespace Database\Seeders;

use App\Models\RedConocimiento;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RedConocimientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el primer usuario administrador para auditoría
        $adminUser = User::first();
        $userId = $adminUser ? $adminUser->id : null;

        // Redes de conocimiento de la regional Guaviare
        $redes_conocimientos = [
            'ACTIVIDAD FÍSICA, RECREACIÓN Y DEPORTE',
            'ACUÍCOLA Y DE PESCA',
            'AGRÍCOLA',
            'AMBIENTAL',
            'ARTES GRÁFICAS',
            'ARTESANIA',
            'AUTOMOTOR',
            'COMERCIO Y VENTAS',
            'CONSTRUCCIÓN',
            'CULTURA',
            'ELECTRÓNICA Y AUTOMATIZACIÓN',
            'ENERGÍA ELÉCTRICA',
            'GESTIÓN ADMINISTRATIVA Y FINANCIERA',
            'HOTELERÍA Y TURISMO',
            'INFORMÁTICA, DISEÑO Y DESARROLLO DE SOFTWARE',
            'INFRAESTRUCTURA',
            'LOGÍSTICA Y GESTIÓN DE LA PRODUCCIÓN',
            'MATERIALES PARA LA INDUSTRIA',
            'MECÁNICA INDUSTRIAL',
            'PECUARIA',
            'QUÍMICA APLICADA',
            'SALUD',
            'SERVICIOS PERSONALES',
            'TELECOMUNICACIONES',
            'TEXTIL, CONFECCIÓN DISEÑO Y MODA'
        ];

        foreach ($redes_conocimientos as $nombre) {
            RedConocimiento::create([
                'nombre' => $nombre,
                'user_create_id' => $userId,
                'user_edit_id' => $userId,
                'status' => 1, // Activo por defecto
            ]);
        }
    }
}