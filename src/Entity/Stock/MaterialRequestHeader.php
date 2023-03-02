<?php

namespace App\Entity\Stock;

use App\Entity\StockHeader;
use App\Repository\Stock\MaterialRequestHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialRequestHeaderRepository::class)]
#[ORM\Table(name: 'stock_material_request_header')]
class MaterialRequestHeader extends StockHeader
{
    public const CODE_NUMBER_CONSTANT = 'MRQ';
    
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

    #[ORM\OneToMany(mappedBy: 'materialRequestHeader', targetEntity: MaterialRequestDetail::class)]
    private Collection $materialRequestDetails;

    #[ORM\OneToMany(mappedBy: 'materialRequestHeader', targetEntity: MaterialReleaseHeader::class)]
    private Collection $materialReleaseHeaders;

    #[ORM\Column]
    private ?int $totalQuantityRemaining = null;

    public function __construct()
    {
        $this->materialRequestDetails = new ArrayCollection();
        $this->materialReleaseHeaders = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTotalQuantity(): int
    {
        $totalQuantity = 0;
        foreach ($this->materialRequestDetails as $materialRequestDetail) {
            if (!$materialRequestDetail->isIsCanceled()) {
                $totalQuantity += $materialRequestDetail->getQuantity();
            }
        }
        return $totalQuantity;
    }

    public function getSyncTotalQuantityRemaining(): int
    {
        $totalQuantity = 0;
        foreach ($this->materialRequestDetails as $materialRequestDetail) {
            if (!$materialRequestDetail->isIsCanceled()) {
                $totalQuantity += $materialRequestDetail->getQuantityRemaining();
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

    /**
     * @return Collection<int, MaterialRequestDetail>
     */
    public function getMaterialRequestDetails(): Collection
    {
        return $this->materialRequestDetails;
    }

    public function addMaterialRequestDetail(MaterialRequestDetail $materialRequestDetail): self
    {
        if (!$this->materialRequestDetails->contains($materialRequestDetail)) {
            $this->materialRequestDetails->add($materialRequestDetail);
            $materialRequestDetail->setMaterialRequestHeader($this);
        }

        return $this;
    }

    public function removeMaterialRequestDetail(MaterialRequestDetail $materialRequestDetail): self
    {
        if ($this->materialRequestDetails->removeElement($materialRequestDetail)) {
            // set the owning side to null (unless already changed)
            if ($materialRequestDetail->getMaterialRequestHeader() === $this) {
                $materialRequestDetail->setMaterialRequestHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MaterialReleaseHeader>
     */
    public function getMaterialReleaseHeaders(): Collection
    {
        return $this->materialReleaseHeaders;
    }

    public function addMaterialReleaseHeader(MaterialReleaseHeader $materialReleaseHeader): self
    {
        if (!$this->materialReleaseHeaders->contains($materialReleaseHeader)) {
            $this->materialReleaseHeaders->add($materialReleaseHeader);
            $materialReleaseHeader->setMaterialRequestHeader($this);
        }

        return $this;
    }

    public function removeMaterialReleaseHeader(MaterialReleaseHeader $materialReleaseHeader): self
    {
        if ($this->materialReleaseHeaders->removeElement($materialReleaseHeader)) {
            // set the owning side to null (unless already changed)
            if ($materialReleaseHeader->getMaterialRequestHeader() === $this) {
                $materialReleaseHeader->setMaterialRequestHeader(null);
            }
        }

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
}
