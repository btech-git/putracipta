<?php

namespace App\Entity\Production;

use App\Entity\Master\Product;
use App\Entity\Transaction\SaleOrderDetail;
use App\Entity\ProductionDetail;
use App\Repository\Production\MasterOrderProductDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MasterOrderProductDetailRepository::class)]
#[ORM\Table(name: 'production_master_order_product_detail')]
class MasterOrderProductDetail extends ProductionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\Column]
    private ?int $quantityOrder = 0;

    #[ORM\Column]
    private ?int $quantityStock = 0;

    #[ORM\Column]
    private ?int $quantityShortage = 0;

    #[ORM\ManyToOne(inversedBy: 'masterOrderProductDetails')]
    private ?MasterOrderHeader $masterOrderHeader = null;

    #[ORM\ManyToOne(inversedBy: 'masterOrderProductDetails')]
    private ?SaleOrderDetail $saleOrderDetail = null;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->masterOrderHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncQuantityShortage() 
    {
        return $this->quantityOrder - $this->quantityStock;
    }
    
    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuantityOrder(): ?int
    {
        return $this->quantityOrder;
    }

    public function setQuantityOrder(int $quantityOrder): self
    {
        $this->quantityOrder = $quantityOrder;

        return $this;
    }

    public function getQuantityStock(): ?int
    {
        return $this->quantityStock;
    }

    public function setQuantityStock(int $quantityStock): self
    {
        $this->quantityStock = $quantityStock;

        return $this;
    }

    public function getQuantityShortage(): ?int
    {
        return $this->quantityShortage;
    }

    public function setQuantityShortage(int $quantityShortage): self
    {
        $this->quantityShortage = $quantityShortage;

        return $this;
    }

    public function getMasterOrderHeader(): ?MasterOrderHeader
    {
        return $this->masterOrderHeader;
    }

    public function setMasterOrderHeader(?MasterOrderHeader $masterOrderHeader): self
    {
        $this->masterOrderHeader = $masterOrderHeader;

        return $this;
    }

    public function getSaleOrderDetail(): ?SaleOrderDetail
    {
        return $this->saleOrderDetail;
    }

    public function setSaleOrderDetail(?SaleOrderDetail $saleOrderDetail): self
    {
        $this->saleOrderDetail = $saleOrderDetail;

        return $this;
    }
}
