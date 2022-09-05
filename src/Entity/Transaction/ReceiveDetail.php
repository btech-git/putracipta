<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Product;
use App\Repository\Transaction\ReceiveDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReceiveDetailRepository::class)]
class ReceiveDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $orderedQuantity = null;

    #[ORM\Column]
    private ?int $receivedQuantity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'receiveDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ReceiveHeader $receiveHeader = null;

    #[ORM\ManyToOne(inversedBy: 'receiveDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PurchaseOrderDetail $purchaseOrderDetail = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderedQuantity(): ?int
    {
        return $this->orderedQuantity;
    }

    public function setOrderedQuantity(int $orderedQuantity): self
    {
        $this->orderedQuantity = $orderedQuantity;

        return $this;
    }

    public function getReceivedQuantity(): ?int
    {
        return $this->receivedQuantity;
    }

    public function setReceivedQuantity(int $receivedQuantity): self
    {
        $this->receivedQuantity = $receivedQuantity;

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

    public function getReceiveHeader(): ?ReceiveHeader
    {
        return $this->receiveHeader;
    }

    public function setReceiveHeader(?ReceiveHeader $receiveHeader): self
    {
        $this->receiveHeader = $receiveHeader;

        return $this;
    }

    public function getPurchaseOrderDetail(): ?PurchaseOrderDetail
    {
        return $this->purchaseOrderDetail;
    }

    public function setPurchaseOrderDetail(?PurchaseOrderDetail $purchaseOrderDetail): self
    {
        $this->purchaseOrderDetail = $purchaseOrderDetail;

        return $this;
    }
}
