<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Material;
use App\Entity\Master\Unit;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\PurchaseRequestDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseRequestDetailRepository::class)]
#[ORM\Table(name: 'transaction_purchase_request_detail')]
class PurchaseRequestDetail extends TransactionDetail
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

    #[ORM\ManyToOne]
    private ?Material $material = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseRequestDetails')]
    private ?PurchaseRequestHeader $purchaseRequestHeader = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $usageDate = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $memo = '';

    #[ORM\OneToOne(mappedBy: 'purchaseRequestDetail', cascade: ['persist', 'remove'])]
    private ?PurchaseOrderDetail $purchaseOrderDetail = null;

    #[ORM\Column(length: 60)]
    private ?string $transactionStatus = self::TRANSACTION_STATUS_OPEN;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->purchaseRequestHeader->isIsCanceled() ? true : $this->isCanceled;
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

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

        return $this;
    }

    public function getPurchaseRequestHeader(): ?PurchaseRequestHeader
    {
        return $this->purchaseRequestHeader;
    }

    public function setPurchaseRequestHeader(?PurchaseRequestHeader $purchaseRequestHeader): self
    {
        $this->purchaseRequestHeader = $purchaseRequestHeader;

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

    public function getPurchaseOrderDetail(): ?PurchaseOrderDetail
    {
        return $this->purchaseOrderDetail;
    }

    public function setPurchaseOrderDetail(?PurchaseOrderDetail $purchaseOrderDetail): self
    {
        // unset the owning side of the relation if necessary
        if ($purchaseOrderDetail === null && $this->purchaseOrderDetail !== null) {
            $this->purchaseOrderDetail->setPurchaseRequestDetail(null);
        }

        // set the owning side of the relation if necessary
        if ($purchaseOrderDetail !== null && $purchaseOrderDetail->getPurchaseRequestDetail() !== $this) {
            $purchaseOrderDetail->setPurchaseRequestDetail($this);
        }

        $this->purchaseOrderDetail = $purchaseOrderDetail;

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
