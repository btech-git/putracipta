<?php

namespace App\Entity\Purchase;

use App\Entity\Master\Material;
use App\Entity\Master\Paper;
use App\Entity\Master\Unit;
use App\Entity\PurchaseDetail;
use App\Repository\Purchase\PurchaseInvoiceDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseInvoiceDetailRepository::class)]
#[ORM\Table(name: 'purchase_purchase_invoice_detail')]
class PurchaseInvoiceDetail extends PurchaseDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $quantity = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    private ?string $unitPrice = '0.00';

    #[ORM\ManyToOne]
    private ?Material $material = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseInvoiceDetails')]
    #[Assert\NotNull]
    private ?PurchaseInvoiceHeader $purchaseInvoiceHeader = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseInvoiceDetails')]
    private ?ReceiveDetail $receiveDetail = null;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->purchaseInvoiceHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getTotal(): string
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

    public function getPurchaseInvoiceHeader(): ?PurchaseInvoiceHeader
    {
        return $this->purchaseInvoiceHeader;
    }

    public function setPurchaseInvoiceHeader(?PurchaseInvoiceHeader $purchaseInvoiceHeader): self
    {
        $this->purchaseInvoiceHeader = $purchaseInvoiceHeader;

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

    public function getPaper(): ?Paper
    {
        return $this->paper;
    }

    public function setPaper(?Paper $paper): self
    {
        $this->paper = $paper;

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
}
