<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'master_customer')]
class Customer extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $code = '';

    #[ORM\Column(length: 100)]
    private ?string $company = '';

    #[ORM\Column(length: 20)]
    private ?string $phone = '';

    #[ORM\Column(length: 60)]
    private ?string $email = '';

    #[ORM\Column(length: 20)]
    private ?string $taxNumber = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = '';

    #[ORM\ManyToOne(inversedBy: 'customers')]
    private ?Account $account = null;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Product::class)]
    private Collection $products;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressDelivery = '';

    #[ORM\Column(type: Types::TEXT)]
    private ?string $addressInvoice = '';

    #[ORM\Column]
    private ?int $paymentTerm = 0;

    #[ORM\Column]
    private ?bool $isBondedZone = false;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    private ?Currency $currency = null;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function getAddressDelivery(): ?string
    {
        return $this->addressDelivery;
    }

    public function setAddressDelivery(string $addressDelivery): self
    {
        $this->addressDelivery = $addressDelivery;

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
}
