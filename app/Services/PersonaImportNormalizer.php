<?php

namespace App\Services;

use Illuminate\Support\Str;

class PersonaImportNormalizer
{
    /**
     * Normaliza texto a minúsculas sin acentos
     */
    public static function normalizarTexto(?string $texto): string
    {
        return Str::lower(Str::ascii(trim($texto ?? '')));
    }

    /**
     * Limpia y normaliza número de documento
     */
    public static function limpiarNumeroDocumento(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $limpio = preg_replace('/[^0-9A-Z]/', '', Str::upper(Str::ascii($valor)));

        return $limpio ?: null;
    }

    /**
     * Normaliza y valida email
     */
    public static function normalizarEmail(?string $valor): ?string
    {
        if (!$valor) {
            return null;
        }

        $correo = Str::lower(trim($valor));

        return filter_var($correo, FILTER_VALIDATE_EMAIL) ? $correo : null;
    }

    /**
     * Normaliza teléfono extrayendo solo dígitos
     */
    public static function normalizarTelefono(?string $valor): ?string
    {
        if (!$valor) {
            return null;
        }

        $soloDigitos = preg_replace('/\D/', '', $valor);

        return $soloDigitos ?: null;
    }
}

