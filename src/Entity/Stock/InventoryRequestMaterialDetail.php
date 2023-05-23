<?php

namespace App\Entity\Stock;

use App\Entity\Master\Material;
use App\Entity\Master\Unit;
use App\Entity\StockDetail;
use App\Repository\Stock\InventoryRequestMaterialDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryRequestMaterialDetailRepository::class)]
#[ORM\Table(name: 'stock_inventory_request_material_detail')]
class InventoryRequestMaterialDetail extends StockDetail
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
    private ?Material $material = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\Column]
    private ?int $quantityRelease = 0;

    #[ORM\Column]
    private ?int $quantityRemaining = 0;

    #[ORM\ManyToOne(inversedBy: 'inventoryRequestMaterialDetails')]
    private ?InventoryRequestHeader $inventoryRequestHeader = null;

    #[ORM\OneToMany(mappedBy: 'inventoryRequestMaterialDetail', targetEntity: InventoryReleaseMaterialDetail::class)]
    private Collection $inventoryReleaseMaterialDetails;

    public function __construct()
    {
        $this->inventoryReleaseMaterialDetails = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->inventoryRequestHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncQuantityRemaining(): int
    {
        return $this->quantity - $this->quantityRelease;
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

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

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

    public function getQuantityRelease(): ?int
    {
        return $this->quantityRelease;
    }

    public function setQuantityRelease(int $quantityRelease): self
    {
        $this->quantityRelease = $quantityRelease;

        return $this;
    }

    public function getQuantityRemaining(): ?int
    {
        return $this->quantityRemaining;
    }

    public function setQuantityRemaining(int $quantityRemaining): self
    {
        $this->quantityRemaining = $quantityRemaining;

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
            $inventoryReleaseMaterialDetail->setInventoryRequestMaterialDetail($this);
        }

        return $this;
    }

    public function removeInventoryReleaseMaterialDetail(InventoryReleaseMaterialDetail $inventoryReleaseMaterialDetail): self
    {
        if ($this->inventoryReleaseMaterialDetails->removeElement($inventoryReleaseMaterialDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryReleaseMaterialDetail->getInventoryRequestMaterialDetail() === $this) {
                $inventoryReleaseMaterialDetail->setInventoryRequestMaterialDetail(null);
            }
        }

        return $this;
    }
}
