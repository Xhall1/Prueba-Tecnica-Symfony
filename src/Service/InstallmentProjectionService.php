<?php

namespace App\Service;

use App\DTO\Response\InstallmentResponse;
use App\Entity\Contract;
use App\Service\PaymentCalculator\PaymentCalculator;

class InstallmentProjectionService
{
    public function generateProjection(
        Contract $contract,
        int $months = null
    ): array
    {
        $months = $months ?? $contract->getNumberOfInstallments();
        $calculator = PaymentCalculator::create($contract->getPaymentMethod());

        $baseAmount = $contract->getTotalValue() / $months;
        $projection = [];

        for ($i = 1; $i <= $months; $i++) {
            $installmentAmount = $calculator->calculateInstallmentAmount($baseAmount);
            $dueDate = $contract->getContractDate()->modify("+{$i} month");

            $projection[] = new InstallmentResponse(
                $i,
                (string)number_format($installmentAmount, 2, '.', ''),
                $dueDate->format('Y-m-d'),
                $calculator->getInterestRate(),
                $calculator->getTransactionFee()
            );
        }

        return $projection;
    }

    public function generateInstallments(
        Contract $contract
    ): void
    {
        $calculator = PaymentCalculator::create($contract->getPaymentMethod());
        $baseAmount = $contract->getTotalValue() / $contract->getNumberOfInstallments();

        for ($i = 1; $i <= $contract->getNumberOfInstallments(); $i++) {
            $installmentAmount = $calculator->calculateInstallmentAmount($baseAmount);
            $dueDate = $contract->getContractDate()->modify("+{$i} month");

            $installment = new \App\Entity\Installment();
            $installment->setContract($contract);
            $installment->setInstallmentNumber($i);
            $installment->setAmount((string)number_format($installmentAmount, 2, '.', ''));
            $installment->setDueDate($dueDate);
            $installment->setInterestRate((string)number_format($calculator->getInterestRate(), 4, '.', ''));
            $installment->setTransactionFee((string)number_format($calculator->getTransactionFee(), 4, '.', ''));

            $contract->addInstallment($installment);
        }
    }
}
