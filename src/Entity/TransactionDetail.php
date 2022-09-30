<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class TransactionDetail
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
