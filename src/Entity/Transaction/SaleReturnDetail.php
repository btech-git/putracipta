<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Product;
use App\Entity\Master\Unit;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\SaleReturnDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SaleReturnDetailRepository::class)]
#[ORM\Table(name: 'transaction_sale_return_detail')]
class SaleReturnDetail extends TransactionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    private ?int $quantity = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $unitPrice = '0.00';

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(inversedBy: 'saleReturnDetails')]
    private ?DeliveryDetail $deliveryDetail = null;

    #[ORM\ManyToOne(inversedBy: 'saleReturnDetails')]
    private ?SaleReturnHeader $saleReturnHeader = null;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->saleReturnHeader->isIsCanceled() ? true : $this->isCanceled;
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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

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

    public function getDeliveryDetail(): ?DeliveryDetail
    {
        return $this->deliveryDetail;
    }

    public function setDeliveryDetail(?DeliveryDetail $deliveryDetail): self
    {
        $this->deliveryDetail = $deliveryDetail;

        return $this;
    }

    public function getSaleReturnHeader(): ?SaleReturnHeader
    {
        return $this->saleReturnHeader;
    }

    public function setSaleReturnHeader(?SaleReturnHeader $saleReturnHeader): self
    {
        $this->saleReturnHeader = $saleReturnHeader;

        return $this;
    }
}
