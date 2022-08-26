<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Supplier;
use App\Repository\Transaction\PurchaseOrderHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseOrderHeaderRepository::class)]
class PurchaseOrderHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $transactionDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateTimeApproved = null;

    #[ORM\Column(length: 20)]
    private ?string $discountType = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $discountValue = null;

    #[ORM\Column]
    private ?bool $isTaxApplicable = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $taxNominal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $shippingFee = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $subTotal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $grandTotal = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseOrderHeaders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Supplier $supplier = null;

    #[ORM\OneToMany(mappedBy: 'purchaseOrderHeader', targetEntity: PurchaseOrderDetail::class)]
    private Collection $purchaseOrderDetails;

    public function __construct()
    {
        $this->purchaseOrderDetails = new ArrayCollection();
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

    public function getDateTimeApproved(): ?\DateTimeInterface
    {
        return $this->dateTimeApproved;
    }

    public function setDateTimeApproved(?\DateTimeInterface $dateTimeApproved): self
    {
        $this->dateTimeApproved = $dateTimeApproved;

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

    public function getDiscountValue(): ?string
    {
        return $this->discountValue;
    }

    public function setDiscountValue(string $discountValue): self
    {
        $this->discountValue = $discountValue;

        return $this;
    }

    public function isIsTaxApplicable(): ?bool
    {
        return $this->isTaxApplicable;
    }

    public function setIsTaxApplicable(bool $isTaxApplicable): self
    {
        $this->isTaxApplicable = $isTaxApplicable;

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
}
