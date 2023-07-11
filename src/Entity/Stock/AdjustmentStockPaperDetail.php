<?php

namespace App\Entity\Stock;

use App\Entity\Master\Paper;
use App\Entity\StockDetail;
use App\Repository\Stock\AdjustmentStockPaperDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdjustmentStockPaperDetailRepository::class)]
#[ORM\Table(name: 'stock_adjustment_stock_paper_detail')]
class AdjustmentStockPaperDetail extends StockDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantityCurrent = 0;

    #[ORM\Column]
    private ?int $quantityAdjustment = 0;

    #[ORM\Column]
    private ?int $quantityDifference = 0;

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\ManyToOne(inversedBy: 'adjustmentStockPaperDetails')]
    private ?AdjustmentStockHeader $adjustmentStockHeader = null;

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->adjustmentStockHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncQuantityDifference(): int
    {
        return $this->quantityAdjustment - $this->quantityCurrent;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantityCurrent(): ?int
    {
        return $this->quantityCurrent;
    }

    public function setQuantityCurrent(int $quantityCurrent): self
    {
        $this->quantityCurrent = $quantityCurrent;

        return $this;
    }

    public function getQuantityAdjustment(): ?int
    {
        return $this->quantityAdjustment;
    }

    public function setQuantityAdjustment(int $quantityAdjustment): self
    {
        $this->quantityAdjustment = $quantityAdjustment;

        return $this;
    }

    public function getQuantityDifference(): ?int
    {
        return $this->quantityDifference;
    }

    public function setQuantityDifference(int $quantityDifference): self
    {
        $this->quantityDifference = $quantityDifference;

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

    public function getAdjustmentStockHeader(): ?AdjustmentStockHeader
    {
        return $this->adjustmentStockHeader;
    }

    public function setAdjustmentStockHeader(?AdjustmentStockHeader $adjustmentStockHeader): self
    {
        $this->adjustmentStockHeader = $adjustmentStockHeader;

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
