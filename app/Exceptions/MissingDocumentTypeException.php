<?php

namespace App\Exceptions;

use RuntimeException;

class MissingDocumentTypeException extends RuntimeException
{
    public function __construct(?string $valorOriginal = null)
    {
        $message = 'No se puede crear una persona sin tipo de documento válido';
        if ($valorOriginal) {
            $message .= ". Valor recibido: '{$valorOriginal}'";
        }
        parent::__construct($message);
    }
}

