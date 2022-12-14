<?php

namespace App\Entity\Transaction;

use App\Entity\Admin\User;
use App\Entity\Master\Currency;
use App\Entity\Master\Supplier;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\PurchaseOrderHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseOrderHeaderRepository::class)]
#[ORM\Table(name: 'transaction_purchase_order_header')]
class PurchaseOrderHeader extends TransactionHeader
{
    public const DISCOUNT_VALUE_TYPE_PERCENTAGE = 'percentage';
    public const DISCOUNT_VALUE_TYPE_NOMINAL = 'nominal';
    public const TAX_MODE_NON_TAX = 'non_tax';
    public const TAX_MODE_TAX_EXCLUSION = 'tax_exclusion';
    public const TAX_MODE_TAX_INCLUSION = 'tax_inclusion';
    public const VAT_PERCENTAGE = 11;
    public const TRANSACTION_STATUS_DRAFT = 'draft';
    public const TRANSACTION_STATUS_APPROVE = 'approve';
    public const TRANSACTION_STATUS_REJECT = 'reject';
    public const TRANSACTION_STATUS_PARTIAL_RECEIVE = 'partial_receive';
    public const TRANSACTION_STATUS_FULL_RECEIVE = 'full_receive';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $discountValueType = self::DISCOUNT_VALUE_TYPE_PERCENTAGE;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $discountValue = '0.00';

    #[ORM\Column(length: 20)]
    private ?string $taxMode = self::TAX_MODE_NON_TAX;

    #[ORM\Column]
    private ?int $taxPercentage = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $taxNominal = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $subTotal = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $subTotalAfterTaxInclusion = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $grandTotal = '0.00';

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $approvedTransactionDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $rejectedTransactionDateTime = null;

    #[ORM\ManyToOne]
    private ?Supplier $supplier = null;

    #[ORM\OneToMany(mappedBy: 'purchaseOrderHeader', targetEntity: PurchaseOrderDetail::class)]
    private Collection $purchaseOrderDetails;

    #[ORM\OneToMany(mappedBy: 'purchaseOrderHeader', targetEntity: ReceiveHeader::class)]
    private Collection $receiveHeaders;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $deliveryDate = null;

    #[ORM\ManyToOne]
    private ?Currency $currency = null;

    #[ORM\ManyToOne]
    protected ?User $approvedTransactionUser = null;

    #[ORM\ManyToOne]
    protected ?User $rejectedTransactionUser = null;

    #[ORM\Column(length: 60)]
    private ?string $transactionStatus = self::TRANSACTION_STATUS_DRAFT;

    public function __construct()
    {
        $this->purchaseOrderDetails = new ArrayCollection();
        $this->receiveHeaders = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return 'PO';
    }

    public function getSyncTaxPercentage(): int
    {
        $taxPercentage = $this->taxMode === self::TAX_MODE_NON_TAX ? 0 : self::VAT_PERCENTAGE;
        return $taxPercentage;
    }

    public function getSyncTaxNominal(): string
    {
        $taxNominal = $this->getSubTotalAfterDiscount() * $this->taxPercentage / 100;
        return $taxNominal;
    }

    public function getSyncSubTotal(): string
    {
        $subTotal = '0.00';
        foreach ($this->purchaseOrderDetails as $purchaseOrderDetail) {
            if (!$purchaseOrderDetail->isIsCanceled()) {
                $subTotal += $purchaseOrderDetail->getTotal();
            }
        }
        return $subTotal;
    }

    public function getSyncSubTotalAfterTaxInclusion(): string
    {
        $subTotalAfterTaxInclusion = $this->taxMode === self::TAX_MODE_TAX_INCLUSION ? $this->subTotal / (1 + $this->taxPercentage / 100) : $this->subTotal;
        return $subTotalAfterTaxInclusion;
    }

    public function getSyncGrandTotal(): string
    {
        $grandTotal = $this->getSubTotalAfterDiscount() + $this->taxNominal;
        return $grandTotal;
    }

    public function getDiscountNominal(): string
    {
        return $this->discountValueType === self::DISCOUNT_VALUE_TYPE_NOMINAL ? $this->discountValue : $this->subTotal * $this->discountValue / 100;
    }

    public function getSubTotalAfterDiscount(): string
    {
        return $this->subTotalAfterTaxInclusion - $this->getDiscountNominal();
    }

