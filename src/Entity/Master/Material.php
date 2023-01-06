<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\MaterialRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialRepository::class)]
#[ORM\Table(name: 'master_material')]
class Material extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $code = '';

    #[ORM\ManyToOne(inversedBy: 'materials')]
    private ?MaterialSubCategory $materialSubCategory = null;

    #[ORM\Column(length: 20)]
    private ?string $thickness = '';

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\Column(length: 60)]
    private ?string $variant = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $weight = '0.00';

    #[ORM\Column]
    private ?int $length = 0;

    #[ORM\Column]
    private ?int $width = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getMaterialSubCategory(): ?MaterialSubCategory
    {
        return $this->materialSubCategory;
    }

    public function setMaterialSubCategory(?MaterialSubCategory $materialSubCategory): self
    {
        $this->materialSubCategory = $materialSubCategory;

        return $this;
    }

    public function getThickness(): ?string
    {
        return $this->thickness;
    }

    public function setThickness(string $thickness): self
    {
        $this->thickness = $thickness;

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getVariant(): ?string
    {
        return $this->variant;
    }

    public function setVariant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }
}
