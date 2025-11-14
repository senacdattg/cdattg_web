<?php

namespace App\Exceptions;

use RuntimeException;

class ImportFileNotFoundException extends RuntimeException
{
    public function __construct(string $path)
    {
        parent::__construct("El archivo de importación no existe en la ruta: {$path}");
    }
}

