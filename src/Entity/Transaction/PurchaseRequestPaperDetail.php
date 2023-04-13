<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Paper;
use App\Entity\Master\Unit;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\PurchaseRequestPaperDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseRequestPaperDetailRepository::class)]
#[ORM\Table(name: 'transaction_purchase_request_paper_detail')]
class PurchaseRequestPaperDetail extends TransactionDetail
{
    public const TRANSACTION_STATUS_OPEN = 'open';
    public const TRANSACTION_STATUS_PURCHASE = 'purchase';
    public const TRANSACTION_STATUS_RECEIVE = 'part_rcv';
    public const TRANSACTION_STATUS_CLOSE = 'full_rcv';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    private ?int $quantity = 0;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotNull]
    private ?\DateTimeInterface $usageDate = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $memo = '';

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseRequestPaperDetails')]
    private ?PurchaseRequestPaperHeader $purchaseRequestPaperHeader = null;

    #[ORM\OneToOne(mappedBy: 'purchaseRequestPaperDetail', cascade: ['persist', 'remove'])]
    private ?PurchaseOrderPaperDetail $purchaseOrderPaperDetail = null;

    #[ORM\Column(length: 60)]
    private ?string $transactionStatus = self::TRANSACTION_STATUS_OPEN;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->purchaseRequestPaperHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUsageDate(): ?\DateTimeInterface
    {
        return $this->usageDate;
    }

    public function setUsageDate(?\DateTimeInterface $usageDate): self
    {
        $this->usageDate = $usageDate;

        return $this;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(string $memo): self
    {
        $this->memo = $memo;

        return $this;
    }

    public function getPaper(): ?Paper
    {
        return $this->paper;
    }

    public function setPaper(?Paper $paper): self
    {
        $this->paper = $paper;

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getPurchaseRequestPaperHeader(): ?PurchaseRequestPaperHeader
    {
        return $this->purchaseRequestPaperHeader;
    }

    public function setPurchaseRequestPaperHeader(?PurchaseRequestPaperHeader $purchaseRequestPaperHeader): self
    {
        $this->purchaseRequestPaperHeader = $purchaseRequestPaperHeader;

        return $this;
    }

    public function getPurchaseOrderPaperDetail(): ?PurchaseOrderPaperDetail
    {
        return $this->purchaseOrderPaperDetail;
    }

    public function setPurchaseOrderPaperDetail(?PurchaseOrderPaperDetail $purchaseOrderPaperDetail): self
    {
        // unset the owning side of the relation if necessary
        if ($purchaseOrderPaperDetail === null && $this->purchaseOrderPaperDetail !== null) {
            $this->purchaseOrderPaperDetail->setPurchaseRequestPaperDetail(null);
        }

        // set the owning side of the relation if necessary
        if ($purchaseOrderPaperDetail !== null && $purchaseOrderPaperDetail->getPurchaseRequestPaperDetail() !== $this) {
            $purchaseOrderPaperDetail->setPurchaseRequestPaperDetail($this);
        }

        $this->purchaseOrderPaperDetail = $purchaseOrderPaperDetail;

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
