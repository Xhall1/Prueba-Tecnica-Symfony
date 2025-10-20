<?php
namespace App\Exceptions;

class InvalidContractException extends \Exception
{
    public function __construct(string $message = 'Contrato invalido', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}

