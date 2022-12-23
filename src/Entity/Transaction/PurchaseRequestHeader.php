<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Warehouse;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\PurchaseRequestHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseRequestHeaderRepository::class)]
#[ORM\Table(name: 'transaction_purchase_request_header')]
class PurchaseRequestHeader extends TransactionHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $totalQuantity = null;

    #[ORM\ManyToOne]
    private ?Warehouse $warehouse = null;

    #[ORM\OneToMany(mappedBy: 'purchaseRequestHeader', targetEntity: PurchaseRequestDetail::class)]
    private Collection $purchaseRequestDetails;

    public function __construct()
    {
        $this->purchaseRequestDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return 'PRQ';
    }

    public function getSyncTotalQuantity(): int
    {
        $totalQuantity = 0;
        foreach ($this->purchaseRequestDetails as $purchaseRequestDetail) {
            if (!$purchaseRequestDetail->isIsCanceled()) {
                $totalQuantity += $purchaseRequestDetail->getQuantity();
            }
        }
        return $totalQuantity;
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

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseRequestDetail>
     */
    public function getPurchaseRequestDetails(): Collection
    {
        return $this->purchaseRequestDetails;
    }

    public function addPurchaseRequestDetail(PurchaseRequestDetail $purchaseRequestDetail): self
    {
        if (!$this->purchaseRequestDetails->contains($purchaseRequestDetail)) {
            $this->purchaseRequestDetails->add($purchaseRequestDetail);
            $purchaseRequestDetail->setPurchaseRequestHeader($this);
        }

        return $this;
    }

    public function removePurchaseRequestDetail(PurchaseRequestDetail $purchaseRequestDetail): self
    {
        if ($this->purchaseRequestDetails->removeElement($purchaseRequestDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseRequestDetail->getPurchaseRequestHeader() === $this) {
                $purchaseRequestDetail->setPurchaseRequestHeader(null);
            }
        }

        return $this;
    }
}
