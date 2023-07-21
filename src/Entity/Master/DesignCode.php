<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Entity\Production\MasterOrderHeader;
use App\Repository\Master\DesignCodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(mappedBy: 'designCode', targetEntity: MasterOrderHeader::class)]
    private Collection $masterOrderHeaders;

    #[ORM\Column]
    private ?int $colorQuantity = null;

    #[ORM\Column(length: 20)]
    private ?string $coating = null;

    public function __construct()
    {
        $this->masterOrderHeaders = new ArrayCollection();
    }

    public function getCodeNumber(): string
    {
        return str_pad($this->customer->getId(), 3, '0', STR_PAD_LEFT) . $this->name . str_pad($this->variant, 3, '0', STR_PAD_LEFT) . $this->version;
    }
    
    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, MasterOrderHeader>
     */
    public function getMasterOrderHeaders(): Collection
    {
        return $this->masterOrderHeaders;
    }

    public function addMasterOrderHeader(MasterOrderHeader $masterOrderHeader): self
    {
        if (!$this->masterOrderHeaders->contains($masterOrderHeader)) {
            $this->masterOrderHeaders->add($masterOrderHeader);
            $masterOrderHeader->setDesignCode($this);
        }

        return $this;
    }

    public function removeMasterOrderHeader(MasterOrderHeader $masterOrderHeader): self
    {
        if ($this->masterOrderHeaders->removeElement($masterOrderHeader)) {
            // set the owning side to null (unless already changed)
            if ($masterOrderHeader->getDesignCode() === $this) {
                $masterOrderHeader->setDesignCode(null);
            }
        }

        return $this;
    }

    public function getColorQuantity(): ?int
    {
        return $this->colorQuantity;
    }

    public function setColorQuantity(int $colorQuantity): self
    {
        $this->colorQuantity = $colorQuantity;

        return $this;
    }

    public function getCoating(): ?string
    {
        return $this->coating;
    }

    public function setCoating(string $coating): self
    {
        $this->coating = $coating;

        return $this;
    }
}
