<?php

namespace App\Entity\Transaction;

use App\Entity\Admin\User;
use App\Entity\Master\Warehouse;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\PurchaseRequestPaperHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseRequestPaperHeaderRepository::class)]
#[ORM\Table(name: 'transaction_purchase_request_paper_header')]
class PurchaseRequestPaperHeader extends TransactionHeader
{
    public const CODE_NUMBER_CONSTANT = 'PRP';
    public const TRANSACTION_STATUS_DRAFT = 'draft';
    public const TRANSACTION_STATUS_HOLD = 'hold';
    public const TRANSACTION_STATUS_APPROVE = 'approve';
    public const TRANSACTION_STATUS_REJECT = 'reject';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $totalQuantity = 0;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Warehouse $warehouse = null;

    #[ORM\OneToMany(mappedBy: 'purchaseRequestPaperHeader', targetEntity: PurchaseRequestPaperDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $purchaseRequestPaperDetails;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $approvedTransactionDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $RejectedTransactionDateTime = null;

    #[ORM\ManyToOne]
    private ?User $approvedTransactionUser = null;

    #[ORM\ManyToOne]
    private ?User $rejectedTransactionUser = null;

    #[ORM\Column(length: 60)]
    private ?string $transactionStatus = self::TRANSACTION_STATUS_DRAFT;

    public function __construct()
    {
        $this->purchaseRequestPaperDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTotalQuantity(): int
    {
        $totalQuantity = 0;
        foreach ($this->purchaseRequestPaperDetails as $purchaseRequestPaperDetail) {
            if (!$purchaseRequestPaperDetail->isIsCanceled()) {
                $totalQuantity += $purchaseRequestPaperDetail->getQuantity();
            }
        }
        return $totalQuantity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalQuantity(): ?int
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(int $totalQuantity): self
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseRequestPaperDetail>
     */
    public function getPurchaseRequestPaperDetails(): Collection
    {
        return $this->purchaseRequestPaperDetails;
    }

    public function addPurchaseRequestPaperDetail(PurchaseRequestPaperDetail $purchaseRequestPaperDetail): self
    {
        if (!$this->purchaseRequestPaperDetails->contains($purchaseRequestPaperDetail)) {
            $this->purchaseRequestPaperDetails->add($purchaseRequestPaperDetail);
            $purchaseRequestPaperDetail->setPurchaseRequestPaperHeader($this);
        }

        return $this;
    }

    public function removePurchaseRequestPaperDetail(PurchaseRequestPaperDetail $purchaseRequestPaperDetail): self
    {
        if ($this->purchaseRequestPaperDetails->removeElement($purchaseRequestPaperDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseRequestPaperDetail->getPurchaseRequestPaperHeader() === $this) {
                $purchaseRequestPaperDetail->setPurchaseRequestPaperHeader(null);
            }
        }

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

    public function getRejectedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->RejectedTransactionDateTime;
    }

    public function setRejectedTransactionDateTime(?\DateTimeInterface $RejectedTransactionDateTime): self
    {
        $this->RejectedTransactionDateTime = $RejectedTransactionDateTime;

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

    public function getRejectedTransactionUser(): ?User
    {
        return $this->rejectedTransactionUser;
    }

    public function setRejectedTransactionUser(?User $rejectedTransactionUser): self
    {
        $this->rejectedTransactionUser = $rejectedTransactionUser;

        return $this;
    }

    public function getTransactionStatus(): ?string
    {
        return $this->transactionStatus;
    }

    public function setTransactionStatus(string $transactionStatus): self
    {
        $this->transactionStatus = $transactionStatus;

        return $this;
    }
}
