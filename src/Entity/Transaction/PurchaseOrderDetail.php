<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Material;
use App\Entity\Master\Unit;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\PurchaseOrderDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseOrderDetailRepository::class)]
#[ORM\Table(name: 'transaction_purchase_order_detail')]
class PurchaseOrderDetail extends TransactionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $unitPrice = '0.00';

    #[ORM\ManyToOne]
    private ?Material $material = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseOrderDetails')]
    private ?PurchaseOrderHeader $purchaseOrderHeader = null;

    #[ORM\OneToMany(mappedBy: 'purchaseOrderDetail', targetEntity: ReceiveDetail::class)]
    private Collection $receiveDetails;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\Column]
    private ?int $totalReceive = 0;

    #[ORM\Column]
    private ?int $totalReturn = 0;

    #[ORM\Column]
    private ?int $remainingReceive = 0;

    #[ORM\OneToOne(inversedBy: 'purchaseOrderDetail', cascade: ['persist', 'remove'])]
    private ?PurchaseRequestDetail $purchaseRequestDetail = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $associationPrice = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $weightPrice = '0.00';

    #[ORM\Column]
    private ?int $apkiValue = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $length = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $width = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $weight = null;

    public function __construct()
    {
        $this->receiveDetails = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->purchaseOrderHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncRemainingReceive(): int
    {
        return $this->quantity - $this->totalReceive;
    }

    public function getWeightPaperPrice(): int
    {
        return empty($this->associationPrice) ? 1.00 : (1 + $this->apkiValue / 100) * $this->associationPrice;
    }

    public function getUnitPaperPrice(): int
    {
        $weight = empty($this->material) ? 1 : $this->material->getWeight();
        $length = empty($this->material) ? 1 : $this->material->getLength();
        $width = empty($this->material) ? 1 : $this->material->getWidth();
        
        return $weight * $length * $width / 20000 * $this->getWeightPaperPrice();
    }

    public function getTotalPaperPrice(): int
    {
        return $this->quantity * $this->getUnitPaperPrice();
    }

    public function getTotal(): int
    {
        return $this->quantity * $this->getUnitPrice();
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

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

        return $this;
    }

    public function getPurchaseOrderHeader(): ?PurchaseOrderHeader
    {
        return $this->purchaseOrderHeader;
    }

    public function setPurchaseOrderHeader(?PurchaseOrderHeader $purchaseOrderHeader): self
    {
        $this->purchaseOrderHeader = $purchaseOrderHeader;

        return $this;
    }

    /**
     * @return Collection<int, ReceiveDetail>
     */
    public function getReceiveDetails(): Collection
    {
        return $this->receiveDetails;
    }

    public function addReceiveDetail(ReceiveDetail $receiveDetail): self
    {
        if (!$this->receiveDetails->contains($receiveDetail)) {
            $this->receiveDetails->add($receiveDetail);
            $receiveDetail->setPurchaseOrderDetail($this);
        }

        return $this;
    }

    public function removeReceiveDetail(ReceiveDetail $receiveDetail): self
    {
        if ($this->receiveDetails->removeElement($receiveDetail)) {
            // set the owning side to null (unless already changed)
            if ($receiveDetail->getPurchaseOrderDetail() === $this) {
                $receiveDetail->setPurchaseOrderDetail(null);
            }
        }

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

    public function getTotalReceive(): ?int
    {
        return $this->totalReceive;
    }

    public function setTotalReceive(int $totalReceive): self
    {
        $this->totalReceive = $totalReceive;

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

    public function getRemainingReceive(): ?int
    {
        return $this->remainingReceive;
    }

    public function setRemainingReceive(int $remainingReceive): self
    {
        $this->remainingReceive = $remainingReceive;

        return $this;
    }

    public function getPurchaseRequestDetail(): ?PurchaseRequestDetail
    {
        return $this->purchaseRequestDetail;
    }

    public function setPurchaseRequestDetail(?PurchaseRequestDetail $purchaseRequestDetail): self
    {
        $this->purchaseRequestDetail = $purchaseRequestDetail;

        return $this;
    }

    public function getAssociationPrice(): ?string
    {
        return $this->associationPrice;
    }

    public function setAssociationPrice(string $associationPrice): self
    {
        $this->associationPrice = $associationPrice;

        return $this;
    }

    public function getWeightPrice(): ?string
    {
        return $this->weightPrice;
    }

    public function setWeightPrice(string $weightPrice): self
    {
        $this->weightPrice = $weightPrice;

        return $this;
    }

    public function getApkiValue(): ?int
    {
        return $this->apkiValue;
    }

    public function setApkiValue(int $apkiValue): self
    {
        $this->apkiValue = $apkiValue;

        return $this;
    }

    public function getLength(): ?string
    {
        return $this->length;
    }

    public function setLength(string $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(string $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }
}
