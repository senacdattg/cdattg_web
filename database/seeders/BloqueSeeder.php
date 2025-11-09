<?php

namespace Database\Seeders;

use App\Models\Bloque;
use Illuminate\Database\Seeder;
use Database\Seeders\Concerns\TruncatesTables;

class BloqueSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateModel(Bloque::class);

        // SEDE CENTRO
        Bloque::create(['id' => 1, 'bloque' =>'CENTRO', 'sede_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // SEDE BIODIVERSA
        Bloque::create(['id' => 2, 'bloque' => 'BIODIVERSA', 'sede_id' => 3, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        //SEDE MODELO
        Bloque::create(['id' => 3, 'bloque' => 'B1', 'sede_id' => 2, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Bloque::create(['id' => 4, 'bloque' => 'B2', 'sede_id' => 2, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Bloque::create(['id' => 5, 'bloque' => 'B3', 'sede_id' => 2, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Bloque::create(['id' => 6, 'bloque' => 'B5', 'sede_id' => 2, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Bloque::create(['id' => 7, 'bloque' => 'B6', 'sede_id' => 2, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // SEDE AMBIENTE EXTERNO SAN JOSE
        Bloque::create(['id' => 8, 'bloque' => 'JOAQUIN PARIS', 'sede_id' => 4, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Bloque::create(['id' => 9, 'bloque' => 'CARPINTERIA CENTRO DE CONVENIOS', 'sede_id' => 4, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Bloque::create(['id' => 10, 'bloque' => 'PANADERIA CHARRAS', 'sede_id' => 4, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Bloque::create(['id' => 11, 'bloque' => 'COLINAS', 'sede_id' => 4, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Bloque::create(['id' => 12, 'bloque' => 'GENERICO', 'sede_id' => 4, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // SEDE AMBIENTE EXTERNO CALAMAR
        Bloque::create(['id' => 13, 'bloque' => 'CALAMAR', 'sede_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // SEDE AMBEINTE EXTERNO EL RETORNO
        Bloque::create(['id' => 14, 'bloque' => 'VEREDA CHAPARRAL', 'sede_id' => 6, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Bloque::create(['id' => 15, 'bloque' => 'EL RETORNO', 'sede_id' => 6, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // SEDE AMBIENTE EXTERNO MIRAFLORES
        Bloque::create(['id' => 16, 'bloque' => 'MIRAFLORES', 'sede_id' => 7, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // SEDE AMBIENTE EXTERNO MAPIRIPAN
        Bloque::create(['id' => 17, 'bloque' => 'MAPIRIPAN', 'sede_id' => 8, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // SEDE AMBIENTE EXTERNO PUERTO CONCORDIA
        Bloque::create(['id' => 18, 'bloque' => 'PUERTO CONCORDIA', 'sede_id' => 9, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
    }
}
