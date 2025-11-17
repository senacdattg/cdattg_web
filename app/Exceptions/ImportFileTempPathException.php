<?php

namespace App\Exceptions;

use RuntimeException;

class ImportFileTempPathException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('No se pudo obtener la ruta temporal del archivo de importación.');
    }
}

