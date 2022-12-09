<?php

namespace App\Entity\Transaction;

use App\Entity\Master\PaymentType;
use App\Entity\Master\Supplier;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\PurchasePaymentHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    #[ORM\JoinColumn(nullable: false)]
    private ?Supplier $supplier = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PaymentType $paymentType = null;

    #[ORM\OneToMany(mappedBy: 'purchasePaymentHeader', targetEntity: PurchasePaymentDetail::class)]
    private Collection $purchasePaymentDetails;

    public function __construct()
    {
        $this->purchasePaymentDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return 'PPY';
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
}
