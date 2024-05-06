<?php

namespace App\Entity\Stock;

use App\Entity\Master\Warehouse;
use App\Entity\StockHeader;
use App\Repository\Stock\InventoryReleaseHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryReleaseHeaderRepository::class)]
#[ORM\Table(name: 'stock_inventory_release_header')]
class InventoryReleaseHeader extends StockHeader
{
    public const CODE_NUMBER_CONSTANT = 'IRL';
    public const RELEASE_MODE_MATERIAL = 'material';
    public const RELEASE_MODE_PAPER = 'paper';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $departmentName = '';

    #[ORM\Column(length: 60)]
    private ?string $workOrderNumber = '';

    #[ORM\Column(length: 60)]
    private ?string $partNumber = '';

    #[ORM\ManyToOne(inversedBy: 'inventoryReleaseHeaders')]
    private ?InventoryRequestHeader $inventoryRequestHeader = null;

    #[ORM\OneToMany(mappedBy: 'inventoryReleaseHeader', targetEntity: InventoryReleaseMaterialDetail::class)]
    private Collection $inventoryReleaseMaterialDetails;

    #[ORM\OneToMany(mappedBy: 'inventoryReleaseHeader', targetEntity: InventoryReleasePaperDetail::class)]
    private Collection $inventoryReleasePaperDetails;

    #[ORM\Column(length: 20)]
    private ?string $releaseMode = self::RELEASE_MODE_MATERIAL;

    #[ORM\ManyToOne]
    private ?Warehouse $warehouse = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalQuantity = '0.00';

    public function __construct()
    {
        $this->inventoryReleaseMaterialDetails = new ArrayCollection();
        $this->inventoryReleasePaperDetails = new ArrayCollection();
    }

    public function getSyncTotalQuantity(): string
    {
        $details = [];
        if ($this->releaseMode === self::RELEASE_MODE_MATERIAL) {
            $details = $this->inventoryReleaseMaterialDetails;
        } else if ($this->releaseMode === self::RELEASE_MODE_PAPER) {
            $details = $this->inventoryReleasePaperDetails;
        }
        $totalQuantity = 0;
        foreach ($details as $detail) {
            if (!$detail->isIsCanceled()) {
                $totalQuantity += $detail->getQuantity();
            }
        }
        return $totalQuantity;
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepartmentName(): ?string
    {
        return $this->departmentName;
    }

    public function setDepartmentName(string $departmentName): self
    {
        $this->departmentName = $departmentName;

        return $this;
    }

    public function getWorkOrderNumber(): ?string
    {
        return $this->workOrderNumber;
    }

    public function setWorkOrderNumber(string $workOrderNumber): self
    {
        $this->workOrderNumber = $workOrderNumber;

        return $this;
    }

    public function getPartNumber(): ?string
    {
        return $this->partNumber;
    }

    public function setPartNumber(string $partNumber): self
    {
        $this->partNumber = $partNumber;

        return $this;
    }

    public function getInventoryRequestHeader(): ?InventoryRequestHeader
    {
        return $this->inventoryRequestHeader;
    }

    public function setInventoryRequestHeader(?InventoryRequestHeader $inventoryRequestHeader): self
    {
        $this->inventoryRequestHeader = $inventoryRequestHeader;

        return $this;
    }

    /**
     * @return Collection<int, InventoryReleaseMaterialDetail>
     */
    public function getInventoryReleaseMaterialDetails(): Collection
    {
        return $this->inventoryReleaseMaterialDetails;
    }

    public function addInventoryReleaseMaterialDetail(InventoryReleaseMaterialDetail $inventoryReleaseMaterialDetail): self
    {
        if (!$this->inventoryReleaseMaterialDetails->contains($inventoryReleaseMaterialDetail)) {
            $this->inventoryReleaseMaterialDetails->add($inventoryReleaseMaterialDetail);
            $inventoryReleaseMaterialDetail->setInventoryReleaseHeader($this);
        }

        return $this;
    }

    public function removeInventoryReleaseMaterialDetail(InventoryReleaseMaterialDetail $inventoryReleaseMaterialDetail): self
    {
        if ($this->inventoryReleaseMaterialDetails->removeElement($inventoryReleaseMaterialDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryReleaseMaterialDetail->getInventoryReleaseHeader() === $this) {
                $inventoryReleaseMaterialDetail->setInventoryReleaseHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InventoryReleasePaperDetail>
     */
    public function getInventoryReleasePaperDetails(): Collection
    {
        return $this->inventoryReleasePaperDetails;
    }

    public function addInventoryReleasePaperDetail(InventoryReleasePaperDetail $inventoryReleasePaperDetail): self
    {
        if (!$this->inventoryReleasePaperDetails->contains($inventoryReleasePaperDetail)) {
            $this->inventoryReleasePaperDetails->add($inventoryReleasePaperDetail);
            $inventoryReleasePaperDetail->setInventoryReleaseHeader($this);
        }

        return $this;
    }

    public function removeInventoryReleasePaperDetail(InventoryReleasePaperDetail $inventoryReleasePaperDetail): self
    {
        if ($this->inventoryReleasePaperDetails->removeElement($inventoryReleasePaperDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryReleasePaperDetail->getInventoryReleaseHeader() === $this) {
                $inventoryReleasePaperDetail->setInventoryReleaseHeader(null);
            }
        }

        return $this;
    }

    public function getReleaseMode(): ?string
    {
        return $this->releaseMode;
    }

    public function setReleaseMode(string $releaseMode): self
    {
        $this->releaseMode = $releaseMode;

        return $this;
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

    public function getTotalQuantity(): ?string
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(string $totalQuantity): self
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }
}
