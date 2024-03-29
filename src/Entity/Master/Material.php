<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\MaterialRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MaterialRepository::class)]
#[ORM\Table(name: 'master_material')]
#[UniqueEntity(['code', 'name'])]
class Material extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    private ?string $code = '';

    #[ORM\ManyToOne(inversedBy: 'materials')]
    #[Assert\NotNull]
    private ?MaterialSubCategory $materialSubCategory = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $thickness = '';

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Unit $unit = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotNull]
    private ?string $variant = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    private ?string $weight = '0.00';

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $length = 0;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $width = 0;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull]
    private ?string $note = '';

    #[ORM\Column(length: 60)]
    private ?string $density = '';

    #[ORM\Column(length: 60)]
    private ?string $viscosity = '';

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getDensity(): ?string
    {
        return $this->density;
    }

    public function setDensity(string $density): self
    {
        $this->density = $density;

        return $this;
    }

    public function getViscosity(): ?string
    {
        return $this->viscosity;
    }

    public function setViscosity(string $viscosity): self
    {
        $this->viscosity = $viscosity;

        return $this;
    }
}
