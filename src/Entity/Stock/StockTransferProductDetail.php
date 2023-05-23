<?php

namespace App\Entity\Stock;

use App\Entity\Master\Product;
use App\Entity\Master\Unit;
use App\Entity\StockDetail;
use App\Repository\Stock\StockTransferProductDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockTransferProductDetailRepository::class)]
#[ORM\Table(name: 'stock_stock_transfer_product_detail')]
class StockTransferProductDetail extends StockDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = 0;

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(inversedBy: 'stockTransferProductDetails')]
    private ?StockTransferHeader $stockTransferHeader = null;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->stockTransferHeader->isIsCanceled() ? true : $this->isCanceled;
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

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(string $memo): self
    {
        $this->memo = $memo;

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

    public function getStockTransferHeader(): ?StockTransferHeader
    {
        return $this->stockTransferHeader;
    }

    public function setStockTransferHeader(?StockTransferHeader $stockTransferHeader): self
    {
        $this->stockTransferHeader = $stockTransferHeader;

        return $this;
    }
}
