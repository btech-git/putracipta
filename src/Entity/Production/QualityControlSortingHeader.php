<?php

namespace App\Entity\Production;

use App\Entity\Master\Customer;
use App\Entity\ProductionHeader;
use App\Repository\Production\QualityControlSortingHeaderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QualityControlSortingHeaderRepository::class)]
#[ORM\Table(name: 'production_quality_control_sorting_header')]
class QualityControlSortingHeader extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'QCS';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?MasterOrderHeader $masterOrderHeader = null;

    #[ORM\ManyToOne]
    private ?Customer $customer = null;

    #[ORM\Column(length: 200)]
    private ?string $employeeInCharge = '';

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMasterOrderHeader(): ?MasterOrderHeader
    {
        return $this->masterOrderHeader;
    }

    public function setMasterOrderHeader(?MasterOrderHeader $masterOrderHeader): self
    {
        $this->masterOrderHeader = $masterOrderHeader;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getEmployeeInCharge(): ?string
    {
        return $this->employeeInCharge;
    }

    public function setEmployeeInCharge(string $employeeInCharge): self
    {
        $this->employeeInCharge = $employeeInCharge;

        return $this;
    }
}
