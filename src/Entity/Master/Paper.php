<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\PaperRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaperRepository::class)]
#[ORM\Table(name: 'master_paper')]
#[UniqueEntity(['code', 'name'])]
class Paper extends Master
{
    public const PRICING_MODE_ASSOCIATION = 'association';
    public const PRICING_MODE_WEIGHT = 'weight';
    public const PRICING_MODE_UNIT = 'unit';
    public const TYPE_FSC = 'fsc';
    public const TYPE_NON_FSC = 'non';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

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
    private ?string $pricingMode = '';

    #[ORM\Column(length: 60)]
    private ?string $type = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull]
    private ?string $note = '';

    #[ORM\OneToMany(mappedBy: 'paper', targetEntity: Product::class)]
    private Collection $products;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $code = 0;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getPaperNameSizeCombination() 
    {
        return $this->name . ' ' . number_format($this->weight, 0) . ' x ' . $this->length . ' x ' . $this->width;
    }
    
    public function getCodeNumber() 
    {
        $type = ($this->type === self::TYPE_FSC) ? 'FSC' : '000';
        
        return $this->name . '-' . number_format($this->weight, 0) . '-' . $type . '-' . $this->code;
    }
    
    public function getId(): ?int
    {
        return $this->id;
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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setPaper($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getPaper() === $this) {
                $product->setPaper(null);
            }
        }

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }
}
