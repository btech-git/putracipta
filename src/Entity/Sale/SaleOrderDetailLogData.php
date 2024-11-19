<?php

namespace App\Entity\Sale;

use App\Entity\Master\Product;
use App\Entity\Master\Unit;
use App\Entity\SaleDetail;
use App\Repository\Sale\SaleOrderDetailLogDataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SaleOrderDetailLogDataRepository::class)]
#[ORM\Table(name: 'sale_sale_order_detail_log_data')]
class SaleOrderDetailLogData extends SaleDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 6)]
    #[Assert\NotBlank]
    #[Assert\Type('numeric')]
    private ?string $unitPrice = '0.00';

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(inversedBy: 'saleOrderDetailLogData')]
    #[Assert\NotNull]
    private ?SaleOrderHeader $saleOrderHeader = null;

    #[ORM\ManyToOne(inversedBy: 'saleOrderDetailLogData')]
    #[Assert\NotNull]
    private ?SaleOrderDetail $saleOrderDetail = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $deliveryDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 6)]
    #[Assert\NotNull]
    #[Assert\Type('numeric')]
    private ?string $unitPriceBeforeTax = '0.00';

    #[ORM\Column]
    private ?bool $isTransactionClosed = false;

    #[ORM\Column]
    private ?int $linePo = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $minimumToleranceQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    private ?string $maximumToleranceQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Type('numeric')]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    private ?string $quantity = '0.00';

    public function getTotal(): string
    {
        if ($this->saleOrderHeader === null) {
            return '0.00';
        }
        return $this->saleOrderHeader->getTaxMode() === $this->saleOrderHeader::TAX_MODE_TAX_INCLUSION ? round($this->quantity * $this->unitPrice / (1 + $this->saleOrderHeader->getTaxPercentage() / 100), 2) : $this->quantity * $this->unitPrice;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSaleOrderHeader(): ?SaleOrderHeader
    {
        return $this->saleOrderHeader;
    }

    public function setSaleOrderHeader(?SaleOrderHeader $saleOrderHeader): self
    {
        $this->saleOrderHeader = $saleOrderHeader;

        return $this;
    }

    public function getSaleOrderDetail(): ?SaleOrderDetail
    {
        return $this->saleOrderDetail;
    }

    public function setSaleOrderDetail(?SaleOrderDetail $saleOrderDetail): self
    {
        $this->saleOrderDetail = $saleOrderDetail;

        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getUnitPriceBeforeTax(): ?string
    {
        return $this->unitPriceBeforeTax;
    }

    public function setUnitPriceBeforeTax(string $unitPriceBeforeTax): self
    {
        $this->unitPriceBeforeTax = $unitPriceBeforeTax;

        return $this;
    }

    public function isIsTransactionClosed(): ?bool
    {
        return $this->isTransactionClosed;
    }

    public function setIsTransactionClosed(bool $isTransactionClosed): self
    {
        $this->isTransactionClosed = $isTransactionClosed;

        return $this;
    }

    public function getLinePo(): ?int
    {
        return $this->linePo;
    }

    public function setLinePo(int $linePo): self
    {
        $this->linePo = $linePo;

        return $this;
    }

    public function getMinimumToleranceQuantity(): ?string
    {
        return $this->minimumToleranceQuantity;
    }

    public function setMinimumToleranceQuantity(string $minimumToleranceQuantity): self
    {
        $this->minimumToleranceQuantity = $minimumToleranceQuantity;

        return $this;
    }

    public function getMaximumToleranceQuantity(): ?string
    {
        return $this->maximumToleranceQuantity;
    }

    public function setMaximumToleranceQuantity(string $maximumToleranceQuantity): self
    {
        $this->maximumToleranceQuantity = $maximumToleranceQuantity;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
