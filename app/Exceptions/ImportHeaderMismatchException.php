<?php

namespace App\Exceptions;

use RuntimeException;

class ImportHeaderMismatchException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('El encabezado del archivo no coincide con el formato esperado.');
    }
}

