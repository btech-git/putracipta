<?php

namespace App\Entity\Stock;

use App\Entity\Master\Warehouse;
use App\Entity\StockHeader;
use App\Repository\Stock\AdjustmentStockHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdjustmentStockHeaderRepository::class)]
#[ORM\Table(name: 'stock_adjustment_stock_header')]
class AdjustmentStockHeader extends StockHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Warehouse $warehouse = null;

    #[ORM\OneToMany(mappedBy: 'adjustmentStockHeader', targetEntity: AdjustmentStockDetail::class)]
    private Collection $adjustmentStockDetails;

    public function __construct()
    {
        $this->adjustmentStockDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return 'SAJ';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * @return Collection<int, AdjustmentStockDetail>
     */
    public function getAdjustmentStockDetails(): Collection
    {
        return $this->adjustmentStockDetails;
    }

    public function addAdjustmentStockDetail(AdjustmentStockDetail $adjustmentStockDetail): self
    {
        if (!$this->adjustmentStockDetails->contains($adjustmentStockDetail)) {
            $this->adjustmentStockDetails->add($adjustmentStockDetail);
            $adjustmentStockDetail->setAdjustmentStockHeader($this);
        }

        return $this;
    }

    public function removeAdjustmentStockDetail(AdjustmentStockDetail $adjustmentStockDetail): self
    {
        if ($this->adjustmentStockDetails->removeElement($adjustmentStockDetail)) {
            // set the owning side to null (unless already changed)
            if ($adjustmentStockDetail->getAdjustmentStockHeader() === $this) {
                $adjustmentStockDetail->setAdjustmentStockHeader(null);
            }
        }

        return $this;
    }
}
