<?php

namespace App\Entity\Transaction;

use App\Entity\Master\PaymentType;
use App\Entity\Master\Supplier;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\PurchasePaymentHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchasePaymentHeaderRepository::class)]
#[ORM\Table(name: 'transaction_purchase_payment_header')]
class PurchasePaymentHeader extends TransactionHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Supplier $supplier = null;

    #[ORM\ManyToOne]
    private ?PaymentType $paymentType = null;

    #[ORM\OneToMany(mappedBy: 'purchasePaymentHeader', targetEntity: PurchasePaymentDetail::class)]
    private Collection $purchasePaymentDetails;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $totalAmount = '0.00';

    #[ORM\Column(length: 60)]
    private ?string $referenceNumber = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0')]
    private ?string $currencyRate = '0.00';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $referenceDate = null;

    #[ORM\Column(length: 100)]
    private ?string $supplierInvoiceCodeNumbers = '';

    public function __construct()
    {
        $this->purchasePaymentDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return 'PPY';
    }

    public function getSyncTotalAmount(): string
    {
        $totalAmount = '0.00';
        foreach ($this->purchasePaymentDetails as $purchasePaymentDetail) {
            if (!$purchasePaymentDetail->isIsCanceled()) {
                $totalAmount += $purchasePaymentDetail->getAmount();
            }
        }
        return $totalAmount;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPaymentType(): ?PaymentType
    {
        return $this->paymentType;
    }

    public function setPaymentType(?PaymentType $paymentType): self
    {
        $this->paymentType = $paymentType;

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
            $purchasePaymentDetail->setPurchasePaymentHeader($this);
        }

        return $this;
    }

    public function removePurchasePaymentDetail(PurchasePaymentDetail $purchasePaymentDetail): self
    {
        if ($this->purchasePaymentDetails->removeElement($purchasePaymentDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchasePaymentDetail->getPurchasePaymentHeader() === $this) {
                $purchasePaymentDetail->setPurchasePaymentHeader(null);
            }
        }

        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
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

    public function getCurrencyRate(): ?string
    {
        return $this->currencyRate;
    }

    public function setCurrencyRate(string $currencyRate): self
    {
        $this->currencyRate = $currencyRate;

        return $this;
    }

    public function getReferenceDate(): ?\DateTimeInterface
    {
        return $this->referenceDate;
    }

    public function setReferenceDate(?\DateTimeInterface $referenceDate): self
    {
        $this->referenceDate = $referenceDate;

        return $this;
    }

    public function getSupplierInvoiceCodeNumbers(): ?string
    {
        return $this->supplierInvoiceCodeNumbers;
    }

    public function setSupplierInvoiceCodeNumbers(string $supplierInvoiceCodeNumbers): self
    {
        $this->supplierInvoiceCodeNumbers = $supplierInvoiceCodeNumbers;

        return $this;
    }
}
