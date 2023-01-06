<?php

namespace App\Entity\Stock;

use App\Entity\StockHeader;
use App\Repository\Stock\MaterialReleaseHeaderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialReleaseHeaderRepository::class)]
#[ORM\Table(name: 'stock_material_release_header')]
class MaterialReleaseHeader extends StockHeader
{
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

    public function getCodeNumberConstant(): string
    {
        return 'MRL';
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
}
