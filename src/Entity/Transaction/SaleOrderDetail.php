<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Product;
use App\Entity\Master\Unit;
use App\Entity\Production\MasterOrder;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\SaleOrderDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SaleOrderDetailRepository::class)]
#[ORM\Table(name: 'transaction_sale_order_detail')]
class SaleOrderDetail extends TransactionDetail
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

    #[ORM\OneToMany(mappedBy: 'saleOrderDetail', targetEntity: MasterOrder::class)]
    private Collection $masterOrders;

    public function __construct()
    {
        $this->deliveryDetails = new ArrayCollection();
        $this->masterOrders = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->saleOrderHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncRemainingDelivery(): int
    {
        return $this->quantity - $this->totalDelivery;
    }

    public function getSyncUnitPriceBeforeTax(): string
    {
        return $this->saleOrderHeader->getTaxMode() === $this->saleOrderHeader::TAX_MODE_TAX_INCLUSION ? $this->unitPrice / (1 + $this->saleOrderHeader->getTaxPercentage() / 100) : $this->unitPrice;
    }

    public function getTotal(): string
    {
        return $this->quantity * $this->unitPriceBeforeTax;
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

    /**
     * @return Collection<int, MasterOrder>
     */
    public function getMasterOrders(): Collection
    {
        return $this->masterOrders;
    }

    public function addMasterOrder(MasterOrder $masterOrder): self
    {
        if (!$this->masterOrders->contains($masterOrder)) {
            $this->masterOrders->add($masterOrder);
            $masterOrder->setSaleOrderDetail($this);
        }

        return $this;
    }

    public function removeMasterOrder(MasterOrder $masterOrder): self
    {
        if ($this->masterOrders->removeElement($masterOrder)) {
            // set the owning side to null (unless already changed)
            if ($masterOrder->getSaleOrderDetail() === $this) {
                $masterOrder->setSaleOrderDetail(null);
            }
        }

        return $this;
    }
}
