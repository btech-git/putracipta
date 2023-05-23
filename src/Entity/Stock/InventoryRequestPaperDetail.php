<?php

namespace App\Entity\Stock;

use App\Entity\Master\Paper;
use App\Entity\Master\Unit;
use App\Entity\StockDetail;
use App\Repository\Stock\InventoryRequestPaperDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryRequestPaperDetailRepository::class)]
#[ORM\Table(name: 'stock_inventory_request_paper_detail')]
class InventoryRequestPaperDetail extends StockDetail
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
    private ?Unit $unit = null;

    #[ORM\Column]
    private ?int $quantityRelease = 0;

    #[ORM\Column]
    private ?int $quantityRemaining = 0;

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\ManyToOne(inversedBy: 'inventoryRequestPaperDetails')]
    private ?InventoryRequestHeader $inventoryRequestHeader = null;

    #[ORM\OneToMany(mappedBy: 'inventoryRequestPaperDetail', targetEntity: InventoryReleasePaperDetail::class)]
    private Collection $inventoryReleasePaperDetails;

    public function __construct()
    {
        $this->inventoryReleasePaperDetails = new ArrayCollection();
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

    public function getPaper(): ?Paper
    {
        return $this->paper;
    }

    public function setPaper(?Paper $paper): self
    {
        $this->paper = $paper;

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
            $inventoryReleasePaperDetail->setInventoryRequestPaperDetail($this);
        }

        return $this;
    }

    public function removeInventoryReleasePaperDetail(InventoryReleasePaperDetail $inventoryReleasePaperDetail): self
    {
        if ($this->inventoryReleasePaperDetails->removeElement($inventoryReleasePaperDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryReleasePaperDetail->getInventoryRequestPaperDetail() === $this) {
                $inventoryReleasePaperDetail->setInventoryRequestPaperDetail(null);
            }
        }

        return $this;
    }
}
