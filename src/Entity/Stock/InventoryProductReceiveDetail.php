<?php

namespace App\Entity\Stock;

use App\Entity\Master\Product;
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

    #[ORM\Column]
    private ?int $quantity = 0;

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    #[ORM\ManyToOne(inversedBy: 'inventoryProductReceiveDetails')]
    private ?MasterOrderProductDetail $masterOrderProductDetail = null;

    #[ORM\ManyToOne(inversedBy: 'inventoryProductReceiveDetails')]
    private ?InventoryProductReceiveHeader $inventoryProductReceiveHeader = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->inventoryProductReceiveHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
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
}
