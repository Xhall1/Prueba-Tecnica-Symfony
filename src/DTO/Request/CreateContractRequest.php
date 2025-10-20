<?php

namespace App\DTO\Request;

class CreateContractRequest
{
    private string $contractNumber;
    private string $contractDate;
    private float $totalValue;
    private string $paymentMethod;
    private int $numberOfInstallments;

    public function __construct(array $data)
    {
        $this->contractNumber = $data['contractNumber'] ?? '';
        $this->contractDate = $data['contractDate'] ?? '';
        $this->totalValue = (float)($data['totalValue'] ?? 0);
        $this->paymentMethod = $data['paymentMethod'] ?? '';
        $this->numberOfInstallments = (int)($data['numberOfInstallments'] ?? 0);
    }

    public function getContractNumber(): string
    {
        return $this->contractNumber;

    }
    public function getContractDate(): string
    {
        return $this->contractDate;
    }
    public function getTotalValue(): float
    {
        return $this->totalValue;
    }
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }
    public function getNumberOfInstallments(): int
    {
        return $this->numberOfInstallments;
    }
}
