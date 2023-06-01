<?php

namespace App\Entity\Stock;

use App\Entity\Master\Warehouse;
use App\Entity\StockHeader;
use App\Repository\Stock\InventoryRequestHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryRequestHeaderRepository::class)]
#[ORM\Table(name: 'stock_inventory_request_header')]
class InventoryRequestHeader extends StockHeader
{
    public const CODE_NUMBER_CONSTANT = 'IRQ';
    public const REQUEST_MODE_MATERIAL = 'material';
    public const REQUEST_MODE_PAPER = 'paper';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $totalQuantity = 0;

    #[ORM\Column(length: 100)]
    private ?string $departmentName = '';

    #[ORM\Column(length: 60)]
    private ?string $workOrderNumber = '';

    #[ORM\Column(length: 60)]
    private ?string $partNumber = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $pickupDate = null;

    #[ORM\Column]
    private ?int $totalQuantityRemaining = 0;

    #[ORM\OneToMany(mappedBy: 'inventoryRequestHeader', targetEntity: InventoryRequestPaperDetail::class)]
    private Collection $inventoryRequestPaperDetails;

    #[ORM\OneToMany(mappedBy: 'inventoryRequestHeader', targetEntity: InventoryRequestMaterialDetail::class)]
    private Collection $inventoryRequestMaterialDetails;

    #[ORM\Column(length: 20)]
    private ?string $requestMode = self::REQUEST_MODE_MATERIAL;

    #[ORM\ManyToOne]
    private ?Warehouse $warehouse = null;

    #[ORM\OneToMany(mappedBy: 'inventoryRequestHeader', targetEntity: InventoryReleaseHeader::class)]
    private Collection $inventoryReleaseHeaders;

    public function __construct()
    {
        $this->inventoryRequestPaperDetails = new ArrayCollection();
        $this->inventoryRequestMaterialDetails = new ArrayCollection();
        $this->inventoryReleaseHeaders = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTotalQuantity(): int
    {
        $details = [];
        if ($this->requestMode === self::REQUEST_MODE_MATERIAL) {
            $details = $this->inventoryRequestMaterialDetails;
        } else if ($this->requestMode === self::REQUEST_MODE_PAPER) {
            $details = $this->inventoryRequestPaperDetails;
        }
        $totalQuantity = 0;
        foreach ($details as $detail) {
            if (!$detail->isIsCanceled()) {
                $totalQuantity += $detail->getQuantity();
            }
        }
        return $totalQuantity;
    }

    public function getSyncTotalQuantityRemaining(): int
    {
        $details = [];
        if ($this->requestMode === self::REQUEST_MODE_MATERIAL) {
            $details = $this->materialRequestMaterialDetails;
        } else if ($this->requestMode === self::REQUEST_MODE_PAPER) {
            $details = $this->materialRequestPaperDetails;
        }
        $totalQuantity = 0;
        foreach ($details as $detail) {
            if (!$detail->isIsCanceled()) {
                $totalQuantity += $detail->getQuantityRemaining();
            }
        }
        return $totalQuantity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalQuantity(): ?int
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(int $totalQuantity): self
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
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

    public function getPickupDate(): ?\DateTimeInterface
    {
        return $this->pickupDate;
    }

    public function setPickupDate(?\DateTimeInterface $pickupDate): self
    {
        $this->pickupDate = $pickupDate;

        return $this;
    }

    public function getTotalQuantityRemaining(): ?int
    {
        return $this->totalQuantityRemaining;
    }

    public function setTotalQuantityRemaining(int $totalQuantityRemaining): self
    {
        $this->totalQuantityRemaining = $totalQuantityRemaining;

        return $this;
    }

    /**
     * @return Collection<int, InventoryRequestPaperDetail>
     */
    public function getInventoryRequestPaperDetails(): Collection
    {
        return $this->inventoryRequestPaperDetails;
    }

    public function addInventoryRequestPaperDetail(InventoryRequestPaperDetail $inventoryRequestPaperDetail): self
    {
        if (!$this->inventoryRequestPaperDetails->contains($inventoryRequestPaperDetail)) {
            $this->inventoryRequestPaperDetails->add($inventoryRequestPaperDetail);
            $inventoryRequestPaperDetail->setInventoryRequestHeader($this);
        }

        return $this;
    }

    public function removeInventoryRequestPaperDetail(InventoryRequestPaperDetail $inventoryRequestPaperDetail): self
    {
        if ($this->inventoryRequestPaperDetails->removeElement($inventoryRequestPaperDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryRequestPaperDetail->getInventoryRequestHeader() === $this) {
                $inventoryRequestPaperDetail->setInventoryRequestHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InventoryRequestMaterialDetail>
     */
    public function getInventoryRequestMaterialDetails(): Collection
    {
        return $this->inventoryRequestMaterialDetails;
    }

    public function addInventoryRequestMaterialDetail(InventoryRequestMaterialDetail $inventoryRequestMaterialDetail): self
    {
        if (!$this->inventoryRequestMaterialDetails->contains($inventoryRequestMaterialDetail)) {
            $this->inventoryRequestMaterialDetails->add($inventoryRequestMaterialDetail);
            $inventoryRequestMaterialDetail->setInventoryRequestHeader($this);
        }

        return $this;
    }

    public function removeInventoryRequestMaterialDetail(InventoryRequestMaterialDetail $inventoryRequestMaterialDetail): self
    {
        if ($this->inventoryRequestMaterialDetails->removeElement($inventoryRequestMaterialDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryRequestMaterialDetail->getInventoryRequestHeader() === $this) {
                $inventoryRequestMaterialDetail->setInventoryRequestHeader(null);
            }
        }

        return $this;
    }

    public function getRequestMode(): ?string
    {
        return $this->requestMode;
    }

    public function setRequestMode(string $requestMode): self
    {
        $this->requestMode = $requestMode;

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

    /**
     * @return Collection<int, InventoryReleaseHeader>
     */
    public function getInventoryReleaseHeaders(): Collection
    {
        return $this->inventoryReleaseHeaders;
    }

    public function addInventoryReleaseHeader(InventoryReleaseHeader $inventoryReleaseHeader): self
    {
        if (!$this->inventoryReleaseHeaders->contains($inventoryReleaseHeader)) {
            $this->inventoryReleaseHeaders->add($inventoryReleaseHeader);
            $inventoryReleaseHeader->setInventoryRequestHeader($this);
        }

        return $this;
    }

    public function removeInventoryReleaseHeader(InventoryReleaseHeader $inventoryReleaseHeader): self
    {
        if ($this->inventoryReleaseHeaders->removeElement($inventoryReleaseHeader)) {
            // set the owning side to null (unless already changed)
            if ($inventoryReleaseHeader->getInventoryRequestHeader() === $this) {
                $inventoryReleaseHeader->setInventoryRequestHeader(null);
            }
        }

        return $this;
    }
}
