<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Entity\Production\MasterOrderHeader;
use App\Repository\Master\DielineMillarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DielineMillarRepository::class)]
#[ORM\Table(name: 'master_dieline_millar')]
class DielineMillar extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = 0;

    #[ORM\Column]
    private ?int $quantityUpPrinting = 0;

    #[ORM\Column(length: 60)]
    private ?string $printingLayout = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = '';

    #[ORM\ManyToOne(inversedBy: 'dielineMillars')]
    private ?Customer $customer = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 20)]
    private ?string $version = '';

    #[ORM\OneToMany(mappedBy: 'dielineMillar', targetEntity: MasterOrderHeader::class)]
    private Collection $masterOrderHeaders;

    #[ORM\Column(length: 60)]
    private ?string $code = '';

    public function __construct()
    {
        $this->masterOrderHeaders = new ArrayCollection();
    }

    public function getCodeNumber(): string
    {
        return str_pad($this->customer->getId(), 3, '0', STR_PAD_LEFT) . $this->name;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantityUpPrinting(): ?int
    {
        return $this->quantityUpPrinting;
    }

    public function setQuantityUpPrinting(int $quantityUpPrinting): self
    {
        $this->quantityUpPrinting = $quantityUpPrinting;

        return $this;
    }

    public function getPrintingLayout(): ?string
    {
        return $this->printingLayout;
    }

    public function setPrintingLayout(string $printingLayout): self
    {
        $this->printingLayout = $printingLayout;

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

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

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
            $masterOrderHeader->setDielineMillar($this);
        }

        return $this;
    }

    public function removeMasterOrderHeader(MasterOrderHeader $masterOrderHeader): self
    {
        if ($this->masterOrderHeaders->removeElement($masterOrderHeader)) {
            // set the owning side to null (unless already changed)
            if ($masterOrderHeader->getDielineMillar() === $this) {
                $masterOrderHeader->setDielineMillar(null);
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
}
