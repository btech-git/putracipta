<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Product;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\PurchaseReturnDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseReturnDetailRepository::class)]
class PurchaseReturnDetail extends TransactionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $unitPrice = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseReturnDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ReceiveDetail $receiveDetail = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseReturnDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PurchaseReturnHeader $purchaseReturnHeader = null;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

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
}
