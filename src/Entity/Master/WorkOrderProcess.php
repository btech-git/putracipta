<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\WorkOrderProcessRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderProcessRepository::class)]
#[ORM\Table(name: 'master_work_order_process')]
class WorkOrderProcess extends Master
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
