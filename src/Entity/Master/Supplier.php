<?php

namespace App\Entity\Master;

use App\Entity\Transaction\PurchaseOrderHeader;
use App\Repository\Master\SupplierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupplierRepository::class)]
class Supplier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $code = null;

    #[ORM\Column(length: 60)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $company = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $address = null;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\Column(length: 60)]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    private ?string $taxNumber = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = null;

    #[ORM\Column]
    private ?bool $isInactive = null;

    #[ORM\ManyToOne(inversedBy: 'suppliers')]
    private ?Account $account = null;

    #[ORM\OneToMany(mappedBy: 'supplier', targetEntity: PurchaseOrderHeader::class)]
    private Collection $purchaseOrderHeaders;

    public function __construct()
    {
        $this->purchaseOrderHeaders = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(string $taxNumber): self
    {
        $this->taxNumber = $taxNumber;

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

    public function isIsInactive(): ?bool
    {
        return $this->isInactive;
    }

    public function setIsInactive(bool $isInactive): self
    {
        $this->isInactive = $isInactive;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseOrderHeader>
     */
    public function getPurchaseOrderHeaders(): Collection
    {
        return $this->purchaseOrderHeaders;
    }

    public function addPurchaseOrderHeader(PurchaseOrderHeader $purchaseOrderHeader): self
    {
        if (!$this->purchaseOrderHeaders->contains($purchaseOrderHeader)) {
            $this->purchaseOrderHeaders->add($purchaseOrderHeader);
            $purchaseOrderHeader->setSupplier($this);
        }

        return $this;
    }

    public function removePurchaseOrderHeader(PurchaseOrderHeader $purchaseOrderHeader): self
    {
        if ($this->purchaseOrderHeaders->removeElement($purchaseOrderHeader)) {
            // set the owning side to null (unless already changed)
            if ($purchaseOrderHeader->getSupplier() === $this) {
                $purchaseOrderHeader->setSupplier(null);
            }
        }

        return $this;
    }
}
