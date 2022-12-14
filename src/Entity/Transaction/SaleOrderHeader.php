<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Customer;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\SaleOrderHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleOrderHeaderRepository::class)]
#[ORM\Table(name: 'transaction_sale_order_header')]
class SaleOrderHeader extends TransactionHeader
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
    public const TRANSACTION_STATUS_PARTIAL_DELIVERY = 'partial_delivery';
    public const TRANSACTION_STATUS_FULL_DELIVERY = 'full_delivery';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $referenceNumber = '';

    #[ORM\Column(length: 60)]
    private ?string $transactionStatus = self::TRANSACTION_STATUS_DRAFT;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deliveryDate = null;

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

    #[ORM\ManyToOne]
    private ?Customer $customer = null;

    #[ORM\OneToMany(mappedBy: 'saleOrderHeader', targetEntity: SaleOrderDetail::class)]
    private Collection $saleOrderDetails;

    #[ORM\OneToMany(mappedBy: 'saleOrderHeader', targetEntity: DeliveryHeader::class)]
    private Collection $deliveryHeaders;

    public function __construct()
    {
        $this->saleOrderDetails = new ArrayCollection();
        $this->deliveryHeaders = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return 'SO';
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
        foreach ($this->saleOrderDetails as $saleOrderDetail) {
            if (!$saleOrderDetail->isIsCanceled()) {
                $subTotal += $saleOrderDetail->getTotal();
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReferenceNumber(): ?string
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(string $referenceNumber): self
    {
        $this->referenceNumber = $referenceNumber;

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

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(?\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
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

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<int, SaleOrderDetail>
     */
    public function getSaleOrderDetails(): Collection
    {
        return $this->saleOrderDetails;
    }

    public function addSaleOrderDetail(SaleOrderDetail $saleOrderDetail): self
    {
        if (!$this->saleOrderDetails->contains($saleOrderDetail)) {
            $this->saleOrderDetails->add($saleOrderDetail);
            $saleOrderDetail->setSaleOrderHeader($this);
        }

        return $this;
    }

    public function removeSaleOrderDetail(SaleOrderDetail $saleOrderDetail): self
    {
        if ($this->saleOrderDetails->removeElement($saleOrderDetail)) {
            // set the owning side to null (unless already changed)
            if ($saleOrderDetail->getSaleOrderHeader() === $this) {
                $saleOrderDetail->setSaleOrderHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DeliveryHeader>
     */
    public function getDeliveryHeaders(): Collection
    {
        return $this->deliveryHeaders;
    }

    public function addDeliveryHeader(DeliveryHeader $deliveryHeader): self
    {
        if (!$this->deliveryHeaders->contains($deliveryHeader)) {
            $this->deliveryHeaders->add($deliveryHeader);
            $deliveryHeader->setSaleOrderHeader($this);
        }

        return $this;
    }

    public function removeDeliveryHeader(DeliveryHeader $deliveryHeader): self
    {
        if ($this->deliveryHeaders->removeElement($deliveryHeader)) {
            // set the owning side to null (unless already changed)
            if ($deliveryHeader->getSaleOrderHeader() === $this) {
                $deliveryHeader->setSaleOrderHeader(null);
            }
        }

        return $this;
    }
}
