<?php

namespace App\Exceptions;

class InvalidPaymentException extends \Exception
{
    private array $validMethods;

    public function __construct(string $method, array $validMethods)
    {
        $this->validMethods = $validMethods;
        $message = sprintf(
            "Metodo de pago no valido '%s'. Metodos válidos: %s",
            $method,
            implode(', ', $validMethods)
        );
        parent::__construct($message, 400);
    }

    public function getValidMethods(): array
    {
        return $this->validMethods;
    }
}

