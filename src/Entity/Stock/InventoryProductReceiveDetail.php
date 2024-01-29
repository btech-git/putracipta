<?php

namespace App\Entity\Stock;

use App\Entity\Master\Product;
use App\Entity\Sale\SaleOrderDetail;
use App\Entity\StockDetail;
use App\Entity\Production\MasterOrderProductDetail;
use App\Repository\Stock\InventoryProductReceiveDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryProductReceiveDetailRepository::class)]
#[ORM\Table(name: 'stock_inventory_product_receive_detail')]
class InventoryProductReceiveDetail extends StockDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    #[ORM\ManyToOne(inversedBy: 'inventoryProductReceiveDetails')]
    private ?MasterOrderProductDetail $masterOrderProductDetail = null;

    #[ORM\ManyToOne(inversedBy: 'inventoryProductReceiveDetails')]
    private ?InventoryProductReceiveHeader $inventoryProductReceiveHeader = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'inventoryProductReceiveDetails')]
    private ?SaleOrderDetail $saleOrderDetail = null;

    #[ORM\Column]
    private ?int $quantityBox = 0;

    #[ORM\Column]
    private ?int $quantityBoxExtraPieces = 0;

    #[ORM\Column]
    private ?int $quantityTotalPieces = 0;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->inventoryProductReceiveHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncQuantityTotalPieces()
    {
        $masterOrderHeader = $this->getMasterOrderProductDetail()->getMasterOrderHeader();
        $quantityBoxOrPacking = $masterOrderHeader->getPackagingPaperQuantity() === '0.00' ? $masterOrderHeader->getPackagingBoxQuantity() : $masterOrderHeader->getPackagingPaperQuantity();
        
        return $this->getQuantityBox() * $quantityBoxOrPacking + $this->getQuantityBoxExtraPieces();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(string $memo): self
    {
        $this->memo = $memo;

        return $this;
    }

    public function getMasterOrderProductDetail(): ?MasterOrderProductDetail
    {
        return $this->masterOrderProductDetail;
    }

    public function setMasterOrderProductDetail(?MasterOrderProductDetail $masterOrderProductDetail): self
    {
        $this->masterOrderProductDetail = $masterOrderProductDetail;

        return $this;
    }

    public function getInventoryProductReceiveHeader(): ?InventoryProductReceiveHeader
    {
        return $this->inventoryProductReceiveHeader;
    }

    public function setInventoryProductReceiveHeader(?InventoryProductReceiveHeader $inventoryProductReceiveHeader): self
    {
        $this->inventoryProductReceiveHeader = $inventoryProductReceiveHeader;

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

    public function getSaleOrderDetail(): ?SaleOrderDetail
    {
        return $this->saleOrderDetail;
    }

    public function setSaleOrderDetail(?SaleOrderDetail $saleOrderDetail): self
    {
        $this->saleOrderDetail = $saleOrderDetail;

        return $this;
    }

    public function getQuantityBox(): ?int
    {
        return $this->quantityBox;
    }

    public function setQuantityBox(int $quantityBox): self
    {
        $this->quantityBox = $quantityBox;

        return $this;
    }

    public function getQuantityBoxExtraPieces(): ?int
    {
        return $this->quantityBoxExtraPieces;
    }

    public function setQuantityBoxExtraPieces(int $quantityBoxExtraPieces): self
    {
        $this->quantityBoxExtraPieces = $quantityBoxExtraPieces;

        return $this;
    }

    public function getQuantityTotalPieces(): ?int
    {
        return $this->quantityTotalPieces;
    }

    public function setQuantityTotalPieces(int $quantityTotalPieces): self
    {
        $this->quantityTotalPieces = $quantityTotalPieces;

        return $this;
    }
}
