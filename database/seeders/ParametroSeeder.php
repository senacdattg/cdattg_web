<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parametro; // Asegúrate de usar la notación correcta
use Database\Seeders\Concerns\TruncatesTables;

class ParametroSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateModel(Parametro::class);

        $parametros = [
            // Estados
            ['id' => 1, 'name' => 'ACTIVO', 'status' => 1],
            ['id' => 2, 'name' => 'INACTIVO', 'status' => 1],

            // Tipos de documento
            ['id' => 3, 'name' => 'CEDULA DE CIUDADANIA', 'status' => 1],
            ['id' => 4, 'name' => 'CEDULA DE EXTRANJERIA', 'status' => 1],
            ['id' => 5, 'name' => 'PASAPORTE', 'status' => 1],
            ['id' => 6, 'name' => 'TARJETA DE IDENTIDAD', 'status' => 1],
            ['id' => 7, 'name' => 'REGISTRO CIVIL', 'status' => 1],
            ['id' => 8, 'name' => 'SIN IDENTIFICACION', 'status' => 1],

            // Género
            ['id' => 9, 'name' => 'MASCULINO', 'status' => 1],
            ['id' => 10, 'name' => 'FEMENINO', 'status' => 1],
            ['id' => 11, 'name' => 'NO DEFINE', 'status' => 1],

            // Dias
            ['id' => 12, 'name' => 'LUNES', 'status' => 1],
            ['id' => 13, 'name' => 'MARTES', 'status' => 1],
            ['id' => 14, 'name' => 'MIERCOLES', 'status' => 1],
            ['id' => 15, 'name' => 'JUEVES', 'status' => 1],
            ['id' => 16, 'name' => 'VIERNES', 'status' => 1],
            ['id' => 17, 'name' => 'SABADO', 'status' => 1],

            // Modalidades
            ['id' => 18, 'name' => 'PRESENCIAL', 'status' => 1],
            ['id' => 19, 'name' => 'VIRTUAL', 'status' => 1],
            ['id' => 20, 'name' => 'MIXTA', 'status' => 1],

            // Niveles de formación
            ['id' => 21, 'name' => 'TÉCNICO', 'status' => 1],
            ['id' => 22, 'name' => 'TECNÓLOGO', 'status' => 1],
            ['id' => 23, 'name' => 'AUXILIAR', 'status' => 1],
            ['id' => 24, 'name' => 'OPERARIO', 'status' => 1],

            // Estados de evidencias
            ['id' => 25, 'name' => 'PENDIENTE', 'status' => 1],
            ['id' => 26, 'name' => 'EN CURSO', 'status' => 1],
            ['id' => 27, 'name' => 'COMPLETADO', 'status' => 1],

            // Tipos de producto
            ['id' => 28, 'name' => 'CONSUMIBLE', 'status' => 1],
            ['id' => 29, 'name' => 'NO CONSUMIBLE', 'status' => 1],

            // Unidades de medida
            ['id' => 30, 'name' => 'GRAMOS', 'status' => 1],
            ['id' => 31, 'name' => 'LIBRAS', 'status' => 1],
            ['id' => 32, 'name' => 'KILOGRAMOS', 'status' => 1],
            ['id' => 33, 'name' => 'ARROBA', 'status' => 1],
            ['id' => 34, 'name' => 'QUINTAL', 'status' => 1],
            ['id' => 35, 'name' => 'ONZA', 'status' => 1],
            ['id' => 36, 'name' => 'MILILITRO', 'status' => 1],
            ['id' => 37, 'name' => 'LITRO', 'status' => 1],
            ['id' => 38, 'name' => 'GALÓN', 'status' => 1],
            ['id' => 39, 'name' => 'ONZA LÍQUIDA', 'status' => 1],
            ['id' => 40, 'name' => 'BARRIL', 'status' => 1],
            ['id' => 41, 'name' => 'UNIDADES', 'status' => 1],
            
            // Estados de producto
            ['id' => 42, 'name' => 'DISPONIBLE', 'status' => 1],
            ['id' => 43, 'name' => 'AGOTADO', 'status' => 1],

            // Tipos de orden
            ['id' => 44, 'name' => 'PRÉSTAMO', 'status' => 1],
            ['id' => 45, 'name' => 'SALIDA', 'status' => 1],

            // Estados de orden
            ['id' => 46, 'name' => 'EN ESPERA', 'status' => 1],
            ['id' => 47, 'name' => 'APROBADA', 'status' => 1],
            ['id' => 48, 'name' => 'RECHAZADA', 'status' => 1],
            
            // Estados de aprobaciones
            ['id' => 49, 'name' => 'ENTREGADA', 'status' => 1],
            ['id' => 50, 'name' => 'EN PRÉSTAMO', 'status' => 1],

            // Categorías
            ['id' => 51, 'name' => 'EMPAQUE', 'status' => 1],
            ['id' => 52, 'name' => 'EPP', 'status' => 1],
            ['id' => 53, 'name' => 'EQUIPO', 'status' => 1],
            ['id' => 54, 'name' => 'FUNGIBLE', 'status' => 1],
            ['id' => 55, 'name' => 'INSTRUMENTO MEDICION', 'status' => 1],
            ['id' => 56, 'name' => 'INSUMO', 'status' => 1],
            ['id' => 57, 'name' => 'REACTIVO', 'status' => 1],
            ['id' => 58, 'name' => 'UTENSILIO', 'status' => 1],
            ['id' => 59, 'name' => 'VIDRIERIA', 'status' => 1],

            // Marcas (Del CSV de laboratorio - sin duplicados ni N/A)
            ['id' => 60, 'name' => 'LAMOTTE', 'status' => 1],
            ['id' => 61, 'name' => 'PYREX', 'status' => 1],
            ['id' => 62, 'name' => 'MERCK', 'status' => 1],
            ['id' => 63, 'name' => 'KIMAX', 'status' => 1],
            ['id' => 64, 'name' => 'HACH', 'status' => 1],
            ['id' => 65, 'name' => 'MILLIPORE', 'status' => 1],
            ['id' => 66, 'name' => 'BOECO', 'status' => 1],
            ['id' => 67, 'name' => 'BRAND', 'status' => 1],
            ['id' => 68, 'name' => 'DURAN', 'status' => 1], 
            ['id' => 69, 'name' => 'PANREAC', 'status' => 1],
            ['id' => 70, 'name' => 'BRIXCO', 'status' => 1],
            ['id' => 71, 'name' => 'CLOROX', 'status' => 1],
            ['id' => 72, 'name' => 'GOLDENWRAP', 'status' => 1],
            ['id' => 73, 'name' => 'ARO', 'status' => 1],
            ['id' => 74, 'name' => 'MILWAUKEE', 'status' => 1],
            ['id' => 75, 'name' => 'HANNA', 'status' => 1],
            ['id' => 76, 'name' => 'ALPHA CHEMIKA', 'status' => 1],
            ['id' => 77, 'name' => 'SUPELCO', 'status' => 1],
            ['id' => 78, 'name' => 'CITOTEST', 'status' => 1],
            ['id' => 79, 'name' => 'BIOPOINTE SCIENTIFIC', 'status' => 1],
            ['id' => 80, 'name' => 'CITOGLAS', 'status' => 1],
            ['id' => 81, 'name' => 'GLASSCO', 'status' => 1],
            ['id' => 82, 'name' => 'LABSCIENT', 'status' => 1],
            ['id' => 83, 'name' => 'KIMBLE', 'status' => 1],
            ['id' => 84, 'name' => 'NADIR', 'status' => 1],
            ['id' => 85, 'name' => 'VIDRIOLAB', 'status' => 1],
            ['id' => 86, 'name' => 'GOTOPLAS', 'status' => 1],
            ['id' => 87, 'name' => 'PLASTIRED', 'status' => 1],
            ['id' => 88, 'name' => 'PLASTICOS R&M', 'status' => 1],
            ['id' => 89, 'name' => 'SCOTCH-BRITE', 'status' => 1],
            ['id' => 90, 'name' => 'TRAMONTINA', 'status' => 1],
            ['id' => 91, 'name' => 'CORONA', 'status' => 1],
            ['id' => 92, 'name' => 'IMUSA', 'status' => 1],
            ['id' => 93, 'name' => 'FARBERWARE', 'status' => 1],
            ['id' => 94, 'name' => 'JGB', 'status' => 1],
            ['id' => 95, 'name' => 'BAXTER', 'status' => 1],
            ['id' => 96, 'name' => 'B.D', 'status' => 1],
            ['id' => 97, 'name' => 'BAYER', 'status' => 1],
            ['id' => 98, 'name' => 'BIOPONTERCIENTIFIC', 'status' => 1],
            ['id' => 99, 'name' => 'NANOCOLOR', 'status' => 1],
            ['id' => 100, 'name' => 'WTW', 'status' => 1],
            ['id' => 101, 'name' => 'MACHEREY NAGEL', 'status' => 1],
            ['id' => 102, 'name' => 'PHYTOTECH', 'status' => 1],
            ['id' => 103, 'name' => 'GE HEALTHCARE', 'status' => 1],
            ['id' => 104, 'name' => 'MEDISPO', 'status' => 1],
            ['id' => 105, 'name' => 'MIDMARK', 'status' => 1],
            ['id' => 106, 'name' => 'VITAL MEDIC', 'status' => 1],
            ['id' => 107, 'name' => 'MINE MEDICAL', 'status' => 1],
            ['id' => 108, 'name' => 'ZAFIRO', 'status' => 1],
            ['id' => 109, 'name' => 'CHEMI', 'status' => 1],
            ['id' => 110, 'name' => 'AZUCAR INCAUCA', 'status' => 1],
            ['id' => 111, 'name' => 'MAIZENA', 'status' => 1],
            ['id' => 112, 'name' => 'COLOMBINA-ZEV', 'status' => 1],
            ['id' => 113, 'name' => 'LUKER', 'status' => 1],
            ['id' => 114, 'name' => 'SANTO DOMINGO', 'status' => 1],
            ['id' => 115, 'name' => 'BETTY CROCKER', 'status' => 1],
            ['id' => 116, 'name' => 'GERBER', 'status' => 1],
            ['id' => 117, 'name' => 'BLANCOX', 'status' => 1],
            ['id' => 118, 'name' => 'BRILLO AROMA', 'status' => 1],
            ['id' => 119, 'name' => 'BRILLAKING', 'status' => 1],
            ['id' => 120, 'name' => 'ASEPSIA', 'status' => 1],
            ['id' => 121, 'name' => 'SOLOASEO', 'status' => 1],
            ['id' => 122, 'name' => 'MAXWIPE', 'status' => 1],
            ['id' => 123, 'name' => 'EXAMTEX', 'status' => 1],
            ['id' => 124, 'name' => 'PROTEXION', 'status' => 1],
            ['id' => 125, 'name' => 'BODI SAFE', 'status' => 1],
            ['id' => 126, 'name' => 'CRISTAR', 'status' => 1],
            ['id' => 127, 'name' => 'DIMEDA', 'status' => 1],
            ['id' => 128, 'name' => 'DROFARMA', 'status' => 1],
            ['id' => 129, 'name' => 'INVERFARMA', 'status' => 1],
            ['id' => 130, 'name' => 'BIOLOGIKA', 'status' => 1],
            ['id' => 131, 'name' => 'BIOHALL', 'status' => 1],
            ['id' => 132, 'name' => 'ABCLABORATORIOS', 'status' => 1],
            ['id' => 133, 'name' => 'CIACOMEQ S.A.S', 'status' => 1],
            ['id' => 134, 'name' => 'LEGAQUIMICOS', 'status' => 1],
            ['id' => 135, 'name' => 'QUINSA', 'status' => 1],
            ['id' => 136, 'name' => 'QUIMPO', 'status' => 1],
            ['id' => 137, 'name' => 'MOL LABS', 'status' => 1],
            ['id' => 138, 'name' => 'METALLURGICA MOTTA', 'status' => 1],
            ['id' => 139, 'name' => 'MOTTA', 'status' => 1],
            ['id' => 140, 'name' => 'LEON', 'status' => 1],
            ['id' => 141, 'name' => 'ALGARRA', 'status' => 1],
            ['id' => 142, 'name' => 'HOPEX', 'status' => 1],
            ['id' => 143, 'name' => 'KRAMER', 'status' => 1],
            ['id' => 144, 'name' => 'MP TOOLS', 'status' => 1],
            ['id' => 145, 'name' => 'PISCICLORO', 'status' => 1],
            ['id' => 146, 'name' => 'FORTILECHE', 'status' => 1],
            ['id' => 147, 'name' => 'COLINAGRO', 'status' => 1],
            ['id' => 148, 'name' => 'CAL', 'status' => 1],
            ['id' => 149, 'name' => 'PINTO', 'status' => 1],
            ['id' => 150, 'name' => 'ROSA', 'status' => 1],
            ['id' => 151, 'name' => 'SAN JORGE', 'status' => 1],
            ['id' => 152, 'name' => 'TROPICAL', 'status' => 1],
            ['id' => 153, 'name' => 'BEISBOL NATURAL', 'status' => 1],
            ['id' => 154, 'name' => 'FRUTUROMA', 'status' => 1],
            ['id' => 155, 'name' => 'POLAROMA', 'status' => 1],
            ['id' => 156, 'name' => 'NOSTALGIA', 'status' => 1],
            ['id' => 157, 'name' => 'PUMP', 'status' => 1],
            ['id' => 158, 'name' => 'WATER WORKS', 'status' => 1],
            ['id' => 159, 'name' => 'BUFFER POWER', 'status' => 1],
            ['id' => 160, 'name' => 'AMCOR', 'status' => 1],
            ['id' => 161, 'name' => 'BESTON', 'status' => 1],
            ['id' => 162, 'name' => 'GIANT', 'status' => 1],
            ['id' => 163, 'name' => 'ELITE', 'status' => 1],
            ['id' => 164, 'name' => 'ATHOS', 'status' => 1],
            ['id' => 165, 'name' => 'TUSKA', 'status' => 1],
            ['id' => 166, 'name' => 'VISMARCK', 'status' => 1],
            ['id' => 167, 'name' => 'VIMACH', 'status' => 1],
            ['id' => 168, 'name' => 'MIO', 'status' => 1],
            ['id' => 169, 'name' => 'MK', 'status' => 1],
            ['id' => 170, 'name' => 'MC', 'status' => 1],
            ['id' => 171, 'name' => 'PD', 'status' => 1],
            ['id' => 172, 'name' => 'JM', 'status' => 1],
            ['id' => 173, 'name' => 'FPC', 'status' => 1],
            ['id' => 174, 'name' => 'CHM', 'status' => 1],
            ['id' => 175, 'name' => 'SOL', 'status' => 1],
            ['id' => 176, 'name' => 'ZEV', 'status' => 1],
            ['id' => 177, 'name' => 'ALPHA', 'status' => 1],
            ['id' => 178, 'name' => 'SUPERDENT', 'status' => 1],
            ['id' => 179, 'name' => 'TONING', 'status' => 1],
        ];

        foreach ($parametros as $parametro) {
            Parametro::updateOrCreate(
                ['id' => $parametro['id']],
                [
                    'name' => $parametro['name'],
                    'status' => $parametro['status'],
                ]
            );
        }
    }
}
