<?php
namespace App\Service;

use App\DTO\Request\CreateContractRequest;
use App\DTO\Response\ContractResponse;
use App\Entity\Contract;
use App\Exceptions\Contract404Exception;
use App\Exceptions\InvalidContractException;
use App\Repository\ContractRepository;
use App\Service\Validator\ContractValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class ContractService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ContractRepository $contractRepository,
        private ContractValidatorInterface $validator,
        private InstallmentProjectionService $projectionService
    ) {}

    public function createContract(CreateContractRequest $request): ContractResponse
    {
        # Validates
        $errors = $this->validator->validate([
            'contractNumber' => $request->getContractNumber(),
            'contractDate' => $request->getContractDate(),
            'totalValue' => $request->getTotalValue(),
            'paymentMethod' => $request->getPaymentMethod(),
            'numberOfInstallments' => $request->getNumberOfInstallments(),
        ]);

        if (!empty($errors)) {
            throw new InvalidContractException(
                'Validación fallida: ' . json_encode($errors)
            );
        }

        # Contract Creation
        $contract = new Contract();
        $contract->setCreatedAt(new \DateTimeImmutable());
        $contract->setUpdatedAt(new \DateTimeImmutable());
        $contract->setContractNumber($request->getContractNumber());
        $contract->setContractDate(new \DateTimeImmutable($request->getContractDate()));
        $contract->setTotalValue($request->getTotalValue());
        $contract->setPaymentMethod($request->getPaymentMethod());
        $contract->setNumberOfInstallments($request->getNumberOfInstallments());

        $this->projectionService->generateInstallments($contract);

        $this->em->persist($contract);
        $this->em->flush();

        return $this->mapToResponse($contract);
    }

    public function getContract(int $id): ContractResponse
    {
        $contract = $this->contractRepository->find($id);

        if (!$contract) {
            throw new Contract404Exception($id);
        }

        return $this->mapToResponse($contract);
    }

    public function getAllContracts(): array
    {
        return array_map(
            function(Contract $contract) {
                return $this->mapToResponse($contract);
            },
            $this->contractRepository->findBy([], ['id' => 'DESC'])
        );
    }

    public function deleteContract(int $id): void
    {
        $contract = $this->contractRepository->find($id);

        if (!$contract) {
            throw new Contract404Exception($id);
        }

        $this->em->remove($contract);
        $this->em->flush();
    }

    private function mapToResponse(Contract $contract): ContractResponse
    {
        $projection = $this->projectionService->generateProjection($contract);

        return new ContractResponse(
            $contract->getId(),
            $contract->getContractNumber(),
            $contract->getContractDate()->format('Y-m-d'),
            $contract->getTotalValue(),
            $contract->getPaymentMethod(),
            $contract->getNumberOfInstallments(),
            array_map(
                function($p) {
                    return $p->toArray();
                },
                $projection
            )
        );
    }

    public function getContractEntity(int $id): Contract
    {
        $contract = $this->contractRepository->find($id);

        if (!$contract) {
            throw new Contract404Exception($id);
        }

        return $contract;
    }
}
