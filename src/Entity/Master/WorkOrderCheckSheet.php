<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\WorkOrderCheckSheetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderCheckSheetRepository::class)]
#[ORM\Table(name: 'master_work_order_check_sheet')]
class WorkOrderCheckSheet extends Master
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
