<?php

namespace App\Entity\Stock;

use App\Entity\StockHeader;
use App\Repository\Stock\MaterialReleaseHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialReleaseHeaderRepository::class)]
#[ORM\Table(name: 'stock_material_release_header')]
class MaterialReleaseHeader extends StockHeader
{
    public const CODE_NUMBER_CONSTANT = 'MRL';
    
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

    #[ORM\ManyToOne(inversedBy: 'materialReleaseHeaders')]
    private ?MaterialRequestHeader $materialRequestHeader = null;

    #[ORM\OneToMany(mappedBy: 'materialReleaseHeader', targetEntity: MaterialReleaseDetail::class)]
    private Collection $materialReleaseDetails;

    public function __construct()
    {
        $this->materialReleaseDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
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
            $materialReleaseDetail->setMaterialReleaseHeader($this);
        }

        return $this;
    }

    public function removeMaterialRequestDetail(MaterialReleaseDetail $materialReleaseDetail): self
    {
        if ($this->materialReleaseDetails->removeElement($materialReleaseDetail)) {
            // set the owning side to null (unless already changed)
            if ($materialReleaseDetail->getMaterialReleaseHeader() === $this) {
                $materialReleaseDetail->setMaterialReleaseHeader(null);
            }
        }

        return $this;
    }

}
