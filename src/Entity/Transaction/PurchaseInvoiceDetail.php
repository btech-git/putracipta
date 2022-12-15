<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Material;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\PurchaseInvoiceDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseInvoiceDetailRepository::class)]
#[ORM\Table(name: 'transaction_purchase_invoice_detail')]
class PurchaseInvoiceDetail extends TransactionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $unitPrice = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $discount = '0.00';

    #[ORM\ManyToOne]
    private ?Material $material = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ReceiveDetail $receiveDetail = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseInvoiceDetails')]
    private ?PurchaseInvoiceHeader $purchaseInvoiceHeader = null;

    public function sync(): void
    {
        $this->isCanceled = $this->getSyncIsCanceled();
    }

    private function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->purchaseInvoiceHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getTotal(): int
    {
        return $this->quantity * $this->unitPrice * (1 - $this->discount / 100);
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

    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    public function setDiscount(string $discount): self
    {
        $this->discount = $discount;

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

    public function setReceiveDetail(ReceiveDetail $receiveDetail): self
    {
        $this->receiveDetail = $receiveDetail;

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
}
