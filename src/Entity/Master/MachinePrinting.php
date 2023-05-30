<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\MachinePrintingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MachinePrintingRepository::class)]
#[ORM\Table(name: 'master_machine_printing')]
class MachinePrinting extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
