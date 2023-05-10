<?php

namespace App\Entity\Stock;

use App\Entity\StockHeader;
use App\Entity\Master\Warehouse;
use App\Repository\Stock\StockTransferHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockTransferHeaderRepository::class)]
#[ORM\Table(name: 'stock_stock_transfer_header')]
class StockTransferHeader extends StockHeader
{
    public const CODE_NUMBER_CONSTANT = 'TRF';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'stockTransferHeader', targetEntity: StockTransferDetail::class)]
    private Collection $stockTransferDetails;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalQuantity = null;

    #[ORM\ManyToOne]
    private ?Warehouse $warehouseFrom = null;

    #[ORM\ManyToOne]
    private ?Warehouse $warehouseTo = null;

    public function __construct()
    {
        $this->stockTransferDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTotalQuantity(): int
    {
        $totalQuantity = 0;
        foreach ($this->stockTransferDetails as $stockTransferDetail) {
            if (!$stockTransferDetail->isIsCanceled()) {
                $totalQuantity += $stockTransferDetail->getQuantity();
            }
        }
        return $totalQuantity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, StockTransferDetail>
     */
    public function getStockTransferDetails(): Collection
    {
        return $this->stockTransferDetails;
    }

    public function addStockTransferDetail(StockTransferDetail $stockTransferDetail): self
    {
        if (!$this->stockTransferDetails->contains($stockTransferDetail)) {
            $this->stockTransferDetails->add($stockTransferDetail);
            $stockTransferDetail->setStockTransferHeader($this);
        }

        return $this;
    }

    public function removeStockTransferDetail(StockTransferDetail $stockTransferDetail): self
    {
        if ($this->stockTransferDetails->removeElement($stockTransferDetail)) {
            // set the owning side to null (unless already changed)
            if ($stockTransferDetail->getStockTransferHeader() === $this) {
                $stockTransferDetail->setStockTransferHeader(null);
            }
        }

        return $this;
    }

    public function getTotalQuantity(): ?string
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(string $totalQuantity): self
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }

    public function getWarehouseFrom(): ?Warehouse
    {
        return $this->warehouseFrom;
    }

    public function setWarehouseFrom(?Warehouse $warehouseFrom): self
    {
        $this->warehouseFrom = $warehouseFrom;

        return $this;
    }

    public function getWarehouseTo(): ?Warehouse
    {
        return $this->warehouseTo;
    }

    public function setWarehouseTo(?Warehouse $warehouseTo): self
    {
        $this->warehouseTo = $warehouseTo;

        return $this;
    }
}
