<?php

namespace Database\Seeders;

use App\Models\Ambiente;
use Illuminate\Database\Seeder;
use Database\Seeders\Concerns\TruncatesTables;

class AmbienteSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateModel(Ambiente::class);

        // ambientes pisos centro
        // p1
        Ambiente::create(['id' => 1, 'title' => 'CENTRO-AUDITORIO', 'piso_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 2, 'title' => 'CENTRO-CANCHA', 'piso_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 3, 'title' => 'CENTRO-P1-A1-FARMACIA', 'piso_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 4, 'title' => 'CENTRO-P1-A2-PELUQUERIA', 'piso_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 5, 'title' => 'CENTRO-P1-A3-COSMETOLOGIA', 'piso_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 6, 'title' => 'CENTRO-P1-A4-MANICURE', 'piso_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 7, 'title' => 'CENTRO-P1-A5-EMPRENDER', 'piso_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 8, 'title' => 'CENTRO-P1-A6-GIMNASIO', 'piso_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 9, 'title' => 'CENTRO-P1-A7-MOTOS', 'piso_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 10, 'title' => 'CENTRO-P1-A8-ELECTRICIDAD', 'piso_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 11, 'title' => 'CENTRO-P1-A9-ARTESANIAS', 'piso_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 12, 'title' => 'CENTRO-P1-Bodega_del_Aprendiz', 'piso_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // p2
        Ambiente::create(['id' => 13, 'title' => 'CENTRO-P2-A1-MUSICA', 'piso_id' => 2, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 14, 'title' => 'CENTRO-P2-A2-ENFERMERIA', 'piso_id' => 2, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 15, 'title' => 'CENTRO-P2-A3-REDES', 'piso_id' => 2, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 16, 'title' => 'CENTRO-P2-A4-SISTEMAS', 'piso_id' => 2, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 17, 'title' => 'CENTRO-P2-A5-BILINGUISMO', 'piso_id' => 2, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // AMBIENTES BIODIVERSA KM11
        Ambiente::create(['id' => 18, 'title' => 'BIODIVERSA KM11-AMBIENTE PRACTICAS', 'piso_id' => 3, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // AMBIENTES MODELO
        // B1
        Ambiente::create(['id' => 19, 'title' => 'AULA_MOVIL', 'piso_id' => 4, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 20, 'title' => 'MODELO-B1-AUDITORIO', 'piso_id' => 4, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 21, 'title' => 'MODELO-B7-TORRE ALTURAS', 'piso_id' => 4, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // B2
        // P1
        Ambiente::create(['id' => 22, 'title' => 'MODELO-B2-P1-A1', 'piso_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 23, 'title' => 'MODELO-B2-P1-A2', 'piso_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 24, 'title' => 'MODELO-B2-P1-A3-MODISTERIA', 'piso_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 25, 'title' => 'MODELO-B2-P1-A4', 'piso_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 26, 'title' => 'MODELO-B2-P1-A5-MODISTERIA', 'piso_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 27, 'title' => 'MODELO-B2-P1-A6', 'piso_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 28, 'title' => 'MODELO-B2-P1-A7', 'piso_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 29, 'title' => 'MODELO-B2-P1-A8', 'piso_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 30, 'title' => 'MODELO-B2-P1-COCINA', 'piso_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 31, 'title' => 'MODELO-B2-P1-PANADERIA', 'piso_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // P2
        Ambiente::create(['id' => 32, 'title' => 'MODELO-B2-P2-A1', 'piso_id' => 6, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 33, 'title' => 'MODELO-B2-P2-A2', 'piso_id' => 6, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 34, 'title' => 'MODELO-B2-P2-A3', 'piso_id' => 6, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 35, 'title' => 'MODELO-B2-P2-A4', 'piso_id' => 6, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 36, 'title' => 'MODELO-B2-P2-A5', 'piso_id' => 6, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 37, 'title' => 'MODELO-B2-P2-A6', 'piso_id' => 6, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 38, 'title' => 'MODELO-B2-P2-A7', 'piso_id' => 6, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 39, 'title' => 'MODELO-B2-P2-A8', 'piso_id' => 6, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 40, 'title' => 'MODELO-B2-P2-LAB G DOCUMENTAL', 'piso_id' => 6, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // P3
        Ambiente::create(['id' => 41, 'title' => 'MODELO-B2-P3-A1', 'piso_id' => 7, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 42, 'title' => 'MODELO-B2-P3-A10', 'piso_id' => 7, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 43, 'title' => 'MODELO-B2-P3-A2', 'piso_id' => 7, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 44, 'title' => 'MODELO-B2-P3-A3', 'piso_id' => 7, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 45, 'title' => 'MODELO-B2-P3-A4', 'piso_id' => 7, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 46, 'title' => 'MODELO-B2-P3-A5', 'piso_id' => 7, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 47, 'title' => 'MODELO-B2-P3-A6-CO CREACION', 'piso_id' => 7, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 48, 'title' => 'MODELO-B2-P3-A7', 'piso_id' => 7, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 49, 'title' => 'MODELO-B2-P3-A8-LEGO', 'piso_id' => 7, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 50, 'title' => 'MODELO-B2-P3-A9', 'piso_id' => 7, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // B3
        // P1
        Ambiente::create(['id' => 51, 'title' => 'MODELO-B3-P1-BIBLIOTECA', 'piso_id' => 8, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 52, 'title' => 'MODELO-B3-P1-PASILLO BIBLIOTECA', 'piso_id' => 8, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 53, 'title' => 'MODELO-B3-P1-TALLER CONSTRUCCIÓN', 'piso_id' => 8, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // P2
        Ambiente::create(['id' => 54, 'title' => 'MODELO-B3-P2-LAB BIORREMEDIACIÓN', 'piso_id' => 9, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 55, 'title' => 'MODELO-B3-P2-LAB BIOTECNOLOGÍA', 'piso_id' => 9, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 56, 'title' => 'MODELO-B3-P2-LAB BROMATOLOGÍA', 'piso_id' => 9, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 57, 'title' => 'MODELO-B3-P2-LAB SUELOS', 'piso_id' => 9, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // P3
        Ambiente::create(['id' => 58, 'title' => 'MODELO-B3-P3-DESARROLLO DE SISTEMAS', 'piso_id' => 10, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 59, 'title' => 'MODELO-B3-P3-MULTIMEDIA', 'piso_id' => 10, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 60, 'title' => 'MODELO-B3-P3-PLM CAD Y ROBÓTICA', 'piso_id' => 10, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 61, 'title' => 'MODELO-B3-P3-SISTEMAS DE INFORMACIÓN', 'piso_id' => 10, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // B5
        // P1
        Ambiente::create(['id' => 62, 'title' => 'MODELO-B5-TALLER AGROINDUSTRIA', 'piso_id' => 11, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // B6
        // P1
        Ambiente::create(['id' => 63, 'title' => 'MODELO-B6-COLISEO', 'piso_id' => 12, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // AMBIENTES EXTERNOS
        // SAN JOSE
        // JOAQUIN PARIS
        Ambiente::create(['id' => 64, 'title' => 'EXTERNO - BATALLON JOAQUIN PARIS', 'piso_id' => 13, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // CARPINTERIA CENTRO DE CONVENIOS
        Ambiente::create(['id' => 65, 'title' => 'EXTERNO - CARPINTERIA CONVENIOS', 'piso_id' => 14, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // PANADERIA CHARRAS
        Ambiente::create(['id' => 66, 'title' => 'EXTERNO - CHARRAS - PANADERIA', 'piso_id' => 15, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // COLINAS
        Ambiente::create(['id' => 67, 'title' => 'EXTERNO - COLINAS', 'piso_id' => 16, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // GENERICO
        Ambiente::create(['id' => 68, 'title' => 'EXTERNO - GENERICO', 'piso_id' => 17, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // CALAMAR
        Ambiente::create(['id' => 69, 'title' => 'EXTERNO - CALAMAR', 'piso_id' => 18, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // EL RETORNO
        Ambiente::create(['id' => 70, 'title' => 'EXTERNO - CHAPARRAL', 'piso_id' => 19, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        Ambiente::create(['id' => 71, 'title' => 'EXTERNO - RETORNO', 'piso_id' => 20, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // MIRAFLORES
        Ambiente::create(['id' => 72, 'title' => 'EXTERNO - MIRAFLORES', 'piso_id' => 21, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // MAPIRIPAN
        Ambiente::create(['id' => 73, 'title' => 'EXTERNO - MAPIRIPAN', 'piso_id' => 22, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);
        // PUERTO CONCORDIA
        Ambiente::create(['id' => 74, 'title' => 'EXTERNO - PUERTO CONCORDIA', 'piso_id' => 23, 'user_create_id' => 1, 'user_edit_id' => 1, 'status' => 1]);










    }
}
