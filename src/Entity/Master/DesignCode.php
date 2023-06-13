<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\DesignCodeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DesignCodeRepository::class)]
#[ORM\Table(name: 'master_design_code')]
class DesignCode extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'designCodes')]
    private ?Product $product = null;

    #[ORM\Column(length: 20)]
    private ?string $version = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
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
