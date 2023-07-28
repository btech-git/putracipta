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
    public const STATUS_FA = 'fa';
    public const STATUS_NA = 'na';
    
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

    #[ORM\OneToMany(mappedBy: 'designCode', targetEntity: MasterOrderHeader::class)]
    private Collection $masterOrderHeaders;

    #[ORM\Column(length: 20)]
    private ?string $coating = '';

    #[ORM\Column(length: 60)]
    private ?string $code = '';

    #[ORM\Column(length: 60)]
    private ?string $colorSpecial1 = '';

    #[ORM\Column(length: 60)]
    private ?string $colorSpecial2 = '';

    #[ORM\Column(length: 60)]
    private ?string $colorSpecial3 = '';

    #[ORM\Column(length: 60)]
    private ?string $colorSpecial4 = '';

    #[ORM\Column]
    private ?int $printingUpQuantity = 0;

    #[ORM\Column(length: 60)]
    private ?string $printingKrisSize = '';

    #[ORM\Column(length: 60)]
    private ?string $paperCuttingSize = '';

    #[ORM\Column(length: 60)]
    private ?string $paperMountage = '';

    #[ORM\ManyToOne(inversedBy: 'designCodes')]
    private ?DiecutKnife $diecutKnife = null;

    #[ORM\ManyToOne(inversedBy: 'designCodes')]
    private ?DielineMillar $dielineMillar = null;

    #[ORM\Column(length: 60)]
    private ?string $status = '';

    public function __construct()
    {
        $this->masterOrderHeaders = new ArrayCollection();
    }

    public function getCodeNumber(): string
    {
        return str_pad($this->customer->getId(), 3, '0', STR_PAD_LEFT) . '-P' . str_pad($this->code, 3, '0', STR_PAD_LEFT) . '-V' . str_pad($this->variant, 3, '0', STR_PAD_LEFT) . '-R' . $this->version;
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

    public function getCoating(): ?string
    {
        return $this->coating;
    }

    public function setCoating(string $coating): self
    {
        $this->coating = $coating;

        return $this;
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

    public function getColorSpecial1(): ?string
    {
        return $this->colorSpecial1;
    }

    public function setColorSpecial1(string $colorSpecial1): self
    {
        $this->colorSpecial1 = $colorSpecial1;

        return $this;
    }

    public function getColorSpecial2(): ?string
    {
        return $this->colorSpecial2;
    }

    public function setColorSpecial2(string $colorSpecial2): self
    {
        $this->colorSpecial2 = $colorSpecial2;

        return $this;
    }

    public function getColorSpecial3(): ?string
    {
        return $this->colorSpecial3;
    }

    public function setColorSpecial3(string $colorSpecial3): self
    {
        $this->colorSpecial3 = $colorSpecial3;

        return $this;
    }

    public function getColorSpecial4(): ?string
    {
        return $this->colorSpecial4;
    }

    public function setColorSpecial4(string $colorSpecial4): self
    {
        $this->colorSpecial4 = $colorSpecial4;

        return $this;
    }

    public function getPrintingUpQuantity(): ?int
    {
        return $this->printingUpQuantity;
    }

    public function setPrintingUpQuantity(int $printingUpQuantity): self
    {
        $this->printingUpQuantity = $printingUpQuantity;

        return $this;
    }

    public function getPrintingKrisSize(): ?string
    {
        return $this->printingKrisSize;
    }

    public function setPrintingKrisSize(string $printingKrisSize): self
    {
        $this->printingKrisSize = $printingKrisSize;

        return $this;
    }

    public function getPaperCuttingSize(): ?string
    {
        return $this->paperCuttingSize;
    }

    public function setPaperCuttingSize(string $paperCuttingSize): self
    {
        $this->paperCuttingSize = $paperCuttingSize;

        return $this;
    }

    public function getPaperMountage(): ?string
    {
        return $this->paperMountage;
    }

    public function setPaperMountage(string $paperMountage): self
    {
        $this->paperMountage = $paperMountage;

        return $this;
    }

    public function getDiecutKnife(): ?DiecutKnife
    {
        return $this->diecutKnife;
    }

    public function setDiecutKnife(?DiecutKnife $diecutKnife): self
    {
        $this->diecutKnife = $diecutKnife;

        return $this;
    }

    public function getDielineMillar(): ?DielineMillar
    {
        return $this->dielineMillar;
    }

    public function setDielineMillar(?DielineMillar $dielineMillar): self
    {
        $this->dielineMillar = $dielineMillar;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
