<?php

namespace App\Entity\Stock;

use App\Entity\Master\Material;
use App\Entity\Master\Paper;
use App\Entity\Master\Product;
use App\Entity\Master\Unit;
use App\Entity\StockDetail;
use App\Repository\Stock\StockTransferDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockTransferDetailRepository::class)]
#[ORM\Table(name: 'stock_stock_transfer_detail')]
class StockTransferDetail extends StockDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $quantity = '0.00';

    #[ORM\ManyToOne(inversedBy: 'stockTransferDetails')]
    private ?StockTransferHeader $stockTransferHeader = null;

    #[ORM\ManyToOne]
    private ?Material $material = null;

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->stockTransferHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStockTransferHeader(): ?StockTransferHeader
    {
        return $this->stockTransferHeader;
    }

    public function setStockTransferHeader(?StockTransferHeader $stockTransferHeader): self
    {
        $this->stockTransferHeader = $stockTransferHeader;

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

    public function getPaper(): ?Paper
    {
        return $this->paper;
    }

    public function setPaper(?Paper $paper): self
    {
        $this->paper = $paper;

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

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(string $memo): self
    {
        $this->memo = $memo;

        return $this;
    }
}
