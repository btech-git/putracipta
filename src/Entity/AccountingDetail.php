<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class AccountingDetail
{
    #[ORM\Column]
    private ?bool $isInactive = null;

    #[ORM\Column(length: 100)]
    private ?string $memo = null;

    public function isIsInactive(): ?bool
    {
        return $this->isInactive;
    }

    public function setIsInactive(bool $isInactive): self
    {
        $this->isInactive = $isInactive;

        return $this;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(string $memo): self
    {
        $this->memo = $memo;

        return $this;
    }
}
