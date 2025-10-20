<?php

namespace App\Controller;

use App\DTO\Request\CreateContractRequest;
use App\Exceptions\Contract404Exception;
use App\Exceptions\InvalidContractException;
use App\Service\ContractService;
use App\Service\InstallmentProjectionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ContractController extends AbstractController
{
    public function __construct(
        private ContractService              $contractService,
        private InstallmentProjectionService $projectionService
    )
    {

    }

    #GET all contracts
    #[Route('/api/v1/contract', methods: ['GET'])]
    public function getAllContracts(): JsonResponse
    {
        try {
            $contracts = $this->contractService->getAllContracts();
            return $this->json([
                'state' => 'ok',
                'data' => array_map(fn($c) => $c->toArray(), $contracts)
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'state' => 'error',
                'message' => 'No se pudieron recuperar los contratos'
            ], 500);
        }
    }

    #GET contracts by ID
    #[Route('/api/v1/contract/{id}', methods: ['GET'])]
    public function getContract(int $id): JsonResponse
    {
        try {
            $contract = $this->contractService->getContract($id);
            return $this->json([
                'state' => 'ok',
                'data' => $contract->toArray()
            ]);
        } catch (Contract404Exception $e) {
            return $this->json([
                'state' => 'error',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return $this->json([
                'state' => 'error',
                'message' => 'Error inesperado'
            ], 500);
        }
    }

    # POST create contract
    #[Route('/api/v1/contract', methods: ['POST'])]
    public function createContract(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $createRequest = new CreateContractRequest($data);
            $contract = $this->contractService->createContract($createRequest);

            return $this->json([
                'state' => 'ok',
                'message' => 'Contrato creado exitosamente',
                'data' => [
                    'id' => $contract->toArray()['id'],
                    'contractNumber' => $contract->toArray()['contractNumber']
                ]
            ], 201);
        } catch (InvalidContractException $e) {
            return $this->json([
                'state' => 'error',
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return $this->json([
                'state' => 'error',
                'message' => 'Fallo al crear nuevo contrato'
            ], 500);
        }
    }

    # GET installments projection
    #[Route('/api/v1/contract/{id}/installments', methods: ['GET'])]
    public function getInstallmentProjection(int $id, Request $request): JsonResponse
    {
        try {
            $contractResponse = $this->contractService->getContract($id);
            $contractArray = $contractResponse->toArray();

            # Define projection months
            $months = $request->query->get('months', $contractArray['numberOfInstallments']);

            $contractEntity = $this->contractService->getContractEntity($id);
            $projection = $this->projectionService->generateProjection($contractEntity, intval($months));

            return $this->json([
                'state' => 'ok',
                'contract' => [
                    'id' => $contractArray['id'],
                    'contractNumber' => $contractArray['contractNumber'],
                    'totalValue' => $contractArray['totalValue'],
                    'paymentMethod' => $contractArray['paymentMethod']
                ],
                'projection' => array_map(fn($p) => $p->toArray(), $projection)
            ]);
        } catch (Contract404Exception $e) {
            return $this->json([
                'state' => 'error',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return $this->json([
                'state' => 'error',
                'message' => 'Fallo al generar la proyección'
            ], 500);
        }
    }

    # DELETE contract
    #[Route('/api/v1/contract/{id}', methods: ['DELETE'])]
    public function deleteContract(int $id): JsonResponse
    {
        try {
            $this->contractService->deleteContract($id);
            return $this->json([
                'state' => 'ok',
                'message' => 'Contrato eliminado exitosamente'
            ]);
        } catch (Contract404Exception $e) {
            return $this->json([
                'state' => 'error',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return $this->json([
                'state' => 'error',
                'message' => 'Fallo al eliminar el contrato'
            ], 500);
        }
    }
}

