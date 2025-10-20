<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use App\Entity\Contract;

#[ORM\Entity(repositoryClass: InstallmentRepository::class)]
class Installment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $installmentNumber = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dueDate = null;


    #[ORM\Column]
    private ?float $interestRate = null;

    #[ORM\Column]
    private ?float $transactionFee = null;

    #[ORM\ManyToOne(targetEntity: Contract::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Ignore]
    private ?Contract $contract = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstallmentNumber(): ?int
    {
        return $this->installmentNumber;
    }

    public function setInstallmentNumber(int $installmentNumber): static
    {
        $this->installmentNumber = $installmentNumber;
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    public function getDueDate(): ?\DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeImmutable $dueDate): static
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getInterestRate(): ?float
    {
        return $this->interestRate;
    }

    public function setInterestRate(float $interestRate): static
    {
        $this->interestRate = $interestRate;
        return $this;
    }

    public function getTransactionFee(): ?float
    {
        return $this->transactionFee;
    }

    public function setTransactionFee(float $transactionFee): static
    {
        $this->transactionFee = $transactionFee;
        return $this;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): static
    {
        $this->contract = $contract;
        return $this;
    }
}
