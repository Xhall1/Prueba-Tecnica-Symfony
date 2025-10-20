<?php

namespace App\Service\Validator;

use App\Exceptions\InvalidContractException;

class ContractValidator implements ContractValidatorInterface
{
    private const REQUIRED_FIELDS = [
        'contractNumber',
        'contractDate',
        'totalValue',
        'paymentMethod',
        'numberOfInstallments',
    ];

    public function validate(array $data): array
    {
        $errors = [];

        # Check required fields
        foreach (self::REQUIRED_FIELDS as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = "El Campo '{$field}' es requerido";
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        # This if Validate contract number format
        if (!$this->isValidContractNumber($data['contractNumber'])) {
            $errors['contractNumber'] = 'Formato de número de contrato no válido';
        }

        # This if Validate date format
        if (!$this->isValidDateFormat($data['contractDate'])) {
            $errors['contractDate'] = 'Formato de fecha no válido (se esperaba Y-m-d)';
        }

        # This if Validate total value
        if (!is_numeric($data['totalValue']) || $data['totalValue'] <= 0) {
            $errors['totalValue'] = 'El valor total debe ser un número positivor';
        }

        # This if Validate number of installments
        if (!is_numeric($data['numberOfInstallments']) || $data['numberOfInstallments'] < 1) {
            $errors['numberOfInstallments'] = 'El número de cuotas debe ser al menos 1';
        }

        return $errors;
    }

    private function isValidContractNumber(string $contractNumber): bool
    {
        return !empty($contractNumber) && strlen($contractNumber) <= 50;
    }

    private function isValidDateFormat(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
