<?php

namespace App\Entity;

use App\Entity\Admin\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class AccountingHeader
{
    #[ORM\Column]
    protected ?bool $isCanceled = false;

    #[ORM\Column]
    protected ?int $codeNumberOrdinal = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    protected ?int $codeNumberMonth = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    protected ?int $codeNumberYear = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $createdTransactionDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $modifiedTransactionDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $approvedTransactionDateTime = null;

    #[ORM\ManyToOne]
    protected ?User $createdTransactionUser = null;

    #[ORM\ManyToOne]
    protected ?User $modifiedTransactionUser = null;

    #[ORM\ManyToOne]
    protected ?User $approvedTransactionUser = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $transactionDate = null;

    #[ORM\Column(type: Types::TEXT)]
    protected ?string $note = '';

    public function isIsCanceled(): ?bool
    {
        return $this->isCanceled;
    }

    public function setIsCanceled(bool $isCanceled): self
    {
        $this->isCanceled = $isCanceled;

        return $this;
    }

    public function getCodeNumberOrdinal(): ?int
    {
        return $this->codeNumberOrdinal;
    }

    public function setCodeNumberOrdinal(int $codeNumberOrdinal): self
    {
        $this->codeNumberOrdinal = $codeNumberOrdinal;

        return $this;
    }

    public function getCodeNumberMonth(): ?int
    {
        return $this->codeNumberMonth;
    }

    public function setCodeNumberMonth(int $codeNumberMonth): self
    {
        $this->codeNumberMonth = $codeNumberMonth;

        return $this;
    }

    public function getCodeNumberYear(): ?int
    {
        return $this->codeNumberYear;
    }

    public function setCodeNumberYear(int $codeNumberYear): self
    {
        $this->codeNumberYear = $codeNumberYear;

        return $this;
    }

    public function getCreatedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->createdTransactionDateTime;
    }

    public function setCreatedTransactionDateTime(?\DateTimeInterface $createdTransactionDateTime): self
    {
        $this->createdTransactionDateTime = $createdTransactionDateTime;

        return $this;
    }

    public function getModifiedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->modifiedTransactionDateTime;
    }

    public function setModifiedTransactionDateTime(?\DateTimeInterface $modifiedTransactionDateTime): self
    {
        $this->modifiedTransactionDateTime = $modifiedTransactionDateTime;

        return $this;
    }

    public function getApprovedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->approvedTransactionDateTime;
    }

    public function setApprovedTransactionDateTime(?\DateTimeInterface $approvedTransactionDateTime): self
    {
        $this->approvedTransactionDateTime = $approvedTransactionDateTime;

        return $this;
    }

    public function getCreatedTransactionUser(): ?User
    {
        return $this->createdTransactionUser;
    }

    public function setCreatedTransactionUser(?User $createdTransactionUser): self
    {
        $this->createdTransactionUser = $createdTransactionUser;

        return $this;
    }

    public function getModifiedTransactionUser(): ?User
    {
        return $this->modifiedTransactionUser;
    }

    public function setModifiedTransactionUser(?User $modifiedTransactionUser): self
    {
        $this->modifiedTransactionUser = $modifiedTransactionUser;

        return $this;
    }

    public function getApprovedTransactionUser(): ?User
    {
        return $this->approvedTransactionUser;
    }

    public function setApprovedTransactionUser(?User $approvedTransactionUser): self
    {
        $this->approvedTransactionUser = $approvedTransactionUser;

        return $this;
    }

    public function getTransactionDate(): ?\DateTimeInterface
    {
        return $this->transactionDate;
    }

    public function setTransactionDate(?\DateTimeInterface $transactionDate): self
    {
        $this->transactionDate = $transactionDate;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
