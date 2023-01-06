<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Product;
use App\Entity\Master\Unit;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\DeliveryDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeliveryDetailRepository::class)]
#[ORM\Table(name: 'transaction_delivery_detail')]
class DeliveryDetail extends TransactionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $orderedQuantity = 0;

    #[ORM\Column]
    private ?int $deliveredQuantity = 0;

    #[ORM\Column(length: 20)]
    private ?string $lotNumber = '';

    #[ORM\Column(length: 60)]
    private ?string $packaging = '';

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'deliveryDetails')]
    private ?DeliveryHeader $deliveryHeader = null;

    #[ORM\ManyToOne(inversedBy: 'deliveryDetails')]
    private ?SaleOrderDetail $saleOrderDetail = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\Column(length: 20)]
    private ?string $fscCode = '';

    #[ORM\OneToMany(mappedBy: 'deliveryDetail', targetEntity: SaleInvoiceDetail::class)]
    private Collection $saleInvoiceDetails;

    #[ORM\OneToMany(mappedBy: 'deliveryDetail', targetEntity: SaleReturnDetail::class)]
    private Collection $saleReturnDetails;

    public function __construct()
    {
        $this->saleInvoiceDetails = new ArrayCollection();
        $this->saleReturnDetails = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->deliveryHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderedQuantity(): ?int
    {
        return $this->orderedQuantity;
    }

    public function setOrderedQuantity(int $orderedQuantity): self
    {
        $this->orderedQuantity = $orderedQuantity;

        return $this;
    }

    public function getDeliveredQuantity(): ?int
    {
        return $this->deliveredQuantity;
    }

    public function setDeliveredQuantity(int $deliveredQuantity): self
    {
        $this->deliveredQuantity = $deliveredQuantity;

        return $this;
    }

    public function getLotNumber(): ?string
    {
        return $this->lotNumber;
    }

    public function setLotNumber(string $lotNumber): self
    {
        $this->lotNumber = $lotNumber;

        return $this;
    }

    public function getPackaging(): ?string
    {
        return $this->packaging;
    }

    public function setPackaging(string $packaging): self
    {
        $this->packaging = $packaging;

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

    public function getDeliveryHeader(): ?DeliveryHeader
    {
        return $this->deliveryHeader;
    }

    public function setDeliveryHeader(?DeliveryHeader $deliveryHeader): self
    {
        $this->deliveryHeader = $deliveryHeader;

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

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getFscCode(): ?string
    {
        return $this->fscCode;
    }

    public function setFscCode(string $fscCode): self
    {
        $this->fscCode = $fscCode;

        return $this;
    }

    /**
     * @return Collection<int, SaleInvoiceDetail>
     */
    public function getSaleInvoiceDetails(): Collection
    {
        return $this->saleInvoiceDetails;
    }

    public function addSaleInvoiceDetail(SaleInvoiceDetail $saleInvoiceDetail): self
    {
        if (!$this->saleInvoiceDetails->contains($saleInvoiceDetail)) {
            $this->saleInvoiceDetails->add($saleInvoiceDetail);
            $saleInvoiceDetail->setDeliveryDetail($this);
        }

        return $this;
    }

    public function removeSaleInvoiceDetail(SaleInvoiceDetail $saleInvoiceDetail): self
    {
        if ($this->saleInvoiceDetails->removeElement($saleInvoiceDetail)) {
            // set the owning side to null (unless already changed)
            if ($saleInvoiceDetail->getDeliveryDetail() === $this) {
                $saleInvoiceDetail->setDeliveryDetail(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SaleReturnDetail>
     */
    public function getSaleReturnDetails(): Collection
    {
        return $this->saleReturnDetails;
    }

    public function addSaleReturnDetail(SaleReturnDetail $saleReturnDetail): self
    {
        if (!$this->saleReturnDetails->contains($saleReturnDetail)) {
            $this->saleReturnDetails->add($saleReturnDetail);
            $saleReturnDetail->setDeliveryDetail($this);
        }

        return $this;
    }

    public function removeSaleReturnDetail(SaleReturnDetail $saleReturnDetail): self
    {
        if ($this->saleReturnDetails->removeElement($saleReturnDetail)) {
            // set the owning side to null (unless already changed)
            if ($saleReturnDetail->getDeliveryDetail() === $this) {
                $saleReturnDetail->setDeliveryDetail(null);
            }
        }

        return $this;
    }
}
