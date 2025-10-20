<?php

namespace App\DTO\Response;

class InstallmentResponse
{
    public function __construct(
        private int $installmentNumber,
        private string $amount,
        private string $dueDate,
        private float $interestRate,
        private float $transactionFee
    ) {

    }

    public function toArray(): array
    {
        return [
            'installmentNumber' => $this->installmentNumber,
            'amount' => $this->amount,
            'dueDate' => $this->dueDate,
            'interestRate' => (string)($this->interestRate * 100) . '%',
            'transactionFee' => (string)($this->transactionFee * 100) . '%',
        ];
    }
}
