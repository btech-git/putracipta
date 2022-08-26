<?php

namespace App\Entity\Master;

use App\Entity\Transaction\PurchaseOrderDetail;
use App\Repository\Master\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $code = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    private ?string $size = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $sellingPrice = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $minimumStock = null;

    #[ORM\Column]
    private ?bool $isInactive = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductCategory $productCategory = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Brand $brand = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: PurchaseOrderDetail::class)]
    private Collection $purchaseOrderDetails;

    public function __construct()
    {
        $this->purchaseOrderDetails = new ArrayCollection();
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

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getSellingPrice(): ?string
    {
        return $this->sellingPrice;
    }

    public function setSellingPrice(string $sellingPrice): self
    {
        $this->sellingPrice = $sellingPrice;

        return $this;
    }

    public function getMinimumStock(): ?int
    {
        return $this->minimumStock;
    }

    public function setMinimumStock(int $minimumStock): self
    {
        $this->minimumStock = $minimumStock;

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

    public function getProductCategory(): ?ProductCategory
    {
        return $this->productCategory;
    }

    public function setProductCategory(?ProductCategory $productCategory): self
    {
        $this->productCategory = $productCategory;

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

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseOrderDetail>
     */
    public function getPurchaseOrderDetails(): Collection
    {
        return $this->purchaseOrderDetails;
    }

    public function addPurchaseOrderDetail(PurchaseOrderDetail $purchaseOrderDetail): self
    {
        if (!$this->purchaseOrderDetails->contains($purchaseOrderDetail)) {
            $this->purchaseOrderDetails->add($purchaseOrderDetail);
            $purchaseOrderDetail->setProduct($this);
        }

        return $this;
    }

    public function removePurchaseOrderDetail(PurchaseOrderDetail $purchaseOrderDetail): self
    {
        if ($this->purchaseOrderDetails->removeElement($purchaseOrderDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseOrderDetail->getProduct() === $this) {
                $purchaseOrderDetail->setProduct(null);
            }
        }

        return $this;
    }
}
