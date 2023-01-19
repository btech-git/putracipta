<?php

namespace App\Entity\Admin;

use App\Repository\Admin\LiteralConfigRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LiteralConfigRepository::class)]
#[ORM\Table(name: 'admin_literal_config')]
class LiteralConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $ifscNumber = null;

    #[ORM\Column]
    private ?int $vatPercentage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIfscNumber(): ?string
    {
        return $this->ifscNumber;
    }

    public function setIfscNumber(string $ifscNumber): self
    {
        $this->ifscNumber = $ifscNumber;

        return $this;
    }

    public function getVatPercentage(): ?int
    {
        return $this->vatPercentage;
    }

    public function setVatPercentage(int $vatPercentage): self
    {
        $this->vatPercentage = $vatPercentage;

        return $this;
    }
}
