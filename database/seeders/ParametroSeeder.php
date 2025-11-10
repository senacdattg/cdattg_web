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
            ['id' => 1, 'name' => 'ACTIVO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 2, 'name' => 'INACTIVO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],

            // Tipos de documento
            ['id' => 3, 'name' => 'CEDULA DE CIUDADANIA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 4, 'name' => 'CEDULA DE EXTRANJERIA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 5, 'name' => 'PASAPORTE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 6, 'name' => 'TARJETA DE IDENTIDAD', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 7, 'name' => 'REGISTRO CIVIL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 8, 'name' => 'SIN IDENTIFICACION', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],

            // Género
            ['id' => 9, 'name' => 'MASCULINO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 10, 'name' => 'FEMENINO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 11, 'name' => 'NO DEFINE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],

            // Dias
            ['id' => 12, 'name' => 'LUNES', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 13, 'name' => 'MARTES', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 14, 'name' => 'MIERCOLES', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 15, 'name' => 'JUEVES', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 16, 'name' => 'VIERNES', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 17, 'name' => 'SABADO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],

            // Modalidades
            ['id' => 18, 'name' => 'PRESENCIAL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 19, 'name' => 'VIRTUAL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 20, 'name' => 'MIXTA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],

            // Niveles de formación
            ['id' => 21, 'name' => 'TÉCNICO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 22, 'name' => 'TECNÓLOGO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 23, 'name' => 'AUXILIAR', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 24, 'name' => 'OPERARIO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],

            // Estados de evidencias
            ['id' => 25, 'name' => 'PENDIENTE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 26, 'name' => 'EN CURSO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 27, 'name' => 'COMPLETADO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],

            // Tipos de producto
            ['id' => 28, 'name' => 'CONSUMIBLE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 29, 'name' => 'NO CONSUMIBLE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],

            // Unidades de medida
            ['id' => 30, 'name' => 'GRAMOS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 31, 'name' => 'LIBRAS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 32, 'name' => 'KILOGRAMOS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 33, 'name' => 'ARROBA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 34, 'name' => 'QUINTAL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 35, 'name' => 'ONZA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 36, 'name' => 'MILILITROS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 37, 'name' => 'LITROS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 38, 'name' => 'GALONES', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 39, 'name' => 'ONZA LÍQUIDA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 40, 'name' => 'BARRIL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 41, 'name' => 'UNIDADES', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 42, 'name' => 'CAJAS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 43, 'name' => 'METROS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 44, 'name' => 'CENTIMETROS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 45, 'name' => 'PAQUETES', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 46, 'name' => 'ROLLOS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 47, 'name' => 'TABLETAS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 48, 'name' => 'TEST', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 49, 'name' => 'SACKETS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            
            // Estados de producto
            ['id' => 50, 'name' => 'DISPONIBLE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 51, 'name' => 'AGOTADO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],

            // Tipos de orden
            ['id' => 52, 'name' => 'PRÉSTAMO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 53, 'name' => 'SALIDA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],

            // Estados de orden
            ['id' => 54, 'name' => 'EN ESPERA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 55, 'name' => 'APROBADA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 56, 'name' => 'RECHAZADA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            
            // Estados de aprobaciones
            ['id' => 57, 'name' => 'ENTREGADA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 58, 'name' => 'EN PRÉSTAMO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],

            // Categorías
            ['id' => 59, 'name' => 'EMPAQUE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 60, 'name' => 'EPP', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 61, 'name' => 'EQUIPO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 62, 'name' => 'FUNGIBLE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 63, 'name' => 'INSTRUMENTO MEDICION', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 64, 'name' => 'INSUMO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 65, 'name' => 'REACTIVO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 66, 'name' => 'UTENSILIO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 67, 'name' => 'VIDRIERIA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],

            // Marcas (Del CSV de laboratorio - sin duplicados ni N/A)
            ['id' => 68, 'name' => 'LAMOTTE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 69, 'name' => 'PYREX', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 70, 'name' => 'MERCK', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 71, 'name' => 'KIMAX', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 72, 'name' => 'HACH', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 73, 'name' => 'MILLIPORE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 74, 'name' => 'BOECO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 75, 'name' => 'BRAND', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 76, 'name' => 'DURAN', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null], 
            ['id' => 77, 'name' => 'PANREAC', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 78, 'name' => 'BRIXCO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 79, 'name' => 'CLOROX', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 80, 'name' => 'GOLDENWRAP', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 81, 'name' => 'ARO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 82, 'name' => 'MILWAUKEE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 83, 'name' => 'HANNA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 84, 'name' => 'ALPHA CHEMIKA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 85, 'name' => 'SUPELCO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 86, 'name' => 'CITOTEST', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 87, 'name' => 'BIOPOINTE SCIENTIFIC', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 88, 'name' => 'CITOGLAS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 89, 'name' => 'GLASSCO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 90, 'name' => 'LABSCIENT', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 91, 'name' => 'KIMBLE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 92, 'name' => 'NADIR', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 93, 'name' => 'VIDRIOLAB', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 94, 'name' => 'GOTOPLAS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 95, 'name' => 'PLASTIRED', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 96, 'name' => 'PLASTICOS R&M', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 97, 'name' => 'SCOTCH-BRITE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 98, 'name' => 'TRAMONTINA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 99, 'name' => 'CORONA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 100, 'name' => 'IMUSA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 101, 'name' => 'FARBERWARE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 102, 'name' => 'JGB', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 103, 'name' => 'BAXTER', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 104, 'name' => 'B.D', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 105, 'name' => 'BAYER', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 106, 'name' => 'BIOPONTERCIENTIFIC', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 107, 'name' => 'NANOCOLOR', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 108, 'name' => 'WTW', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 109, 'name' => 'MACHEREY NAGEL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 110, 'name' => 'PHYTOTECH', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 111, 'name' => 'GE HEALTHCARE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 112, 'name' => 'MEDISPO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 113, 'name' => 'MIDMARK', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 114, 'name' => 'VITAL MEDIC', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 115, 'name' => 'MINE MEDICAL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 116, 'name' => 'ZAFIRO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 117, 'name' => 'CHEMI', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 118, 'name' => 'AZUCAR INCAUCA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 119, 'name' => 'MAIZENA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 120, 'name' => 'COLOMBINA-ZEV', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 121, 'name' => 'LUKER', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 122, 'name' => 'SANTO DOMINGO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 123, 'name' => 'BETTY CROCKER', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 124, 'name' => 'GERBER', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 125, 'name' => 'BLANCOX', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 126, 'name' => 'BRILLO AROMA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 127, 'name' => 'BRILLAKING', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 128, 'name' => 'ASEPSIA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 129, 'name' => 'SOLOASEO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 130, 'name' => 'MAXWIPE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 131, 'name' => 'EXAMTEX', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 132, 'name' => 'PROTEXION', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 133, 'name' => 'BODI SAFE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 134, 'name' => 'CRISTAR', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 135, 'name' => 'DIMEDA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 136, 'name' => 'DROFARMA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 137, 'name' => 'INVERFARMA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 138, 'name' => 'BIOLOGIKA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 139, 'name' => 'BIOHALL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 140, 'name' => 'ABCLABORATORIOS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 141, 'name' => 'CIACOMEQ S.A.S', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 142, 'name' => 'LEGAQUIMICOS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 143, 'name' => 'QUINSA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 144, 'name' => 'QUIMPO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 145, 'name' => 'MOL LABS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 146, 'name' => 'METALLURGICA MOTTA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 147, 'name' => 'MOTTA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 148, 'name' => 'LEON', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 149, 'name' => 'ALGARRA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 150, 'name' => 'HOPEX', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 151, 'name' => 'KRAMER', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 152, 'name' => 'MP TOOLS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 153, 'name' => 'PISCICLORO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 154, 'name' => 'FORTILECHE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 155, 'name' => 'COLINAGRO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 156, 'name' => 'CAL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 157, 'name' => 'PINTO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 158, 'name' => 'ROSA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 159, 'name' => 'SAN JORGE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 160, 'name' => 'TROPICAL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 161, 'name' => 'BEISBOL NATURAL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 162, 'name' => 'FRUTUROMA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 163, 'name' => 'POLAROMA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 164, 'name' => 'NOSTALGIA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 165, 'name' => 'PUMP', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 166, 'name' => 'WATER WORKS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 167, 'name' => 'BUFFER POWER', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 168, 'name' => 'AMCOR', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 169, 'name' => 'BESTON', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 170, 'name' => 'GIANT', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 171, 'name' => 'ELITE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 172, 'name' => 'ATHOS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 173, 'name' => 'TUSKA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 174, 'name' => 'VISMARCK', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 175, 'name' => 'VIMACH', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 176, 'name' => 'MIO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 177, 'name' => 'MK', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 178, 'name' => 'MC', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 179, 'name' => 'PD', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 180, 'name' => 'JM', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 181, 'name' => 'FPC', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 182, 'name' => 'CHM', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 183, 'name' => 'SOL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 184, 'name' => 'ZEV', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 185, 'name' => 'ALPHA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 186, 'name' => 'SUPERDENT', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 187, 'name' => 'TONING', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],

            // Persona caracterización
            ['id' => 188, 'name' => 'AFROCOLOMBIANO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 189, 'name' => 'AFROCOLOMBIANOS DESPLAZADOS POR LA VIOLENCIA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 190, 'name' => 'AFROCOLOMBIANOS DESPLAZADOS POR LA VIOLENCIA CABEZ', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 191, 'name' => 'INDÍGENA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 192, 'name' => 'INDÍGENAS DESPLAZADOS POR LA VIOLENCIA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 193, 'name' => 'INDÍGENAS DESPLAZADOS POR LA VIOLENCIA CABEZA DE F', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 194, 'name' => 'GITANO ROM', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 195, 'name' => 'PALENQUERO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 196, 'name' => 'RAIZAL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 197, 'name' => 'NEGRO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 198, 'name' => 'MUJER CABEZA DE FAMILIA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 199, 'name' => 'DESPLAZADOS POR LA VIOLENCIA CABEZA DE FAMILIA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 200, 'name' => 'DESPLAZADOS POR FENÓMENOS NATURALES CABEZA DE FAM', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 201, 'name' => 'JÓVENES VULNERABLES', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 202, 'name' => 'ADOLESCENTE TRABAJADOR', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 203, 'name' => 'ADOLESCENTE EN CONFLICTO CON LA LEY PENAL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 204, 'name' => 'PERSONAS EN PROCESO DE REINTEGRACIÓN', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 205, 'name' => 'EMPRENDEDORES', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 206, 'name' => 'MICROEMPRESAS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 207, 'name' => 'ARTESANOS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 208, 'name' => 'CAMPESINO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 209, 'name' => 'SOLDADOS CAMPESINOS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 210, 'name' => 'DISCAPACIDAD INTELECTUAL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 211, 'name' => 'DISCAPACIDAD AUDITIVA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 212, 'name' => 'DISCAPACIDAD FÍSICA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 213, 'name' => 'DISCAPACIDAD VISUAL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 214, 'name' => 'DISCAPACIDAD PSICOSOCIAL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 215, 'name' => 'DISCAPACIDAD MÚLTIPLE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 216, 'name' => 'SORDOCEGUERA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 217, 'name' => 'DESPLAZADOS DISCAPACITADOS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 218, 'name' => 'ABANDONO O DESPOJO FORZADO DE TIERRAS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 219, 'name' => 'ACTOS TERRORISTA ATENTADOS COMBATES ENFRENTAMIENTOS HOSTIGAMIENTOS', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 220, 'name' => 'ADOLESCENTE DESVINCULADO DE GRUPOS ARMADOS ORGANIZ', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 221, 'name' => 'DELITOS CONTRA LA LIBERTAD Y LA INTEGRIDAD SEXUAL EN DESARROLLO DEL CONFLICTO ARMADO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 222, 'name' => 'DESAPARICIÓN FORZADA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 223, 'name' => 'DESPLAZADOS POR LA VIOLENCIA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 224, 'name' => 'RECLUTAMIENTO FORZADO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 225, 'name' => 'SECUESTRO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 226, 'name' => 'HOMICIDIO MASACRE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 227, 'name' => 'HERIDO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 228, 'name' => 'SOBREVIVIENTES MINAS ANTIPERSONALES', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 229, 'name' => 'MINAS ANTIPERSONAL MUNICIÓN SIN EXPLOTAR Y ARTEFACTO EXPLOSIVO IMPROVISADO', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 230, 'name' => 'AMENAZA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 231, 'name' => 'DESPLAZADOS POR FENÓMENOS NATURALES', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 232, 'name' => 'INPEC', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 233, 'name' => 'REMITIDOS POR EL CIE', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 234, 'name' => 'REMITIDOS POR EL PAL', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],
            ['id' => 235, 'name' => 'NINGUNA', 'status' => 1, 'user_create_id' => null, 'user_edit_id' => null],

        ];

        foreach ($parametros as $parametro) {
            Parametro::updateOrCreate(
                ['id' => $parametro['id']],
                [
                    'name' => $parametro['name'],
                    'status' => $parametro['status'],
                    'user_create_id' => $parametro['user_create_id'],
                    'user_edit_id' => $parametro['user_edit_id'],
                ]
            );
        }
    }
}