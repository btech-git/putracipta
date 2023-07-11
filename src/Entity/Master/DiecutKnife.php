<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\DiecutKnifeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiecutKnifeRepository::class)]
#[ORM\Table(name: 'master_diecut_knife')]
class DiecutKnife extends Master
{
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

    #[ORM\Column]
    private ?bool $isLocationBobst = false;

    #[ORM\Column]
    private ?bool $isLocationPon = false;

    #[ORM\ManyToOne(inversedBy: 'diecutKnives')]
    private ?Customer $customer = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = '';

    #[ORM\ManyToOne(inversedBy: 'diecutKnives')]
    private ?Product $product = null;

    #[ORM\Column(length: 20)]
    private ?string $version = '';

    public function getCodeNumber(): string
    {
        return str_pad($this->customer->getId(), 3, '0', STR_PAD_LEFT) . str_pad($this->product->getId(), 3, '0', STR_PAD_LEFT) . $this->version;
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

    public function isIsLocationBobst(): ?bool
    {
        return $this->isLocationBobst;
    }

    public function setIsLocationBobst(bool $isLocationBobst): self
    {
        $this->isLocationBobst = $isLocationBobst;

        return $this;
    }

    public function isIsLocationPon(): ?bool
    {
        return $this->isLocationPon;
    }

    public function setIsLocationPon(bool $isLocationPon): self
    {
        $this->isLocationPon = $isLocationPon;

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
}
