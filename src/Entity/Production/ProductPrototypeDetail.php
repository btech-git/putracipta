<?php

namespace App\Entity\Production;

use App\Entity\Master\DesignCodeProductDetail;
use App\Entity\Master\Product;
use App\Entity\ProductionDetail;
use App\Repository\Production\ProductPrototypeDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductPrototypeDetailRepository::class)]
#[ORM\Table(name: 'production_product_prototype_detail')]
class ProductPrototypeDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productPrototypeDetails')]
    private ?ProductPrototype $productPrototype = null;

    #[ORM\ManyToOne(inversedBy: 'productPrototypeDetails')]
    private ?DesignCodeProductDetail $designCodeProductDetail = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductPrototype(): ?ProductPrototype
    {
        return $this->productPrototype;
    }

    public function setProductPrototype(?ProductPrototype $productPrototype): self
    {
        $this->productPrototype = $productPrototype;

        return $this;
    }

    public function getDesignCodeProductDetail(): ?DesignCodeProductDetail
    {
        return $this->designCodeProductDetail;
    }

    public function setDesignCodeProductDetail(?DesignCodeProductDetail $designCodeProductDetail): self
    {
        $this->designCodeProductDetail = $designCodeProductDetail;

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
}
