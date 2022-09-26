<?php

namespace App\Entity\Accounting;

use App\Entity\Master\Account;
use App\Repository\Accounting\AccountingLedgerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountingLedgerRepository::class)]
class AccountingLedger
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $transactionType = null;

    #[ORM\Column(length: 100)]
    private ?string $transactionSubject = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $debitAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $creditAmount = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransactionType(): ?string
    {
        return $this->transactionType;
    }

    public function setTransactionType(string $transactionType): self
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    public function getTransactionSubject(): ?string
    {
        return $this->transactionSubject;
    }

    public function setTransactionSubject(string $transactionSubject): self
    {
        $this->transactionSubject = $transactionSubject;

        return $this;
    }

    public function getDebitAmount(): ?string
    {
        return $this->debitAmount;
    }

    public function setDebitAmount(string $debitAmount): self
    {
        $this->debitAmount = $debitAmount;

        return $this;
    }

    public function getCreditAmount(): ?string
    {
        return $this->creditAmount;
    }

    public function setCreditAmount(string $creditAmount): self
    {
        $this->creditAmount = $creditAmount;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }
}
