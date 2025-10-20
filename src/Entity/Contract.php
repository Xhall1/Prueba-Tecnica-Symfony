<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Installment;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
class Contract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $contractNumber = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $contractDate = null;

    #[ORM\Column]
    private ?float $totalValue = null;

    #[ORM\Column(length: 100)]
    private ?string $paymentMethod = null;

    #[ORM\Column]
    private ?int $numberOfInstallments = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'contract', targetEntity: Installment::class, cascade: ['persist', 'remove'])]
    private Collection $installments;

    public function __construct()
    {
        $this->installments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContractNumber(): ?string
    {
        return $this->contractNumber;
    }

    public function setContractNumber(string $contractNumber): static
    {
        $this->contractNumber = $contractNumber;
        return $this;
    }

    public function getContractDate(): ?\DateTimeImmutable
    {
        return $this->contractDate;
    }

    public function setContractDate(\DateTimeImmutable $contractDate): static
    {
        $this->contractDate = $contractDate;
        return $this;
    }

    public function getTotalValue(): ?float
    {
        return $this->totalValue;
    }

    public function setTotalValue(float $totalValue): static
    {
        $this->totalValue = $totalValue;
        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function getNumberOfInstallments(): ?int
    {
        return $this->numberOfInstallments;
    }

    public function setNumberOfInstallments(int $numberOfInstallments): static
    {
        $this->numberOfInstallments = $numberOfInstallments;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getInstallments(): Collection
    {
        return $this->installments;
    }

    #METHODS to add installments
    public function addInstallment(Installment $installment): static
    {
        if (!$this->installments->contains($installment)) {
            $this->installments->add($installment);
            $installment->setContract($this);
        }

        return $this;
    }

    #METHODS to remove installments
    public function removeInstallment(Installment $installment): static
    {
        if ($this->installments->removeElement($installment)) {
            if ($installment->getContract() === $this) {
                $installment->setContract(null);
            }
        }

        return $this;
    }
}
