<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\PaymentTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentTypeRepository::class)]
class PaymentType extends Master
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
