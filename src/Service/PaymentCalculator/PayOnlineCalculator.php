<?php

namespace App\Service\PaymentCalculator;

class PayOnlineCalculator implements PaymentCalculatorInterface
{
    private const INTEREST_RATE = 0.02;
    private const TRANSACTION_FEE = 0.01;

    public function getInterestRate(): float
    {
        return self::INTEREST_RATE;
    }

    public function getTransactionFee(): float
    {
        return self::TRANSACTION_FEE;
    }

    public function calculateInstallmentAmount(float $baseAmount): float
    {
        $interest = $baseAmount * $this->getInterestRate();
        $fee = $baseAmount * $this->getTransactionFee();
        return $baseAmount + $interest + $fee;
    }
}
