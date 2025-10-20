<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Entity\Installment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ContractController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #GET all contracts
    #[Route('/api/v1/contract', methods: ['GET'])]
    public function getAllContracts(): JsonResponse
    {

        $data = $this->em->getRepository(Contract::class)->findBy([], [
            'id' => 'DESC'
        ]);

        if (!$data) {
            return $this->json([
                'state' => 'error',
                'message' => 'No hay información'
            ], 400);
        }

        return $this->json($data);
    }

    #GET contracts by ID
    #[Route('/api/v1/contract/{id}', methods: ['GET'])]
    public function getAllContractsById(int $id): JsonResponse
    {

        $data = $this->em->getRepository(Contract::class)->find($id);

        if (!$data) {
            return $this->json([
                'state' => 'error',
                'message' => 'No se encontró el contrato'
            ], 400);
        }

        return $this->json($data);
    }

# POST create contract
    #[Route('/api/v1/contract', methods: ['POST'])]
    public function createContract(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        # Basic validation
        if (!isset($data['contractNumber'], $data['contractDate'], $data['totalValue'],
            $data['paymentMethod'], $data['numberOfInstallments'])) {
            return $this->json([
                'state' => 'error',
                'message' => 'Campos requeridos faltantes'
            ], 400);
        }

        # Validate payment method
        if (!in_array($data['paymentMethod'], ['paypal', 'payonline'])) {
            return $this->json([
                'state' => 'error',
                'message' => 'Método de pago no válido'
            ], 400);
        }

        try {

            $today = new \DateTimeImmutable();

            # Contract creation
            $contract = new Contract();

            # TODAY DAY
            $contract->setCreatedAt($today);
            $contract->setUpdatedAt($today);

            # FIElDS
            $contract->setContractNumber($data['contractNumber']);
            $contract->setContractDate(new \DateTimeImmutable($data['contractDate']));
            $contract->setTotalValue($data['totalValue']);
            $contract->setPaymentMethod($data['paymentMethod']);
            $contract->setNumberOfInstallments($data['numberOfInstallments']);

            # Installments calculations
            $baseAmount = floatval($data['totalValue']) / intval($data['numberOfInstallments']);
            $paymentMethod = $data['paymentMethod'];

            # Logic Calculation
            if ($paymentMethod === 'paypal') {
                $interestRate = 0.01;
                $transactionFee = 0.02;
            } else {
                $interestRate = 0.02;
                $transactionFee = 0.01;
            }

            # Generating installments
            for ($i = 1; $i <= $data['numberOfInstallments']; $i++) {
                $interest = $baseAmount * $interestRate;
                $fee = $baseAmount * $transactionFee;
                $installmentAmount = $baseAmount + $interest + $fee;

                $dueDate = (new \DateTimeImmutable($data['contractDate']))
                    ->modify("+{$i} month");

                $installment = new Installment();
                $installment->setContract($contract);
                $installment->setInstallmentNumber($i);
                $installment->setAmount(number_format($installmentAmount, 2, '.', ''));
                $installment->setDueDate($dueDate);
                $installment->setInterestRate(number_format($interestRate, 4, '.', ''));
                $installment->setTransactionFee(number_format($transactionFee, 4, '.', ''));

                $contract->addInstallment($installment);
            }

            $this->em->persist($contract);
            $this->em->flush();

            return $this->json([
                'state' => 'ok',
                'message' => 'Contrato creado correctamente',
                'data' => [
                    'id' => $contract->getId(),
                    'contractNumber' => $contract->getContractNumber()
                ]
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'state' => 'error',
                'message' => 'Error al crear el contrato: ' . $e->getMessage()
            ], 500);
        }
    }
}
