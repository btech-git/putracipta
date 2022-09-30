<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Supplier;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\ReceiveHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReceiveHeaderRepository::class)]
class ReceiveHeader extends TransactionHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $totalQuantity = null;

    #[ORM\Column(length: 60)]
    private ?string $supplierDeliveryCodeNumber = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Supplier $supplier = null;

    #[ORM\ManyToOne(inversedBy: 'receiveHeaders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PurchaseOrderHeader $purchaseOrderHeader = null;

    #[ORM\OneToMany(mappedBy: 'receiveHeader', targetEntity: ReceiveDetail::class)]
    private Collection $receiveDetails;

    #[ORM\OneToMany(mappedBy: 'receiveHeader', targetEntity: PurchaseReturnHeader::class)]
    private Collection $purchaseReturnHeaders;

    public function __construct()
    {
        $this->receiveDetails = new ArrayCollection();
        $this->purchaseReturnHeaders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalQuantity(): ?int
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(int $totalQuantity): self
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }

    public function getSupplierDeliveryCodeNumber(): ?string
    {
        return $this->supplierDeliveryCodeNumber;
    }

    public function setSupplierDeliveryCodeNumber(string $supplierDeliveryCodeNumber): self
    {
        $this->supplierDeliveryCodeNumber = $supplierDeliveryCodeNumber;

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

    public function getPurchaseOrderHeader(): ?PurchaseOrderHeader
    {
        return $this->purchaseOrderHeader;
    }

    public function setPurchaseOrderHeader(?PurchaseOrderHeader $purchaseOrderHeader): self
    {
        $this->purchaseOrderHeader = $purchaseOrderHeader;

        return $this;
    }

    /**
     * @return Collection<int, ReceiveDetail>
     */
    public function getReceiveDetails(): Collection
    {
        return $this->receiveDetails;
    }

    public function addReceiveDetail(ReceiveDetail $receiveDetail): self
    {
        if (!$this->receiveDetails->contains($receiveDetail)) {
            $this->receiveDetails->add($receiveDetail);
            $receiveDetail->setReceiveHeader($this);
        }

        return $this;
    }

    public function removeReceiveDetail(ReceiveDetail $receiveDetail): self
    {
        if ($this->receiveDetails->removeElement($receiveDetail)) {
            // set the owning side to null (unless already changed)
            if ($receiveDetail->getReceiveHeader() === $this) {
                $receiveDetail->setReceiveHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PurchaseReturnHeader>
     */
    public function getPurchaseReturnHeaders(): Collection
    {
        return $this->purchaseReturnHeaders;
    }

    public function addPurchaseReturnHeader(PurchaseReturnHeader $purchaseReturnHeader): self
    {
        if (!$this->purchaseReturnHeaders->contains($purchaseReturnHeader)) {
            $this->purchaseReturnHeaders->add($purchaseReturnHeader);
            $purchaseReturnHeader->setReceiveHeader($this);
        }

        return $this;
    }

    public function removePurchaseReturnHeader(PurchaseReturnHeader $purchaseReturnHeader): self
    {
        if ($this->purchaseReturnHeaders->removeElement($purchaseReturnHeader)) {
            // set the owning side to null (unless already changed)
            if ($purchaseReturnHeader->getReceiveHeader() === $this) {
                $purchaseReturnHeader->setReceiveHeader(null);
            }
        }

        return $this;
    }
}