    public function getTotalRemaining(): string
    {
        $totalRemaining = '0.00';
        foreach ($this->purchaseOrderDetails as $purchaseOrderDetail) {
            if (!$purchaseOrderDetail->isIsCanceled()) {
                $totalRemaining += $purchaseOrderDetail->getRemainingReceive();
            }
        }
        return $totalRemaining;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscountValueType(): ?string
    {
        return $this->discountValueType;
    }

    public function setDiscountValueType(string $discountValueType): self
    {
        $this->discountValueType = $discountValueType;

        return $this;
    }

    public function getDiscountValue(): ?string
    {
        return $this->discountValue;
    }

    public function setDiscountValue(string $discountValue): self
    {
        $this->discountValue = $discountValue;

        return $this;
    }

    public function getTaxMode(): ?string
    {
        return $this->taxMode;
    }

    public function setTaxMode(string $taxMode): self
    {
        $this->taxMode = $taxMode;

        return $this;
    }

    public function getTaxPercentage(): ?int
    {
        return $this->taxPercentage;
    }

    public function setTaxPercentage(int $taxPercentage): self
    {
        $this->taxPercentage = $taxPercentage;

        return $this;
    }

    public function getTaxNominal(): ?string
    {
        return $this->taxNominal;
    }

    public function setTaxNominal(string $taxNominal): self
    {
        $this->taxNominal = $taxNominal;

        return $this;
    }

    public function getSubTotal(): ?string
    {
        return $this->subTotal;
    }

    public function setSubTotal(string $subTotal): self
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    public function getSubTotalAfterTaxInclusion(): ?string
    {
        return $this->subTotalAfterTaxInclusion;
    }

    public function setSubTotalAfterTaxInclusion(string $subTotalAfterTaxInclusion): self
    {
        $this->subTotalAfterTaxInclusion = $subTotalAfterTaxInclusion;

        return $this;
    }

    public function getGrandTotal(): ?string
    {
        return $this->grandTotal;
    }

    public function setGrandTotal(string $grandTotal): self
    {
        $this->grandTotal = $grandTotal;

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
        return $this->rejectedTransactionDateTime;
    }

    public function setRejectedTransactionDateTime(?\DateTimeInterface $rejectedTransactionDateTime): self
    {
        $this->rejectedTransactionDateTime = $rejectedTransactionDateTime;

        return $this;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseOrderDetail>
     */
    public function getPurchaseOrderDetails(): Collection
    {
        return $this->purchaseOrderDetails;
    }

    public function addPurchaseOrderDetail(PurchaseOrderDetail $purchaseOrderDetail): self
    {
        if (!$this->purchaseOrderDetails->contains($purchaseOrderDetail)) {
            $this->purchaseOrderDetails->add($purchaseOrderDetail);
            $purchaseOrderDetail->setPurchaseOrderHeader($this);
        }

        return $this;
    }

    public function removePurchaseOrderDetail(PurchaseOrderDetail $purchaseOrderDetail): self
    {
        if ($this->purchaseOrderDetails->removeElement($purchaseOrderDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseOrderDetail->getPurchaseOrderHeader() === $this) {
                $purchaseOrderDetail->setPurchaseOrderHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReceiveHeader>
     */
    public function getReceiveHeaders(): Collection
    {
        return $this->receiveHeaders;
    }

    public function addReceiveHeader(ReceiveHeader $receiveHeader): self
    {
        if (!$this->receiveHeaders->contains($receiveHeader)) {
            $this->receiveHeaders->add($receiveHeader);
            $receiveHeader->setPurchaseOrderHeader($this);
        }

        return $this;
    }

    public function removeReceiveHeader(ReceiveHeader $receiveHeader): self
    {
        if ($this->receiveHeaders->removeElement($receiveHeader)) {
            // set the owning side to null (unless already changed)
            if ($receiveHeader->getPurchaseOrderHeader() === $this) {
                $receiveHeader->setPurchaseOrderHeader(null);
            }
        }

        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(?\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

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
    
    public function getSyncSubTotalPaper(): string
    {
        $subTotal = '0.00';
        foreach ($this->purchaseOrderDetails as $purchaseOrderDetail) {
            if (!$purchaseOrderDetail->isIsCanceled()) {
                $subTotal += $purchaseOrderDetail->getTotalPaperPrice();
            }
        }
        return $subTotal;
    }
}
