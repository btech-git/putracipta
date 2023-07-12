<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'master_customer')]
class Customer extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $code = '';

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $company = '';

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $phone = '';

    #[ORM\Column(length: 60)]
    #[Assert\NotNull]
    private ?string $email = '';

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $taxNumber = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull]
    private ?string $note = '';

    #[ORM\ManyToOne(inversedBy: 'customers')]
    private ?Account $account = null;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Product::class)]
    private Collection $products;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull]
    private ?string $addressInvoice = '';

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $paymentTerm = 0;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?bool $isBondedZone = false;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    private ?Currency $currency = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?bool $hasFscCode = false;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery2 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery3 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery4 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery5 = '';

    #[ORM\Column(length: 100)]
    private ?string $name2 = '';

    #[ORM\Column(length: 100)]
    private ?string $name3 = '';

    #[ORM\Column(length: 100)]
    private ?string $name4 = '';

    #[ORM\Column(length: 100)]
    private ?string $name5 = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery1 = '';

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: DiecutKnife::class)]
    private Collection $diecutKnives;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: DesignCode::class)]
    private Collection $designCodes;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: DielineMillar::class)]
    private Collection $dielineMillars;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->diecutKnives = new ArrayCollection();
        $this->designCodes = new ArrayCollection();
        $this->dielineMillars = new ArrayCollection();
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

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

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
            $product->setCustomer($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCustomer() === $this) {
                $product->setCustomer(null);
            }
        }

        return $this;
    }

    public function getAddressInvoice(): ?string
    {
        return $this->addressInvoice;
    }

    public function setAddressInvoice(string $addressInvoice): self
    {
        $this->addressInvoice = $addressInvoice;

        return $this;
    }

    public function getPaymentTerm(): ?int
    {
        return $this->paymentTerm;
    }

    public function setPaymentTerm(int $paymentTerm): self
    {
        $this->paymentTerm = $paymentTerm;

        return $this;
    }

    public function isIsBondedZone(): ?bool
    {
        return $this->isBondedZone;
    }

    public function setIsBondedZone(bool $isBondedZone): self
    {
        $this->isBondedZone = $isBondedZone;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function isHasFscCode(): ?bool
    {
        return $this->hasFscCode;
    }

    public function setHasFscCode(bool $hasFscCode): self
    {
        $this->hasFscCode = $hasFscCode;

        return $this;
    }

    public function getAddressDelivery2(): ?string
    {
        return $this->addressDelivery2;
    }

    public function setAddressDelivery2(string $addressDelivery2): self
    {
        $this->addressDelivery2 = $addressDelivery2;

        return $this;
    }

    public function getAddressDelivery3(): ?string
    {
        return $this->addressDelivery3;
    }

    public function setAddressDelivery3(string $addressDelivery3): self
    {
        $this->addressDelivery3 = $addressDelivery3;

        return $this;
    }

    public function getAddressDelivery4(): ?string
    {
        return $this->addressDelivery4;
    }

    public function setAddressDelivery4(string $addressDelivery4): self
    {
        $this->addressDelivery4 = $addressDelivery4;

        return $this;
    }

    public function getAddressDelivery5(): ?string
    {
        return $this->addressDelivery5;
    }

    public function setAddressDelivery5(string $addressDelivery5): self
    {
        $this->addressDelivery5 = $addressDelivery5;

        return $this;
    }

    public function getName2(): ?string
    {
        return $this->name2;
    }

    public function setName2(string $name2): self
    {
        $this->name2 = $name2;

        return $this;
    }

    public function getName3(): ?string
    {
        return $this->name3;
    }

    public function setName3(string $name3): self
    {
        $this->name3 = $name3;

        return $this;
    }

    public function getName4(): ?string
    {
        return $this->name4;
    }

    public function setName4(string $name4): self
    {
        $this->name4 = $name4;

        return $this;
    }

    public function getName5(): ?string
    {
        return $this->name5;
    }

    public function setName5(string $name5): self
    {
        $this->name5 = $name5;

        return $this;
    }

    public function getAddressDelivery1(): ?string
    {
        return $this->addressDelivery1;
    }

    public function setAddressDelivery1(string $addressDelivery1): self
    {
        $this->addressDelivery1 = $addressDelivery1;

        return $this;
    }

    /**
     * @return Collection<int, DiecutKnife>
     */
    public function getDiecutKnives(): Collection
    {
        return $this->diecutKnives;
    }

    public function addDiecutKnife(DiecutKnife $diecutKnife): self
    {
        if (!$this->diecutKnives->contains($diecutKnife)) {
            $this->diecutKnives->add($diecutKnife);
            $diecutKnife->setCustomer($this);
        }

        return $this;
    }

    public function removeDiecutKnife(DiecutKnife $diecutKnife): self
    {
        if ($this->diecutKnives->removeElement($diecutKnife)) {
            // set the owning side to null (unless already changed)
            if ($diecutKnife->getCustomer() === $this) {
                $diecutKnife->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DesignCode>
     */
    public function getDesignCodes(): Collection
    {
        return $this->designCodes;
    }

    public function addDesignCode(DesignCode $designCode): self
    {
        if (!$this->designCodes->contains($designCode)) {
            $this->designCodes->add($designCode);
            $designCode->setCustomer($this);
        }

        return $this;
    }

    public function removeDesignCode(DesignCode $designCode): self
    {
        if ($this->designCodes->removeElement($designCode)) {
            // set the owning side to null (unless already changed)
            if ($designCode->getCustomer() === $this) {
                $designCode->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DielineMillar>
     */
    public function getDielineMillars(): Collection
    {
        return $this->dielineMillars;
    }

    public function addDielineMillar(DielineMillar $dielineMillar): self
    {
        if (!$this->dielineMillars->contains($dielineMillar)) {
            $this->dielineMillars->add($dielineMillar);
            $dielineMillar->setCustomer($this);
        }

        return $this;
    }

    public function removeDielineMillar(DielineMillar $dielineMillar): self
    {
        if ($this->dielineMillars->removeElement($dielineMillar)) {
            // set the owning side to null (unless already changed)
            if ($dielineMillar->getCustomer() === $this) {
                $dielineMillar->setCustomer(null);
            }
        }

        return $this;
    }
}
