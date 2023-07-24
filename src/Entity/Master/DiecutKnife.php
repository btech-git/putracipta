<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Entity\Production\MasterOrderHeader;
use App\Repository\Master\DiecutKnifeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiecutKnifeRepository::class)]
#[ORM\Table(name: 'master_diecut_knife')]
class DiecutKnife extends Master
{
    public const LOCATION_BOBST = 'bobst';
    public const LOCATION_PON = 'pon';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $upPerSecondKnife = 0;

    #[ORM\Column]
    private ?int $upPerSecondPrint = 0;

    #[ORM\Column(length: 20)]
    private ?string $printingSize = '';

    #[ORM\ManyToOne(inversedBy: 'diecutKnives')]
    private ?Customer $customer = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = '';

    #[ORM\Column(length: 20)]
    private ?string $version = '';

    #[ORM\OneToMany(mappedBy: 'diecutKnife', targetEntity: MasterOrderHeader::class)]
    private Collection $masterOrderHeaders;

    #[ORM\Column(length: 60)]
    private ?string $code = '';

    #[ORM\Column(length: 60)]
    private ?string $location = '';

    public function __construct()
    {
        $this->masterOrderHeaders = new ArrayCollection();
    }

    public function getCodeNumber(): string
    {
        return str_pad($this->customer->getId(), 3, '0', STR_PAD_LEFT) . $this->code;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUpPerSecondKnife(): ?int
    {
        return $this->upPerSecondKnife;
    }

    public function setUpPerSecondKnife(int $upPerSecondKnife): self
    {
        $this->upPerSecondKnife = $upPerSecondKnife;

        return $this;
    }

    public function getUpPerSecondPrint(): ?int
    {
        return $this->upPerSecondPrint;
    }

    public function setUpPerSecondPrint(int $upPerSecondPrint): self
    {
        $this->upPerSecondPrint = $upPerSecondPrint;

        return $this;
    }

    public function getPrintingSize(): ?string
    {
        return $this->printingSize;
    }

    public function setPrintingSize(string $printingSize): self
    {
        $this->printingSize = $printingSize;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

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
            $masterOrderHeader->setDiecutKnife($this);
        }

        return $this;
    }

    public function removeMasterOrderHeader(MasterOrderHeader $masterOrderHeader): self
    {
        if ($this->masterOrderHeaders->removeElement($masterOrderHeader)) {
            // set the owning side to null (unless already changed)
            if ($masterOrderHeader->getDiecutKnife() === $this) {
                $masterOrderHeader->setDiecutKnife(null);
            }
        }

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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }
}
