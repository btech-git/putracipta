<?php

namespace App\Entity\Stock;

use App\Entity\Master\Material;
use App\Entity\Master\Unit;
use App\Entity\StockDetail;
use App\Repository\Stock\MaterialRequestDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialRequestDetailRepository::class)]
#[ORM\Table(name: 'stock_material_request_detail')]
class MaterialRequestDetail extends StockDetail
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

    #[ORM\ManyToOne(inversedBy: 'materialRequestDetails')]
    private ?MaterialRequestHeader $materialRequestHeader = null;

    #[ORM\OneToMany(mappedBy: 'materialRequestDetail', targetEntity: MaterialReleaseDetail::class)]
    private Collection $materialReleaseDetails;

    #[ORM\Column]
    private ?int $quantityRelease = 0;

    #[ORM\Column]
    private ?int $quantityRemaining = 0;

    public function __construct()
    {
        $this->materialReleaseDetails = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->materialRequestHeader->isIsCanceled() ? true : $this->isCanceled;
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

    public function getMaterialRequestHeader(): ?MaterialRequestHeader
    {
        return $this->materialRequestHeader;
    }

    public function setMaterialRequestHeader(?MaterialRequestHeader $materialRequestHeader): self
    {
        $this->materialRequestHeader = $materialRequestHeader;

        return $this;
    }

    /**
     * @return Collection<int, MaterialReleaseDetail>
     */
    public function getMaterialReleaseDetails(): Collection
    {
        return $this->materialReleaseDetails;
    }

    public function addMaterialReleaseDetail(MaterialReleaseDetail $materialReleaseDetail): self
    {
        if (!$this->materialReleaseDetails->contains($materialReleaseDetail)) {
            $this->materialReleaseDetails->add($materialReleaseDetail);
            $materialReleaseDetail->setMaterialRequestDetail($this);
        }

        return $this;
    }

    public function removeMaterialReleaseDetail(MaterialReleaseDetail $materialReleaseDetail): self
    {
        if ($this->materialReleaseDetails->removeElement($materialReleaseDetail)) {
            // set the owning side to null (unless already changed)
            if ($materialReleaseDetail->getMaterialRequestDetail() === $this) {
                $materialReleaseDetail->setMaterialRequestDetail(null);
            }
        }

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
}
