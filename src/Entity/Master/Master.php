<?php

namespace App\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Master
{
    #[ORM\Column]
    private ?bool $isInactive = null;

    public function isIsInactive(): ?bool
    {
        return $this->isInactive;
    }

    public function setIsInactive(bool $isInactive): self
    {
        $this->isInactive = $isInactive;

        return $this;
    }
}
