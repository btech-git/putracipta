<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\PaperRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaperRepository::class)]
#[ORM\Table(name: 'master_paper')]
class Paper extends Master
{
    public const PRICING_MODE_ASSOCIATION = 'association';
    public const PRICING_MODE_WEIGHT = 'weight';
    public const PRICING_MODE_UNIT = 'unit';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    private ?string $code = '';

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $length = 0;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $width = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    private ?string $weight = '0.00';

    #[ORM\ManyToOne(inversedBy: 'papers')]
    #[Assert\NotNull]
    private ?Unit $unit = null;

    #[ORM\Column(length: 60)]
    private ?string $pricingMode = null;

    #[ORM\Column(length: 60)]
    private ?string $type = null;

    public function getPaperNameSizeCombination() {
        return $this->name . ' ' . $this->length . ' x ' . $this->width . ' x ' . $this->weight;
    }
    
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

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

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

    public function getPricingMode(): ?string
    {
        return $this->pricingMode;
    }

    public function setPricingMode(string $pricingMode): self
    {
        $this->pricingMode = $pricingMode;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
