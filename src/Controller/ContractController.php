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

        $data = $this->em->getRepository(Contract::class)->findBy([],[
            'id' => 'DESC'
        ]);

        if(!$data)
        {
            return $this->json([
                'state' => 'error',
                'message' => 'No hay información'
            ],400);
        }

        return $this->json($data);
    }

    #GET contracts by ID
    #[Route('/api/v1/contract/{id}', methods: ['GET'])]
    public function getAllContractsById(int $id): JsonResponse
    {

        $data = $this->em->getRepository(Contract::class)->find($id);

        if(!$data)
        {
            return $this->json([
                'state' => 'error',
                'message' => 'No se encontró el contrato'
            ],400);
        }

        return $this->json($data);
    }


}
