<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Master
{
    #[ORM\Column]
    private ?bool $isInactive = null;

    #[ORM\Column(length: 60)]
    private ?string $name = null;

    public function isIsInactive(): ?bool
    {
        return $this->isInactive;
    }

    public function setIsInactive(bool $isInactive): self
    {
        $this->isInactive = $isInactive;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
