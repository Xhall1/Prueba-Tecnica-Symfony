<?php

namespace App\Exceptions;

class Contract404Exception extends \Exception
{
    public function __construct(int $id)
    {
        parent::__construct("Contrato con el {$id} no encontrado", 404);
    }
}
