<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\WorkOrderDistributionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderDistributionRepository::class)]
#[ORM\Table(name: 'master_work_order_distribution')]
class WorkOrderDistribution extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
