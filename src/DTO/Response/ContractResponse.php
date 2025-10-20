<?php

namespace App\DTO\Response;

class ContractResponse
{
    public function __construct(
        private int $id,
        private string $contractNumber,
        private string $contractDate,
        private float $totalValue,
        private string $paymentMethod,
        private int $numberOfInstallments,
        private array $installments = []
    ) {

    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'contractNumber' => $this->contractNumber,
            'contractDate' => $this->contractDate,
            'totalValue' => $this->totalValue,
            'paymentMethod' => $this->paymentMethod,
            'numberOfInstallments' => $this->numberOfInstallments,
            'installments' => $this->installments,
        ];
    }
}
