<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Customer;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\SaleInvoiceHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleInvoiceHeaderRepository::class)]
#[ORM\Table(name: 'transaction_sale_invoice_header')]
class SaleInvoiceHeader extends TransactionHeader
{
    public const DISCOUNT_VALUE_TYPE_PERCENTAGE = 'percentage';
    public const DISCOUNT_VALUE_TYPE_NOMINAL = 'nominal';
    public const TAX_MODE_NON_TAX = 'non_tax';
    public const TAX_MODE_TAX_EXCLUSION = 'tax_exclusion';
    public const TAX_MODE_TAX_INCLUSION = 'tax_inclusion';
    public const VAT_PERCENTAGE = 11;
    public const TRANSACTION_STATUS_INVOICING = 'invoicing';
    public const TRANSACTION_STATUS_PARTIAL_PAYMENT = 'partial_payment';
    public const TRANSACTION_STATUS_FULL_PAYMENT = 'full_payment';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $invoiceTaxCodeNumber = '';

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

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $totalPayment = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $totalReturn = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $remainingPayment = '0.00';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $invoiceTaxDate = null;

    #[ORM\Column(length: 60)]
    private ?string $transactionStatus = self::TRANSACTION_STATUS_INVOICING;

    #[ORM\ManyToOne]
    private ?Customer $customer = null;

    #[ORM\ManyToOne(inversedBy: 'saleInvoiceHeaders')]
    private ?DeliveryHeader $deliveryHeader = null;

    #[ORM\OneToMany(mappedBy: 'saleInvoiceHeader', targetEntity: SalePaymentDetail::class)]
    private Collection $salePaymentDetails;

    #[ORM\OneToMany(mappedBy: 'saleInvoiceHeader', targetEntity: SaleInvoiceDetail::class)]
    private Collection $saleInvoiceDetails;

    public function __construct()
    {
        $this->salePaymentDetails = new ArrayCollection();
        $this->saleInvoiceDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return 'SIN';
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
        foreach ($this->saleInvoiceDetails as $saleInvoiceDetail) {
            if (!$saleInvoiceDetail->isIsCanceled()) {
                $subTotal += $saleInvoiceDetail->getTotal();
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

    public function getSyncDueDate(): \DateTimeInterface
    {
        $paymentTerm = $this->customer === null ? 0 : $this->customer->getPaymentTerm();
        $dueDate = \DateTime::createFromInterface($this->transactionDate);
        $dueDate->add(new \DateInterval('P' . $paymentTerm . 'd'));
        return $dueDate;
    }

    public function getSyncRemainingPayment(): int
    {
        return $this->grandTotal - $this->totalPayment;
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

    public function getInvoiceTaxCodeNumber(): ?string
    {
        return $this->invoiceTaxCodeNumber;
    }

    public function setInvoiceTaxCodeNumber(string $invoiceTaxCodeNumber): self
    {
        $this->invoiceTaxCodeNumber = $invoiceTaxCodeNumber;

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

    public function getTotalPayment(): ?string
    {
        return $this->totalPayment;
    }

    public function setTotalPayment(string $totalPayment): self
    {
        $this->totalPayment = $totalPayment;

        return $this;
    }

    public function getTotalReturn(): ?string
    {
        return $this->totalReturn;
    }

    public function setTotalReturn(string $totalReturn): self
    {
        $this->totalReturn = $totalReturn;

        return $this;
    }

    public function getRemainingPayment(): ?string
    {
        return $this->remainingPayment;
    }

    public function setRemainingPayment(string $remainingPayment): self
    {
        $this->remainingPayment = $remainingPayment;

        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getInvoiceTaxDate(): ?\DateTimeInterface
    {
        return $this->invoiceTaxDate;
    }

    public function setInvoiceTaxDate(?\DateTimeInterface $invoiceTaxDate): self
    {
        $this->invoiceTaxDate = $invoiceTaxDate;

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

    public function getDeliveryHeader(): ?DeliveryHeader
    {
        return $this->deliveryHeader;
    }

    public function setDeliveryHeader(?DeliveryHeader $deliveryHeader): self
    {
        $this->deliveryHeader = $deliveryHeader;

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

    /**
     * @return Collection<int, SalePaymentDetail>
     */
    public function getSalePaymentDetails(): Collection
    {
        return $this->salePaymentDetails;
    }

    public function addSalePaymentDetail(SalePaymentDetail $salePaymentDetail): self
    {
        if (!$this->salePaymentDetails->contains($salePaymentDetail)) {
            $this->salePaymentDetails->add($salePaymentDetail);
            $salePaymentDetail->setSaleInvoiceHeader($this);
        }

        return $this;
    }

    public function removeSalePaymentDetail(SalePaymentDetail $salePaymentDetail): self
    {
        if ($this->salePaymentDetails->removeElement($salePaymentDetail)) {
            // set the owning side to null (unless already changed)
            if ($salePaymentDetail->getSaleInvoiceHeader() === $this) {
                $salePaymentDetail->setSaleInvoiceHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SaleInvoiceDetail>
     */
    public function getSaleInvoiceDetails(): Collection
    {
        return $this->saleInvoiceDetails;
    }

    public function addSaleInvoiceDetail(SaleInvoiceDetail $saleInvoiceDetail): self
    {
        if (!$this->saleInvoiceDetails->contains($saleInvoiceDetail)) {
            $this->saleInvoiceDetails->add($saleInvoiceDetail);
            $saleInvoiceDetail->setSaleInvoiceHeader($this);
        }

        return $this;
    }

    public function removeSaleInvoiceDetail(SaleInvoiceDetail $saleInvoiceDetail): self
    {
        if ($this->saleInvoiceDetails->removeElement($saleInvoiceDetail)) {
            // set the owning side to null (unless already changed)
            if ($saleInvoiceDetail->getSaleInvoiceHeader() === $this) {
                $saleInvoiceDetail->setSaleInvoiceHeader(null);
            }
        }

        return $this;
    }
}
