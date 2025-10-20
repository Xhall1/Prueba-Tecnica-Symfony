<?php

namespace App\Service\Validator;

interface ContractValidatorInterface
{
    public function validate(array $data): array;
}
