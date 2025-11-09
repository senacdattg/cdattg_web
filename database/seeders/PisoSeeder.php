<?php

namespace Database\Seeders;

use App\Models\Piso;
use Illuminate\Database\Seeder;
use Database\Seeders\Concerns\TruncatesTables;

class PisoSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateModel(Piso::class);

        // pisos bloque centro
        Piso::create(['id' => 1, 'piso' => 'P1', 'bloque_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Piso::create(['id' => 2, 'piso' => 'P2', 'bloque_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // PISOS BLOQUE BIODIVERSA KM11
        Piso::create(['id' => 3, 'piso' => 'P1', 'bloque_id' => 2, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // PISOS BLOQUES MODELO
        // B1
        Piso::create(['id' => 4, 'piso' => 'P1', 'bloque_id' => 3, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // B2
        Piso::create(['id' => 5, 'piso' => 'P1', 'bloque_id' => 4, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Piso::create(['id' => 6, 'piso' => 'P2', 'bloque_id' => 4, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Piso::create(['id' => 7, 'piso' => 'P3', 'bloque_id' => 4, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // B3
        Piso::create(['id' => 8, 'piso' => 'P1', 'bloque_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Piso::create(['id' => 9, 'piso' => 'P2', 'bloque_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Piso::create(['id' => 10, 'piso' => 'P3', 'bloque_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // B5
        Piso::create(['id' => 11, 'piso' => 'P1', 'bloque_id' => 6, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // B6
        Piso::create(['id' => 12, 'piso' => 'P1', 'bloque_id' => 7, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // PISOS AMBIENTES EXTERNOS
        // SAN JOSE
        // JOAQUIN PARIS
        Piso::create(['id' => 13, 'piso' => 'JOAQUIN PARIS', 'bloque_id' => 8, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // CARPINTERIA CENTRO DE CONVENIOS
        Piso::create(['id' => 14, 'piso' => 'CARPINTERIA CENTRO DE CONVENIOS', 'bloque_id' => 9, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // PANADERIA CHARRAS
        Piso::create(['id' => 15, 'piso' => 'PANADERIA CHARRAS', 'bloque_id' => 10, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // COLINAS
        Piso::create(['id' => 16, 'piso' => 'COLINAS', 'bloque_id' => 11, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // GENERICO
        Piso::create(['id' => 17, 'piso' => 'GENERICO', 'bloque_id' => 12, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // CALAMAR
        Piso::create(['id' => 18, 'piso' => 'P1', 'bloque_id' => 13, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // RETORNO
        Piso::create(['id' => 19, 'piso' => 'P1', 'bloque_id' => 14, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Piso::create(['id' => 20, 'piso' => 'P1', 'bloque_id' => 15, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // MIRAFLORES
        Piso::create(['id' => 21, 'piso' => 'P1', 'bloque_id' => 16, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // MAPIRIPAN
        Piso::create(['id' => 22, 'piso' => 'P1', 'bloque_id' => 17, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // PUERTO CONCORDIA
        Piso::create(['id' => 23, 'piso' => 'P1', 'bloque_id' => 18, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
    }
}
