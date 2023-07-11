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

    #[ORM\Column(length: 60)]
    private ?string $variant = '';

    #[ORM\ManyToOne(inversedBy: 'designCodes')]
    private ?Customer $customer = null;

    #[ORM\Column(length: 60)]
    private ?string $color = '';

    #[ORM\Column(length: 60)]
    private ?string $pantone = '';

    #[ORM\Column]
    private ?int $quantityPrinting1 = 0;

    #[ORM\Column]
    private ?int $quantityPrinting2 = 0;

    public function getCodeNumber(): string
    {
        return str_pad($this->customer->getId(), 3, '0', STR_PAD_LEFT) . str_pad($this->product->getId(), 3, '0', STR_PAD_LEFT) . str_pad($this->variant, 3, '0', STR_PAD_LEFT) . $this->version;
    }
    
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

    public function getVariant(): ?string
    {
        return $this->variant;
    }

    public function setVariant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getPantone(): ?string
    {
        return $this->pantone;
    }

    public function setPantone(string $pantone): self
    {
        $this->pantone = $pantone;

        return $this;
    }

    public function getQuantityPrinting1(): ?int
    {
        return $this->quantityPrinting1;
    }

    public function setQuantityPrinting1(int $quantityPrinting1): self
    {
        $this->quantityPrinting1 = $quantityPrinting1;

        return $this;
    }

    public function getQuantityPrinting2(): ?int
    {
        return $this->quantityPrinting2;
    }

    public function setQuantityPrinting2(int $quantityPrinting2): self
    {
        $this->quantityPrinting2 = $quantityPrinting2;

        return $this;
    }
}
