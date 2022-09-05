<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Supplier;
use App\Repository\Transaction\PurchaseInvoiceHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseInvoiceHeaderRepository::class)]
class PurchaseInvoiceHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $transactionDate = null;

    #[ORM\Column(length: 60)]
    private ?string $invoiceTaxCodeNumber = null;

    #[ORM\Column(length: 60)]
    private ?string $supplierInvoiceCodeNumber = null;

    #[ORM\Column(length: 20)]
    private ?string $discountType = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $discountNominal = null;

    #[ORM\Column]
    private ?int $taxPercentage = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $taxNominal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $shippingFee = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $subTotal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $grandTotal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $totalPayment = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $totalReturn = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $remainingPayment = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Supplier $supplier = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?ReceiveHeader $receiveHeader = null;

    #[ORM\OneToMany(mappedBy: 'purchaseInvoiceHeader', targetEntity: PurchaseInvoiceDetail::class)]
    private Collection $purchaseInvoiceDetails;

    #[ORM\OneToMany(mappedBy: 'purchaseInvoiceHeader', targetEntity: PurchasePaymentDetail::class)]
    private Collection $purchasePaymentDetails;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdTransactionDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $modifiedTransactionDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $approvedTransactionDateTime = null;

    public function __construct()
    {
        $this->purchaseInvoiceDetails = new ArrayCollection();
        $this->purchasePaymentDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransactionDate(): ?\DateTimeInterface
    {
        return $this->transactionDate;
    }

    public function setTransactionDate(\DateTimeInterface $transactionDate): self
    {
        $this->transactionDate = $transactionDate;

        return $this;
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

    public function getSupplierInvoiceCodeNumber(): ?string
    {
        return $this->supplierInvoiceCodeNumber;
    }

    public function setSupplierInvoiceCodeNumber(string $supplierInvoiceCodeNumber): self
    {
        $this->supplierInvoiceCodeNumber = $supplierInvoiceCodeNumber;

        return $this;
    }

    public function getDiscountType(): ?string
    {
        return $this->discountType;
    }

    public function setDiscountType(string $discountType): self
    {
        $this->discountType = $discountType;

        return $this;
    }

    public function getDiscountNominal(): ?string
    {
        return $this->discountNominal;
    }

    public function setDiscountNominal(string $discountNominal): self
    {
        $this->discountNominal = $discountNominal;

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

    public function getShippingFee(): ?string
    {
        return $this->shippingFee;
    }

    public function setShippingFee(string $shippingFee): self
    {
        $this->shippingFee = $shippingFee;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

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

    public function getReceiveHeader(): ?ReceiveHeader
    {
        return $this->receiveHeader;
    }

    public function setReceiveHeader(ReceiveHeader $receiveHeader): self
    {
        $this->receiveHeader = $receiveHeader;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseInvoiceDetail>
     */
    public function getPurchaseInvoiceDetails(): Collection
    {
        return $this->purchaseInvoiceDetails;
    }

    public function addPurchaseInvoiceDetail(PurchaseInvoiceDetail $purchaseInvoiceDetail): self
    {
        if (!$this->purchaseInvoiceDetails->contains($purchaseInvoiceDetail)) {
            $this->purchaseInvoiceDetails->add($purchaseInvoiceDetail);
            $purchaseInvoiceDetail->setPurchaseInvoiceHeader($this);
        }

        return $this;
    }

    public function removePurchaseInvoiceDetail(PurchaseInvoiceDetail $purchaseInvoiceDetail): self
    {
        if ($this->purchaseInvoiceDetails->removeElement($purchaseInvoiceDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseInvoiceDetail->getPurchaseInvoiceHeader() === $this) {
                $purchaseInvoiceDetail->setPurchaseInvoiceHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PurchasePaymentDetail>
     */
    public function getPurchasePaymentDetails(): Collection
    {
        return $this->purchasePaymentDetails;
    }

    public function addPurchasePaymentDetail(PurchasePaymentDetail $purchasePaymentDetail): self
    {
        if (!$this->purchasePaymentDetails->contains($purchasePaymentDetail)) {
            $this->purchasePaymentDetails->add($purchasePaymentDetail);
            $purchasePaymentDetail->setPurchaseInvoiceHeader($this);
        }

        return $this;
    }

    public function removePurchasePaymentDetail(PurchasePaymentDetail $purchasePaymentDetail): self
    {
        if ($this->purchasePaymentDetails->removeElement($purchasePaymentDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchasePaymentDetail->getPurchaseInvoiceHeader() === $this) {
                $purchasePaymentDetail->setPurchaseInvoiceHeader(null);
            }
        }

        return $this;
    }

    public function getCreatedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->createdTransactionDateTime;
    }

    public function setCreatedTransactionDateTime(\DateTimeInterface $createdTransactionDateTime): self
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
}
