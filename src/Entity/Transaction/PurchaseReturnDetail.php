<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Material;
use App\Entity\Master\Unit;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\PurchaseReturnDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseReturnDetailRepository::class)]
#[ORM\Table(name: 'transaction_purchase_return_detail')]
class PurchaseReturnDetail extends TransactionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $unitPrice = '0.00';

    #[ORM\ManyToOne]
    private ?Material $material = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseReturnDetails')]
    private ?ReceiveDetail $receiveDetail = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseReturnDetails')]
    private ?PurchaseReturnHeader $purchaseReturnHeader = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->purchaseReturnHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getTotal(): int
    {
        return $this->quantity * $this->unitPrice;
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

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

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

    public function getReceiveDetail(): ?ReceiveDetail
    {
        return $this->receiveDetail;
    }

    public function setReceiveDetail(?ReceiveDetail $receiveDetail): self
    {
        $this->receiveDetail = $receiveDetail;

        return $this;
    }

    public function getPurchaseReturnHeader(): ?PurchaseReturnHeader
    {
        return $this->purchaseReturnHeader;
    }

    public function setPurchaseReturnHeader(?PurchaseReturnHeader $purchaseReturnHeader): self
    {
        $this->purchaseReturnHeader = $purchaseReturnHeader;

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
