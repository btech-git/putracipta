<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Customer;
use App\Entity\Master\Warehouse;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\DeliveryHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeliveryHeaderRepository::class)]
#[ORM\Table(name: 'transaction_delivery_header')]
class DeliveryHeader extends TransactionHeader
{
    public const CODE_NUMBER_CONSTANT = 'DLV';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $totalQuantity = 0;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Customer $customer = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Warehouse $warehouse = null;

    #[ORM\OneToMany(mappedBy: 'deliveryHeader', targetEntity: DeliveryDetail::class)]
    private Collection $deliveryDetails;

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $driverName = '';

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $vehicleType = '';

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $vehicleNumber = '';

    #[ORM\OneToOne(mappedBy: 'deliveryHeader', cascade: ['persist', 'remove'])]
    private ?SaleReturnHeader $saleReturnHeader = null;

    public function __construct()
    {
        $this->deliveryDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
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

    public function getSaleReturnHeader(): ?SaleReturnHeader
    {
        return $this->saleReturnHeader;
    }

    public function setSaleReturnHeader(?SaleReturnHeader $saleReturnHeader): self
    {
        // unset the owning side of the relation if necessary
        if ($saleReturnHeader === null && $this->saleReturnHeader !== null) {
            $this->saleReturnHeader->setDeliveryHeader(null);
        }

        // set the owning side of the relation if necessary
        if ($saleReturnHeader !== null && $saleReturnHeader->getDeliveryHeader() !== $this) {
            $saleReturnHeader->setDeliveryHeader($this);
        }

        $this->saleReturnHeader = $saleReturnHeader;

        return $this;
    }
}
