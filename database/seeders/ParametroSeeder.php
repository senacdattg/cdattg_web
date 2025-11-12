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
        return array_merge(
            $this->estados(),
            $this->tiposDocumento(),
            $this->generos(),
            $this->dias(),
            $this->modalidades(),
            $this->nivelesFormacion(),
            $this->estadosEvidencias(),
            $this->productos(),
            $this->unidadesMedida(),
            $this->ordenesYAprobaciones(),
            $this->categorias(),
            $this->marcas(),
            $this->personaCaracterizacion(),
            $this->vias(),
            $this->letras()
        );
    }

    private function estados(): array
    {
        return [
            $this->parametro(1, 'ACTIVO'),
            $this->parametro(2, 'INACTIVO'),
        ];
    }

    private function tiposDocumento(): array
    {
        return [
            $this->parametro(3, 'CEDULA DE CIUDADANIA'),
            $this->parametro(4, 'CEDULA DE EXTRANJERIA'),
            $this->parametro(5, 'PASAPORTE'),
            $this->parametro(6, 'TARJETA DE IDENTIDAD'),
            $this->parametro(7, 'REGISTRO CIVIL'),
            $this->parametro(8, 'SIN IDENTIFICACION'),
        ];
    }

    private function generos(): array
    {
        return [
            $this->parametro(9, 'MASCULINO'),
            $this->parametro(10, 'FEMENINO'),
            $this->parametro(11, 'NO DEFINE'),
        ];
    }

    private function dias(): array
    {
        return [
            $this->parametro(12, 'LUNES'),
            $this->parametro(13, 'MARTES'),
            $this->parametro(14, 'MIERCOLES'),
            $this->parametro(15, 'JUEVES'),
            $this->parametro(16, 'VIERNES'),
            $this->parametro(17, 'SABADO'),
        ];
    }

    private function modalidades(): array
    {
        return [
            $this->parametro(18, 'PRESENCIAL'),
            $this->parametro(19, 'VIRTUAL'),
            $this->parametro(20, 'MIXTA'),
        ];
    }

    private function nivelesFormacion(): array
    {
        return [
            $this->parametro(21, 'TÉCNICO'),
            $this->parametro(22, 'TECNÓLOGO'),
            $this->parametro(23, 'AUXILIAR'),
            $this->parametro(24, 'OPERARIO'),
        ];
    }

    private function estadosEvidencias(): array
    {
        return [
            $this->parametro(25, 'PENDIENTE'),
            $this->parametro(26, 'EN CURSO'),
            $this->parametro(27, 'COMPLETADO'),
        ];
    }

    private function unidadesMedida(): array
    {
        return [
            $this->parametro(30, 'GRAMOS'),
            $this->parametro(31, 'LIBRAS'),
            $this->parametro(32, 'KILOGRAMOS'),
            $this->parametro(33, 'ARROBA'),
            $this->parametro(34, 'QUINTAL'),
            $this->parametro(35, 'ONZA'),
            $this->parametro(36, 'MILILITROS'),
            $this->parametro(37, 'LITROS'),
            $this->parametro(38, 'GALONES'),
            $this->parametro(39, 'ONZA LÍQUIDA'),
            $this->parametro(40, 'BARRIL'),
            $this->parametro(41, 'UNIDADES'),
            $this->parametro(42, 'CAJAS'),
            $this->parametro(43, 'METROS'),
            $this->parametro(44, 'CENTIMETROS'),
            $this->parametro(45, 'PAQUETES'),
            $this->parametro(46, 'ROLLOS'),
            $this->parametro(47, 'TABLETAS'),
            $this->parametro(48, 'TEST'),
            $this->parametro(49, 'SACKETS'),
        ];
    }

    private function productos(): array
    {
        return [
            $this->parametro(28, 'CONSUMIBLE'),
            $this->parametro(29, 'NO CONSUMIBLE'),
            $this->parametro(50, 'DISPONIBLE'),
            $this->parametro(51, 'AGOTADO'),
        ];
    }

    private function ordenesYAprobaciones(): array
    {
        return [
            $this->parametro(52, 'PRÉSTAMO'),
            $this->parametro(53, 'SALIDA'),
            $this->parametro(54, 'EN ESPERA'),
            $this->parametro(55, 'APROBADA'),
            $this->parametro(56, 'RECHAZADA'),
            $this->parametro(57, 'ENTREGADA'),
            $this->parametro(58, 'EN PRÉSTAMO'),
        ];
    }

    private function categorias(): array
    {
        return [
            $this->parametro(59, 'EMPAQUE'),
            $this->parametro(60, 'EPP'),
            $this->parametro(61, 'EQUIPO'),
            $this->parametro(62, 'FUNGIBLE'),
            $this->parametro(63, 'INSTRUMENTO MEDICION'),
            $this->parametro(64, 'INSUMO'),
            $this->parametro(65, 'REACTIVO'),
            $this->parametro(66, 'UTENSILIO'),
            $this->parametro(67, 'VIDRIERIA'),
        ];
    }

    private function marcas(): array
    {
        return [
            $this->parametro(68, 'LAMOTTE'),
            $this->parametro(69, 'PYREX'),
            $this->parametro(70, 'MERCK'),
            $this->parametro(71, 'KIMAX'),
            $this->parametro(72, 'HACH'),
            $this->parametro(73, 'MILLIPORE'),
            $this->parametro(74, 'BOECO'),
            $this->parametro(75, 'BRAND'),
            $this->parametro(76, 'DURAN'),
            $this->parametro(77, 'PANREAC'),
            $this->parametro(78, 'BRIXCO'),
            $this->parametro(79, 'CLOROX'),
            $this->parametro(80, 'GOLDENWRAP'),
            $this->parametro(81, 'ARO'),
            $this->parametro(82, 'MILWAUKEE'),
            $this->parametro(83, 'HANNA'),
            $this->parametro(84, 'ALPHA CHEMIKA'),
            $this->parametro(85, 'SUPELCO'),
            $this->parametro(86, 'CITOTEST'),
            $this->parametro(87, 'BIOPOINTE SCIENTIFIC'),
            $this->parametro(88, 'CITOGLAS'),
            $this->parametro(89, 'GLASSCO'),
            $this->parametro(90, 'LABSCIENT'),
            $this->parametro(91, 'KIMBLE'),
            $this->parametro(92, 'NADIR'),
            $this->parametro(93, 'VIDRIOLAB'),
            $this->parametro(94, 'GOTOPLAS'),
            $this->parametro(95, 'PLASTIRED'),
            $this->parametro(96, 'PLASTICOS R&M'),
            $this->parametro(97, 'SCOTCH-BRITE'),
            $this->parametro(98, 'TRAMONTINA'),
            $this->parametro(99, 'CORONA'),
            $this->parametro(100, 'IMUSA'),
            $this->parametro(101, 'FARBERWARE'),
            $this->parametro(102, 'JGB'),
            $this->parametro(103, 'BAXTER'),
            $this->parametro(104, 'B.D'),
            $this->parametro(105, 'BAYER'),
            $this->parametro(106, 'BIOPONTERCIENTIFIC'),
            $this->parametro(107, 'NANOCOLOR'),
            $this->parametro(108, 'WTW'),
            $this->parametro(109, 'MACHEREY NAGEL'),
            $this->parametro(110, 'PHYTOTECH'),
            $this->parametro(111, 'GE HEALTHCARE'),
            $this->parametro(112, 'MEDISPO'),
            $this->parametro(113, 'MIDMARK'),
            $this->parametro(114, 'VITAL MEDIC'),
            $this->parametro(115, 'MINE MEDICAL'),
            $this->parametro(116, 'ZAFIRO'),
            $this->parametro(117, 'CHEMI'),
            $this->parametro(118, 'AZUCAR INCAUCA'),
            $this->parametro(119, 'MAIZENA'),
            $this->parametro(120, 'COLOMBINA-ZEV'),
            $this->parametro(121, 'LUKER'),
            $this->parametro(122, 'SANTO DOMINGO'),
            $this->parametro(123, 'BETTY CROCKER'),
            $this->parametro(124, 'GERBER'),
            $this->parametro(125, 'BLANCOX'),
            $this->parametro(126, 'BRILLO AROMA'),
            $this->parametro(127, 'BRILLAKING'),
            $this->parametro(128, 'ASEPSIA'),
            $this->parametro(129, 'SOLOASEO'),
            $this->parametro(130, 'MAXWIPE'),
            $this->parametro(131, 'EXAMTEX'),
            $this->parametro(132, 'PROTEXION'),
            $this->parametro(133, 'BODI SAFE'),
            $this->parametro(134, 'CRISTAR'),
            $this->parametro(135, 'DIMEDA'),
            $this->parametro(136, 'DROFARMA'),
            $this->parametro(137, 'INVERFARMA'),
            $this->parametro(138, 'BIOLOGIKA'),
            $this->parametro(139, 'BIOHALL'),
            $this->parametro(140, 'ABCLABORATORIOS'),
            $this->parametro(141, 'CIACOMEQ S.A.S'),
            $this->parametro(142, 'LEGAQUIMICOS'),
            $this->parametro(143, 'QUINSA'),
            $this->parametro(144, 'QUIMPO'),
            $this->parametro(145, 'MOL LABS'),
            $this->parametro(146, 'METALLURGICA MOTTA'),
            $this->parametro(147, 'MOTTA'),
            $this->parametro(148, 'LEON'),
            $this->parametro(149, 'ALGARRA'),
            $this->parametro(150, 'HOPEX'),
            $this->parametro(151, 'KRAMER'),
            $this->parametro(152, 'MP TOOLS'),
            $this->parametro(153, 'PISCICLORO'),
            $this->parametro(154, 'FORTILECHE'),
            $this->parametro(155, 'COLINAGRO'),
            $this->parametro(156, 'CAL'),
            $this->parametro(157, 'PINTO'),
            $this->parametro(158, 'ROSA'),
            $this->parametro(159, 'SAN JORGE'),
            $this->parametro(160, 'TROPICAL'),
            $this->parametro(161, 'BEISBOL NATURAL'),
            $this->parametro(162, 'FRUTUROMA'),
            $this->parametro(163, 'POLAROMA'),
            $this->parametro(164, 'NOSTALGIA'),
            $this->parametro(165, 'PUMP'),
            $this->parametro(166, 'WATER WORKS'),
            $this->parametro(167, 'BUFFER POWER'),
            $this->parametro(168, 'AMCOR'),
            $this->parametro(169, 'BESTON'),
            $this->parametro(170, 'GIANT'),
            $this->parametro(171, 'ELITE'),
            $this->parametro(172, 'ATHOS'),
            $this->parametro(173, 'TUSKA'),
            $this->parametro(174, 'VISMARCK'),
            $this->parametro(175, 'VIMACH'),
            $this->parametro(176, 'MIO'),
            $this->parametro(177, 'MK'),
            $this->parametro(178, 'MC'),
            $this->parametro(179, 'PD'),
            $this->parametro(180, 'JM'),
            $this->parametro(181, 'FPC'),
            $this->parametro(182, 'CHM'),
            $this->parametro(183, 'SOL'),
            $this->parametro(184, 'ZEV'),
            $this->parametro(185, 'ALPHA'),
            $this->parametro(186, 'SUPERDENT'),
            $this->parametro(187, 'TONING'),
        ];
    }

    private function personaCaracterizacion(): array
    {
        return [
            $this->parametro(188, 'AFROCOLOMBIANO'),
            $this->parametro(189, 'AFROCOLOMBIANOS DESPLAZADOS POR LA VIOLENCIA'),
            $this->parametro(
                190,
                'AFROCOLOMBIANOS DESPLAZADOS POR LA VIOLENCIA CABEZ'
            ),
            $this->parametro(191, 'INDÍGENA'),
            $this->parametro(192, 'INDÍGENAS DESPLAZADOS POR LA VIOLENCIA'),
            $this->parametro(
                193,
                'INDÍGENAS DESPLAZADOS POR LA VIOLENCIA CABEZA DE F'
            ),
            $this->parametro(194, 'GITANO ROM'),
            $this->parametro(195, 'PALENQUERO'),
            $this->parametro(196, 'RAIZAL'),
            $this->parametro(197, 'NEGRO'),
            $this->parametro(198, 'MUJER CABEZA DE FAMILIA'),
            $this->parametro(
                199,
                'DESPLAZADOS POR LA VIOLENCIA CABEZA DE FAMILIA'
            ),
            $this->parametro(
                200,
                'DESPLAZADOS POR FENÓMENOS NATURALES CABEZA DE FAM'
            ),
            $this->parametro(201, 'JÓVENES VULNERABLES'),
            $this->parametro(202, 'ADOLESCENTE TRABAJADOR'),
            $this->parametro(
                203,
                'ADOLESCENTE EN CONFLICTO CON LA LEY PENAL'
            ),
            $this->parametro(
                204,
                'PERSONAS EN PROCESO DE REINTEGRACIÓN'
            ),
            $this->parametro(205, 'EMPRENDEDORES'),
            $this->parametro(206, 'MICROEMPRESAS'),
            $this->parametro(207, 'ARTESANOS'),
            $this->parametro(208, 'CAMPESINO'),
            $this->parametro(209, 'SOLDADOS CAMPESINOS'),
            $this->parametro(210, 'DISCAPACIDAD INTELECTUAL'),
            $this->parametro(211, 'DISCAPACIDAD AUDITIVA'),
            $this->parametro(212, 'DISCAPACIDAD FÍSICA'),
            $this->parametro(213, 'DISCAPACIDAD VISUAL'),
            $this->parametro(214, 'DISCAPACIDAD PSICOSOCIAL'),
            $this->parametro(215, 'DISCAPACIDAD MÚLTIPLE'),
            $this->parametro(216, 'SORDOCEGUERA'),
            $this->parametro(217, 'DESPLAZADOS DISCAPACITADOS'),
            $this->parametro(
                218,
                'ABANDONO O DESPOJO FORZADO DE TIERRAS'
            ),
            $this->parametro(
                219,
                'ACTOS TERRORISTA ATENTADOS COMBATES ENFRENTAMIENTOS '
                    . 'HOSTIGAMIENTOS'
            ),
            $this->parametro(
                220,
                'ADOLESCENTE DESVINCULADO DE GRUPOS ARMADOS ORGANIZ'
            ),
            $this->parametro(
                221,
                'DELITOS CONTRA LA LIBERTAD Y LA INTEGRIDAD SEXUAL EN '
                    . 'DESARROLLO DEL CONFLICTO ARMADO'
            ),
            $this->parametro(222, 'DESAPARICIÓN FORZADA'),
            $this->parametro(223, 'DESPLAZADOS POR LA VIOLENCIA'),
            $this->parametro(224, 'RECLUTAMIENTO FORZADO'),
            $this->parametro(225, 'SECUESTRO'),
            $this->parametro(226, 'HOMICIDIO MASACRE'),
            $this->parametro(227, 'HERIDO'),
            $this->parametro(
                228,
                'SOBREVIVIENTES MINAS ANTIPERSONALES'
            ),
            $this->parametro(
                229,
                'MINAS ANTIPERSONAL MUNICIÓN SIN EXPLOTAR Y ARTEFACTO '
                    . 'EXPLOSIVO IMPROVISADO'
            ),
            $this->parametro(230, 'AMENAZA'),
            $this->parametro(
                231,
                'DESPLAZADOS POR FENÓMENOS NATURALES'
            ),
            $this->parametro(232, 'INPEC'),
            $this->parametro(233, 'REMITIDOS POR EL CIE'),
            $this->parametro(234, 'REMITIDOS POR EL PAL'),
            $this->parametro(235, 'NINGUNA'),
        ];
    }

    private function vias(): array
    {
        return [
            $this->parametro(236, 'CARRERA'),
            $this->parametro(237, 'CALLE'),
            $this->parametro(238, 'TRANSVERSAL'),
            $this->parametro(239, 'DIAGONAL'),
            $this->parametro(240, 'AVENIDA'),
            $this->parametro(241, 'AUTOPISTA'),
            $this->parametro(242, 'CIRCULAR'),
            $this->parametro(243, 'VÍA'),
            $this->parametro(244, 'PASAJE'),
            $this->parametro(245, 'MANZANA'),
        ];
    }

    private function letras(): array
    {
        return [
            $this->parametro(246, 'A'),
            $this->parametro(247, 'B'),
            $this->parametro(248, 'C'),
            $this->parametro(249, 'D'),
            $this->parametro(250, 'E'),
            $this->parametro(251, 'F'),
            $this->parametro(252, 'G'),
            $this->parametro(253, 'H'),
            $this->parametro(254, 'I'),
            $this->parametro(255, 'J'),
            $this->parametro(256, 'K'),
            $this->parametro(257, 'L'),
            $this->parametro(258, 'M'),
            $this->parametro(259, 'N'),
            $this->parametro(260, 'O'),
            $this->parametro(261, 'P'),
            $this->parametro(262, 'Q'),
            $this->parametro(263, 'R'),
            $this->parametro(264, 'S'),
            $this->parametro(265, 'T'),
            $this->parametro(266, 'U'),
            $this->parametro(267, 'V'),
            $this->parametro(268, 'W'),
            $this->parametro(269, 'X'),
            $this->parametro(270, 'Y'),
            $this->parametro(271, 'Z'),
        ];
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
