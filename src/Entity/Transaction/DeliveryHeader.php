<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Customer;
use App\Entity\Master\Employee;
use App\Entity\Master\Transportation;
use App\Entity\Master\Warehouse;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\DeliveryHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $deliveryDetails;

    #[ORM\OneToOne(mappedBy: 'deliveryHeader', cascade: ['persist', 'remove'])]
    private ?SaleReturnHeader $saleReturnHeader = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Transportation $transportation = null;

    #[ORM\ManyToOne]
    private ?Employee $employee = null;

    #[ORM\Column]
    private ?bool $isUsingFscPaper = false;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $deliveryAddress = '';

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

    public function getTransportation(): ?Transportation
    {
        return $this->transportation;
    }

    public function setTransportation(?Transportation $transportation): self
    {
        $this->transportation = $transportation;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): self
    {
        $this->employee = $employee;

        return $this;
    }

    public function isIsUsingFscPaper(): ?bool
    {
        return $this->isUsingFscPaper;
    }

    public function setIsUsingFscPaper(bool $isUsingFscPaper): self
    {
        $this->isUsingFscPaper = $isUsingFscPaper;

        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): self
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }
}
