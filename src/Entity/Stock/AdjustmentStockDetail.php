<?php

namespace App\Entity\Stock;

use App\Entity\Master\Product;
use App\Repository\Stock\AdjustmentStockDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdjustmentStockDetailRepository::class)]
class AdjustmentStockDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $currentQuantity = null;

    #[ORM\Column]
    private ?int $adjustedQuantity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'adjustmentStockDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AdjustmentStockHeader $adjustmentStockHeader = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrentQuantity(): ?int
    {
        return $this->currentQuantity;
    }

    public function setCurrentQuantity(int $currentQuantity): self
    {
        $this->currentQuantity = $currentQuantity;

        return $this;
    }

    public function getAdjustedQuantity(): ?int
    {
        return $this->adjustedQuantity;
    }

    public function setAdjustedQuantity(int $adjustedQuantity): self
    {
        $this->adjustedQuantity = $adjustedQuantity;

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

    public function getAdjustmentStockHeader(): ?AdjustmentStockHeader
    {
        return $this->adjustmentStockHeader;
    }

    public function setAdjustmentStockHeader(?AdjustmentStockHeader $adjustmentStockHeader): self
    {
        $this->adjustmentStockHeader = $adjustmentStockHeader;

        return $this;
    }
}
