<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Product;
use App\Entity\Master\Unit;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\SaleInvoiceDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SaleInvoiceDetailRepository::class)]
#[ORM\Table(name: 'transaction_sale_invoice_detail')]
class SaleInvoiceDetail extends TransactionDetail
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
    private ?Unit $unit = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'saleInvoiceDetails')]
    #[Assert\NotNull]
    private ?SaleInvoiceHeader $saleInvoiceHeader = null;

    #[ORM\OneToOne(inversedBy: 'saleInvoiceDetail', cascade: ['persist', 'remove'])]
    private ?DeliveryDetail $deliveryDetail = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    private ?string $return_amount = '0.00';

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->saleInvoiceHeader->isIsCanceled() ? true : $this->isCanceled;
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

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

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

    public function getSaleInvoiceHeader(): ?SaleInvoiceHeader
    {
        return $this->saleInvoiceHeader;
    }

    public function setSaleInvoiceHeader(?SaleInvoiceHeader $saleInvoiceHeader): self
    {
        $this->saleInvoiceHeader = $saleInvoiceHeader;

        return $this;
    }

    public function getDeliveryDetail(): ?DeliveryDetail
    {
        return $this->deliveryDetail;
    }

    public function setDeliveryDetail(?DeliveryDetail $deliveryDetail): self
    {
        $this->deliveryDetail = $deliveryDetail;

        return $this;
    }

    public function getReturnAmount(): ?string
    {
        return $this->return_amount;
    }

    public function setReturnAmount(string $return_amount): self
    {
        $this->return_amount = $return_amount;

        return $this;
    }
}
