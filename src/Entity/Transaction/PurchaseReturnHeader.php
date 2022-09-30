<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Supplier;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\PurchaseReturnHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseReturnHeaderRepository::class)]
class PurchaseReturnHeader extends TransactionHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $shippingFee = null;

    #[ORM\Column(length: 20)]
    private ?string $taxMode = null;

    #[ORM\Column]
    private ?int $taxPercentage = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $taxNominal = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Supplier $supplier = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseReturnHeaders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ReceiveHeader $receiveHeader = null;

    #[ORM\OneToMany(mappedBy: 'purchaseReturnHeader', targetEntity: PurchaseReturnDetail::class)]
    private Collection $purchaseReturnDetails;

    public function __construct()
    {
        $this->purchaseReturnDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setReceiveHeader(?ReceiveHeader $receiveHeader): self
    {
        $this->receiveHeader = $receiveHeader;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseReturnDetail>
     */
    public function getPurchaseReturnDetails(): Collection
    {
        return $this->purchaseReturnDetails;
    }

    public function addPurchaseReturnDetail(PurchaseReturnDetail $purchaseReturnDetail): self
    {
        if (!$this->purchaseReturnDetails->contains($purchaseReturnDetail)) {
            $this->purchaseReturnDetails->add($purchaseReturnDetail);
            $purchaseReturnDetail->setPurchaseReturnHeader($this);
        }

        return $this;
    }

    public function removePurchaseReturnDetail(PurchaseReturnDetail $purchaseReturnDetail): self
    {
        if ($this->purchaseReturnDetails->removeElement($purchaseReturnDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseReturnDetail->getPurchaseReturnHeader() === $this) {
                $purchaseReturnDetail->setPurchaseReturnHeader(null);
            }
        }

        return $this;
    }
}
