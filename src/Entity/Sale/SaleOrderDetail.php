<?php

namespace App\Entity\Sale;

use App\Entity\Master\Product;
use App\Entity\Master\Unit;
use App\Entity\Production\MasterOrderProductDetail;
use App\Entity\SaleDetail;
use App\Entity\Stock\InventoryProductReceiveDetail;
use App\Repository\Sale\SaleOrderDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SaleOrderDetailRepository::class)]
#[ORM\Table(name: 'sale_sale_order_detail')]
class SaleOrderDetail extends SaleDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    private ?int $quantity = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private ?string $unitPrice = '0.00';

    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $totalDelivery = 0;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $totalReturn = 0;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $remainingDelivery = 0;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(inversedBy: 'saleOrderDetails')]
    #[Assert\NotNull]
    private ?SaleOrderHeader $saleOrderHeader = null;

    #[ORM\OneToMany(mappedBy: 'saleOrderDetail', targetEntity: DeliveryDetail::class)]
    private Collection $deliveryDetails;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deliveryDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    private ?string $unitPriceBeforeTax = '0.00';

    #[ORM\Column]
    private ?bool $isTransactionClosed = false;

    #[ORM\OneToMany(mappedBy: 'saleOrderDetail', targetEntity: MasterOrderProductDetail::class)]
    private Collection $masterOrderProductDetails;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $linePo = 0;

    #[ORM\OneToMany(mappedBy: 'saleOrderDetail', targetEntity: InventoryProductReceiveDetail::class)]
    private Collection $inventoryProductReceiveDetails;

    #[ORM\Column]
    private ?int $quantityStock = 0;

    #[ORM\Column]
    private ?int $quantityProduction = 0;

    #[ORM\Column]
    private ?int $quantityProductionRemaining = 0;

    public function __construct()
    {
        $this->deliveryDetails = new ArrayCollection();
        $this->masterOrderProductDetails = new ArrayCollection();
        $this->inventoryProductReceiveDetails = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->saleOrderHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncRemainingDelivery(): int
    {
        return $this->quantity - $this->totalDelivery + $this->totalReturn;
    }
    
    public function getSyncRemainingProduction(): int
    {
        return $this->quantity - $this->quantityProduction;
    }

    public function getSyncUnitPriceBeforeTax(): string
    {
        return $this->saleOrderHeader->getTaxMode() === $this->saleOrderHeader::TAX_MODE_TAX_INCLUSION ? round($this->unitPrice / (1 + $this->saleOrderHeader->getTaxPercentage() / 100), 2) : $this->unitPrice;
    }

    public function getSyncTotalQuantityReturn(): int
    {
        $total = 0;
        
        foreach ($this->getDeliveryDetails() as $deliveryDetail) {
            foreach ($deliveryDetail->getSaleReturnDetails() as $saleReturnDetail) {
                $total += $saleReturnDetail->isIsCanceled() === false ? $saleReturnDetail->getQuantity() : 0;
            }
        }
        
        return $total;
    }

    public function getTotal(): string
    {
        return $this->saleOrderHeader->getTaxMode() === $this->saleOrderHeader::TAX_MODE_TAX_INCLUSION ? round($this->quantity * $this->unitPrice / (1 + $this->saleOrderHeader->getTaxPercentage() / 100), 2) : $this->quantity * $this->unitPrice;
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

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getTotalDelivery(): ?int
    {
        return $this->totalDelivery;
    }

    public function setTotalDelivery(int $totalDelivery): self
    {
        $this->totalDelivery = $totalDelivery;

        return $this;
    }

    public function getTotalReturn(): ?int
    {
        return $this->totalReturn;
    }

    public function setTotalReturn(int $totalReturn): self
    {
        $this->totalReturn = $totalReturn;

        return $this;
    }

    public function getRemainingDelivery(): ?int
    {
        return $this->remainingDelivery;
    }

    public function setRemainingDelivery(int $remainingDelivery): self
    {
        $this->remainingDelivery = $remainingDelivery;

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

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getSaleOrderHeader(): ?SaleOrderHeader
    {
        return $this->saleOrderHeader;
    }

    public function setSaleOrderHeader(?SaleOrderHeader $saleOrderHeader): self
    {
        $this->saleOrderHeader = $saleOrderHeader;

        return $this;
    }

    /**
     * @return Collection<int, DeliveryDetail>
     */
    public function getDeliveryDetails(): Collection
    {
        return $this->deliveryDetails;
    }

    public function addDeliveryDetail(DeliveryDetail $deliveryDetail): self
    {
        if (!$this->deliveryDetails->contains($deliveryDetail)) {
            $this->deliveryDetails->add($deliveryDetail);
            $deliveryDetail->setSaleOrderDetail($this);
        }

        return $this;
    }

    public function removeDeliveryDetail(DeliveryDetail $deliveryDetail): self
    {
        if ($this->deliveryDetails->removeElement($deliveryDetail)) {
            // set the owning side to null (unless already changed)
            if ($deliveryDetail->getSaleOrderDetail() === $this) {
                $deliveryDetail->setSaleOrderDetail(null);
            }
        }

        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(?\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getUnitPriceBeforeTax(): ?string
    {
        return $this->unitPriceBeforeTax;
    }

    public function setUnitPriceBeforeTax(string $unitPriceBeforeTax): self
    {
        $this->unitPriceBeforeTax = $unitPriceBeforeTax;

        return $this;
    }

    public function isIsTransactionClosed(): ?bool
    {
        return $this->isTransactionClosed;
    }

    public function setIsTransactionClosed(bool $isTransactionClosed): self
    {
        $this->isTransactionClosed = $isTransactionClosed;

        return $this;
    }

    /**
     * @return Collection<int, MasterOrderProductDetail>
     */
    public function getMasterOrderProductDetails(): Collection
    {
        return $this->masterOrderProductDetails;
    }

    public function addMasterOrderProductDetail(MasterOrderProductDetail $masterOrderProductDetail): self
    {
        if (!$this->masterOrderProductDetails->contains($masterOrderProductDetail)) {
            $this->masterOrderProductDetails->add($masterOrderProductDetail);
            $masterOrderProductDetail->setSaleOrderDetail($this);
        }

        return $this;
    }

    public function removeMasterOrderProductDetail(MasterOrderProductDetail $masterOrderProductDetail): self
    {
        if ($this->masterOrderProductDetails->removeElement($masterOrderProductDetail)) {
            // set the owning side to null (unless already changed)
            if ($masterOrderProductDetail->getSaleOrderDetail() === $this) {
                $masterOrderProductDetail->setSaleOrderDetail(null);
            }
        }

        return $this;
    }

    public function getLinePo(): ?int
    {
        return $this->linePo;
    }

    public function setLinePo(int $linePo): self
    {
        $this->linePo = $linePo;

        return $this;
    }

    /**
     * @return Collection<int, InventoryProductReceiveDetail>
     */
    public function getInventoryProductReceiveDetails(): Collection
    {
        return $this->inventoryProductReceiveDetails;
    }

    public function addInventoryProductReceiveDetail(InventoryProductReceiveDetail $inventoryProductReceiveDetail): self
    {
        if (!$this->inventoryProductReceiveDetails->contains($inventoryProductReceiveDetail)) {
            $this->inventoryProductReceiveDetails->add($inventoryProductReceiveDetail);
            $inventoryProductReceiveDetail->setSaleDetail($this);
        }

        return $this;
    }

    public function removeInventoryProductReceiveDetail(InventoryProductReceiveDetail $inventoryProductReceiveDetail): self
    {
        if ($this->inventoryProductReceiveDetails->removeElement($inventoryProductReceiveDetail)) {
            // set the owning side to null (unless already changed)
            if ($inventoryProductReceiveDetail->getSaleDetail() === $this) {
                $inventoryProductReceiveDetail->setSaleDetail(null);
            }
        }

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

    public function getQuantityProduction(): ?int
    {
        return $this->quantityProduction;
    }

    public function setQuantityProduction(int $quantityProduction): self
    {
        $this->quantityProduction = $quantityProduction;

        return $this;
    }

    public function getQuantityProductionRemaining(): ?int
    {
        return $this->quantityProductionRemaining;
    }

    public function setQuantityProductionRemaining(int $quantityProductionRemaining): self
    {
        $this->quantityProductionRemaining = $quantityProductionRemaining;

        return $this;
    }
}
