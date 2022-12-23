<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Material;
use App\Entity\Master\Unit;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\PurchaseRequestDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseRequestDetailRepository::class)]
#[ORM\Table(name: 'transaction_purchase_request_detail')]
class PurchaseRequestDetail extends TransactionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = 0;

    #[ORM\ManyToOne]
    private ?Material $material = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseRequestDetails')]
    private ?PurchaseRequestHeader $purchaseRequestHeader = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->purchaseRequestHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

        return $this;
    }

    public function getPurchaseRequestHeader(): ?PurchaseRequestHeader
    {
        return $this->purchaseRequestHeader;
    }

    public function setPurchaseRequestHeader(?PurchaseRequestHeader $purchaseRequestHeader): self
    {
        $this->purchaseRequestHeader = $purchaseRequestHeader;

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }
}
