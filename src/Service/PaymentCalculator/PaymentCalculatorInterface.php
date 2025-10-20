<?php
namespace App\Service\PaymentCalculator;

interface PaymentCalculatorInterface
{
    public function getInterestRate(): float;
    public function getTransactionFee(): float;
    public function calculateInstallmentAmount(float $baseAmount): float;
}
