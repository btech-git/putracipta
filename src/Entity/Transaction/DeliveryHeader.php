<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Customer;
use App\Entity\Master\Warehouse;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\DeliveryHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeliveryHeaderRepository::class)]
#[ORM\Table(name: 'transaction_delivery_header')]
class DeliveryHeader extends TransactionHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $totalQuantity = 0;

    #[ORM\ManyToOne]
    private ?Customer $customer = null;

    #[ORM\ManyToOne(inversedBy: 'deliveryHeaders')]
    private ?SaleOrderHeader $saleOrderHeader = null;

    #[ORM\ManyToOne]
    private ?Warehouse $warehouse = null;

    #[ORM\OneToMany(mappedBy: 'deliveryHeader', targetEntity: DeliveryDetail::class)]
    private Collection $deliveryDetails;

    #[ORM\Column(length: 100)]
    private ?string $driverName = '';

    #[ORM\Column(length: 100)]
    private ?string $vehicleType = '';

    #[ORM\Column(length: 20)]
    private ?string $vehicleNumber = '';

    #[ORM\OneToMany(mappedBy: 'deliveryHeader', targetEntity: SaleInvoiceHeader::class)]
    private Collection $saleInvoiceHeaders;

    #[ORM\OneToMany(mappedBy: 'deliveryHeader', targetEntity: SaleReturnHeader::class)]
    private Collection $saleReturnHeaders;

    public function __construct()
    {
        $this->deliveryDetails = new ArrayCollection();
        $this->saleInvoiceHeaders = new ArrayCollection();
        $this->saleReturnHeaders = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return 'DLV';
    }

    public function getSyncTotalQuantity(): int
    {
        $totalQuantity = 0;
        foreach ($this->deliveryDetails as $deliveryDetail) {
            if (!$deliveryDetail->isIsCanceled()) {
                $totalQuantity += $deliveryDetail->getDeliveredQuantity();
            }
        }
        return $totalQuantity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalQuantity(): ?int
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(int $totalQuantity): self
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

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

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

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
            $deliveryDetail->setDeliveryHeader($this);
        }

        return $this;
    }

    public function removeDeliveryDetail(DeliveryDetail $deliveryDetail): self
    {
        if ($this->deliveryDetails->removeElement($deliveryDetail)) {
            // set the owning side to null (unless already changed)
            if ($deliveryDetail->getDeliveryHeader() === $this) {
                $deliveryDetail->setDeliveryHeader(null);
            }
        }

        return $this;
    }

    public function getDriverName(): ?string
    {
        return $this->driverName;
    }

    public function setDriverName(string $driverName): self
    {
        $this->driverName = $driverName;

        return $this;
    }

    public function getVehicleType(): ?string
    {
        return $this->vehicleType;
    }

    public function setVehicleType(string $vehicleType): self
    {
        $this->vehicleType = $vehicleType;

        return $this;
    }

    public function getVehicleNumber(): ?string
    {
        return $this->vehicleNumber;
    }

    public function setVehicleNumber(string $vehicleNumber): self
    {
        $this->vehicleNumber = $vehicleNumber;

        return $this;
    }

    /**
     * @return Collection<int, SaleInvoiceHeader>
     */
    public function getSaleInvoiceHeaders(): Collection
    {
        return $this->saleInvoiceHeaders;
    }

    public function addSaleInvoiceHeader(SaleInvoiceHeader $saleInvoiceHeader): self
    {
        if (!$this->saleInvoiceHeaders->contains($saleInvoiceHeader)) {
            $this->saleInvoiceHeaders->add($saleInvoiceHeader);
            $saleInvoiceHeader->setDeliveryHeader($this);
        }

        return $this;
    }

    public function removeSaleInvoiceHeader(SaleInvoiceHeader $saleInvoiceHeader): self
    {
        if ($this->saleInvoiceHeaders->removeElement($saleInvoiceHeader)) {
            // set the owning side to null (unless already changed)
            if ($saleInvoiceHeader->getDeliveryHeader() === $this) {
                $saleInvoiceHeader->setDeliveryHeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SaleReturnHeader>
     */
    public function getSaleReturnHeaders(): Collection
    {
        return $this->saleReturnHeaders;
    }

    public function addSaleReturnHeader(SaleReturnHeader $saleReturnHeader): self
    {
        if (!$this->saleReturnHeaders->contains($saleReturnHeader)) {
            $this->saleReturnHeaders->add($saleReturnHeader);
            $saleReturnHeader->setDeliveryHeader($this);
        }

        return $this;
    }

    public function removeSaleReturnHeader(SaleReturnHeader $saleReturnHeader): self
    {
        if ($this->saleReturnHeaders->removeElement($saleReturnHeader)) {
            // set the owning side to null (unless already changed)
            if ($saleReturnHeader->getDeliveryHeader() === $this) {
                $saleReturnHeader->setDeliveryHeader(null);
            }
        }

        return $this;
    }
}
