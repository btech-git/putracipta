<?php

namespace App\Entity\Production;

use App\Entity\Master\DesignCodeDistributionDetail;
use App\Entity\Master\WorkOrderDistribution;
use App\Entity\ProductionDetail;
use App\Repository\Production\MasterOrderDistributionDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MasterOrderDistributionDetailRepository::class)]
#[ORM\Table(name: 'production_master_order_distribution_detail')]
class MasterOrderDistributionDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'masterOrderDistributionDetails')]
    private ?MasterOrderHeader $masterOrderHeader = null;

    #[ORM\Column]
    private ?bool $isSubcon = false;

    #[ORM\ManyToOne]
    private ?DesignCodeDistributionDetail $designCodeDistributionDetail = null;

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

    public function isIsSubcon(): ?bool
    {
        return $this->isSubcon;
    }

    public function setIsSubcon(bool $isSubcon): self
    {
        $this->isSubcon = $isSubcon;

        return $this;
    }

    public function getDesignCodeDistributionDetail(): ?DesignCodeDistributionDetail
    {
        return $this->designCodeDistributionDetail;
    }

    public function setDesignCodeDistributionDetail(?DesignCodeDistributionDetail $designCodeDistributionDetail): self
    {
        $this->designCodeDistributionDetail = $designCodeDistributionDetail;

        return $this;
    }
}
