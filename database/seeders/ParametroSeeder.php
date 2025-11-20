<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parametro; // Asegúrate de usar la notación correcta
use Database\Seeders\Concerns\TruncatesTables;

class ParametroSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Catálogos de parámetros base organizados por temática.
     *
     * Mantener las colecciones en constantes reduce la cantidad de métodos en la clase
     * y facilita su reutilización en seeders especializados.
     */
    private const CATALOGOS = [
        'estados' => [
            1 => 'ACTIVO',
            2 => 'INACTIVO',
        ],
        'tipos_documento' => [
            3 => 'CÉDULA DE CIUDADANÍA',
            4 => 'CÉDULA DE EXTRANJERÍA',
            5 => 'PASAPORTE',
            6 => 'TARJETA DE IDENTIDAD',
            7 => 'REGISTRO CIVIL',
            8 => 'SIN IDENTIFICACIÓN',
        ],
        'generos' => [
            9 => 'MASCULINO',
            10 => 'FEMENINO',
            11 => 'NO DEFINE',
        ],
        'dias' => [
            12 => 'LUNES',
            13 => 'MARTES',
            14 => 'MIERCOLES',
            15 => 'JUEVES',
            16 => 'VIERNES',
            17 => 'SABADO',
        ],
        'modalidades' => [
            18 => 'PRESENCIAL',
            19 => 'VIRTUAL',
            20 => 'MIXTA',
        ],
        'niveles_formacion' => [
            21 => 'TÉCNICO',
            22 => 'TECNÓLOGO',
            23 => 'AUXILIAR',
            24 => 'OPERARIO',
        ],
        'estados_evidencias' => [
            25 => 'PENDIENTE',
            26 => 'EN CURSO',
            27 => 'COMPLETADO',
        ],
        'productos' => [
            28 => 'CONSUMIBLE',
            29 => 'NO CONSUMIBLE',
            50 => 'DISPONIBLE',
            51 => 'AGOTADO',
        ],
        'unidades_medida' => [
            30 => 'GRAMOS',
            31 => 'LIBRAS',
            32 => 'KILOGRAMOS',
            33 => 'ARROBA',
            34 => 'QUINTAL',
            35 => 'ONZA',
            36 => 'MILILITROS',
            37 => 'LITROS',
            38 => 'GALONES',
            39 => 'ONZA LÍQUIDA',
            40 => 'BARRIL',
            41 => 'UNIDADES',
            42 => 'CAJAS',
            43 => 'METROS',
            44 => 'CENTIMETROS',
            45 => 'PAQUETES',
            46 => 'ROLLOS',
            47 => 'TABLETAS',
            48 => 'TEST',
            49 => 'SACKETS',
        ],
        'ordenes_aprobaciones' => [
            52 => 'PRÉSTAMO',
            53 => 'SALIDA',
            54 => 'EN ESPERA',
            55 => 'APROBADA',
            56 => 'RECHAZADA',
            57 => 'ENTREGADA',
            58 => 'EN PRÉSTAMO',
        ],
        'categorias' => [
            59 => 'EMPAQUE',
            60 => 'EPP',
            61 => 'EQUIPO',
            62 => 'FUNGIBLE',
            63 => 'INSTRUMENTO MEDICION',
            64 => 'INSUMO',
            65 => 'REACTIVO',
            66 => 'UTENSILIO',
            67 => 'VIDRIERIA',
        ],
        'marcas' => [
            68 => 'LAMOTTE',
            69 => 'PYREX',
            70 => 'MERCK',
            71 => 'KIMAX',
            72 => 'HACH',
            73 => 'MILLIPORE',
            74 => 'BOECO',
            75 => 'BRAND',
            76 => 'DURAN',
            77 => 'PANREAC',
            78 => 'BRIXCO',
            79 => 'CLOROX',
            80 => 'GOLDENWRAP',
            81 => 'ARO',
            82 => 'MILWAUKEE',
            83 => 'HANNA',
            84 => 'ALPHA CHEMIKA',
            85 => 'SUPELCO',
            86 => 'CITOTEST',
            87 => 'BIOPOINTE SCIENTIFIC',
            88 => 'CITOGLAS',
            89 => 'GLASSCO',
            90 => 'LABSCIENT',
            91 => 'KIMBLE',
            92 => 'NADIR',
            93 => 'VIDRIOLAB',
            94 => 'GOTOPLAS',
            95 => 'PLASTIRED',
            96 => 'PLASTICOS R&M',
            97 => 'SCOTCH-BRITE',
            98 => 'TRAMONTINA',
            99 => 'CORONA',
            100 => 'IMUSA',
            101 => 'FARBERWARE',
            102 => 'JGB',
            103 => 'BAXTER',
            104 => 'B.D',
            105 => 'BAYER',
            106 => 'BIOPONTERCIENTIFIC',
            107 => 'NANOCOLOR',
            108 => 'WTW',
            109 => 'MACHEREY NAGEL',
            110 => 'PHYTOTECH',
            111 => 'GE HEALTHCARE',
            112 => 'MEDISPO',
            113 => 'MIDMARK',
            114 => 'VITAL MEDIC',
            115 => 'MINE MEDICAL',
            116 => 'ZAFIRO',
            117 => 'CHEMI',
            118 => 'AZUCAR INCAUCA',
            119 => 'MAIZENA',
            120 => 'COLOMBINA-ZEV',
            121 => 'LUKER',
            122 => 'SANTO DOMINGO',
            123 => 'BETTY CROCKER',
            124 => 'GERBER',
            125 => 'BLANCOX',
            126 => 'BRILLO AROMA',
            127 => 'BRILLAKING',
            128 => 'ASEPSIA',
            129 => 'SOLOASEO',
            130 => 'MAXWIPE',
            131 => 'EXAMTEX',
            132 => 'PROTEXION',
            133 => 'BODI SAFE',
            134 => 'CRISTAR',
            135 => 'DIMEDA',
            136 => 'DROFARMA',
            137 => 'INVERFARMA',
            138 => 'BIOLOGIKA',
            139 => 'BIOHALL',
            140 => 'ABCLABORATORIOS',
            141 => 'CIACOMEQ S.A.S',
            142 => 'LEGAQUIMICOS',
            143 => 'QUINSA',
            144 => 'QUIMPO',
            145 => 'MOL LABS',
            146 => 'METALLURGICA MOTTA',
            147 => 'MOTTA',
            148 => 'LEON',
            149 => 'ALGARRA',
            150 => 'HOPEX',
            151 => 'KRAMER',
            152 => 'MP TOOLS',
            153 => 'PISCICLORO',
            154 => 'FORTILECHE',
            155 => 'COLINAGRO',
            156 => 'CAL',
            157 => 'PINTO',
            158 => 'ROSA',
            159 => 'SAN JORGE',
            160 => 'TROPICAL',
            161 => 'BEISBOL NATURAL',
            162 => 'FRUTUROMA',
            163 => 'POLAROMA',
            164 => 'NOSTALGIA',
            165 => 'PUMP',
            166 => 'WATER WORKS',
            167 => 'BUFFER POWER',
            168 => 'AMCOR',
            169 => 'BESTON',
            170 => 'GIANT',
            171 => 'ELITE',
            172 => 'ATHOS',
            173 => 'TUSKA',
            174 => 'VISMARCK',
            175 => 'VIMACH',
            176 => 'MIO',
            177 => 'MK',
            178 => 'MC',
            179 => 'PD',
            180 => 'JM',
            181 => 'FPC',
            182 => 'CHM',
            183 => 'SOL',
            184 => 'ZEV',
            185 => 'ALPHA',
            186 => 'SUPERDENT',
            187 => 'TONING',
        ],
        'persona_caracterizacion' => [
            188 => 'AFROCOLOMBIANO',
            189 => 'AFROCOLOMBIANOS DESPLAZADOS POR LA VIOLENCIA',
            190 => 'AFROCOLOMBIANOS DESPLAZADOS POR LA VIOLENCIA CABEZ',
            191 => 'INDÍGENA',
            192 => 'INDÍGENAS DESPLAZADOS POR LA VIOLENCIA',
            193 => 'INDÍGENAS DESPLAZADOS POR LA VIOLENCIA CABEZA DE F',
            194 => 'GITANO ROM',
            195 => 'PALENQUERO',
            196 => 'RAIZAL',
            197 => 'NEGRO',
            198 => 'MUJER CABEZA DE FAMILIA',
            199 => 'DESPLAZADOS POR LA VIOLENCIA CABEZA DE FAMILIA',
            200 => 'DESPLAZADOS POR FENÓMENOS NATURALES CABEZA DE FAM',
            201 => 'JÓVENES VULNERABLES',
            202 => 'ADOLESCENTE TRABAJADOR',
            203 => 'ADOLESCENTE EN CONFLICTO CON LA LEY PENAL',
            204 => 'PERSONAS EN PROCESO DE REINTEGRACIÓN',
            205 => 'EMPRENDEDORES',
            206 => 'MICROEMPRESAS',
            207 => 'ARTESANOS',
            208 => 'CAMPESINO',
            209 => 'SOLDADOS CAMPESINOS',
            210 => 'DISCAPACIDAD INTELECTUAL',
            211 => 'DISCAPACIDAD AUDITIVA',
            212 => 'DISCAPACIDAD FÍSICA',
            213 => 'DISCAPACIDAD VISUAL',
            214 => 'DISCAPACIDAD PSICOSOCIAL',
            215 => 'DISCAPACIDAD MÚLTIPLE',
            216 => 'SORDOCEGUERA',
            217 => 'DESPLAZADOS DISCAPACITADOS',
            218 => 'ABANDONO O DESPOJO FORZADO DE TIERRAS',
            219 => 'ACTOS TERRORISTA ATENTADOS COMBATES ENFRENTAMIENTOS HOSTIGAMIENTOS',
            220 => 'ADOLESCENTE DESVINCULADO DE GRUPOS ARMADOS ORGANIZ',
            221 => 'DELITOS CONTRA LA LIBERTAD Y LA INTEGRIDAD SEXUAL EN DESARROLLO DEL CONFLICTO ARMADO',
            222 => 'DESAPARICIÓN FORZADA',
            223 => 'DESPLAZADOS POR LA VIOLENCIA',
            224 => 'RECLUTAMIENTO FORZADO',
            225 => 'SECUESTRO',
            226 => 'HOMICIDIO MASACRE',
            227 => 'HERIDO',
            228 => 'SOBREVIVIENTES MINAS ANTIPERSONALES',
            229 => 'MINAS ANTIPERSONAL MUNICIÓN SIN EXPLOTAR Y ARTEFACTO EXPLOSIVO IMPROVISADO',
            230 => 'AMENAZA',
            231 => 'DESPLAZADOS POR FENÓMENOS NATURALES',
            232 => 'INPEC',
            233 => 'REMITIDOS POR EL CIE',
            234 => 'REMITIDOS POR EL PAL',
            235 => 'NINGUNA',
        ],
        'vias' => [
            236 => 'CARRERA',
            237 => 'CALLE',
            238 => 'TRANSVERSAL',
            239 => 'DIAGONAL',
            240 => 'AVENIDA',
            241 => 'AUTOPISTA',
            242 => 'CIRCULAR',
            243 => 'VÍA',
            244 => 'PASAJE',
            245 => 'MANZANA',
            246 => 'RUTA',
            247 => 'KM',
        ],
        'letras' => [
            248 => 'A',
            249 => 'B',
            250 => 'C',
            251 => 'D',
            252 => 'E',
            253 => 'F',
            254 => 'G',
            255 => 'H',
            256 => 'I',
            257 => 'J',
            258 => 'K',
            259 => 'L',
            260 => 'M',
            261 => 'N',
            262 => 'O',
            263 => 'P',
            264 => 'Q',
            265 => 'R',
            266 => 'S',
            267 => 'T',
            268 => 'U',
            269 => 'V',
            270 => 'W',
            271 => 'X',
            272 => 'Y',
            273 => 'Z',
        ],
        'documentos_extranjeros' => [
            274 => 'PERMISO POR PROTECCIÓN ESPECIAL',
            275 => 'PERMISO POR PROTECCION TEMPORAL',
        ],

        'dias_faltantes' => [
            276 => 'DOMINGO',
        ],

        'jornadas' => [
            277 => 'MAÑANA',
            278 => 'TARDE',
            279 => 'NOCHE',
            280 => 'FINES DE SEMANA',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->resetTable();

        foreach ($this->parametrosConfig() as $parametro) {
            $this->createParametro($parametro);
        }
    }

    private function resetTable(): void
    {
        if (app()->environment('production')) {
            // Evita truncados en producción; el seeder opera de forma idempotente.
            return;
        }

        $this->truncateModel(Parametro::class);
    }

    /**
     * Devuelve la configuración estática de parámetros.
     *
     * Centralizar la definición facilita mantenibilidad y evita duplicidad.
     */
    private function parametrosConfig(): array
    {
        $parametros = [];

        foreach (self::CATALOGOS as $catalogo) {
            foreach ($catalogo as $id => $name) {
                $parametros[] = $this->parametro($id, $name);
            }
        }

        return $parametros;
    }

    private function createParametro(array $parametro): void
    {
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

    private function parametro(int $id, string $name): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'status' => 1,
            'user_create_id' => null,
            'user_edit_id' => null,
        ];
    }
}
