<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Supplier;
use App\Entity\Master\Warehouse;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\ReceiveHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReceiveHeaderRepository::class)]
#[ORM\Table(name: 'transaction_receive_header')]
class ReceiveHeader extends TransactionHeader
{
    public const CODE_NUMBER_CONSTANT = 'RCV';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $totalQuantity = 0;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    private ?string $supplierDeliveryCodeNumber = '';

    #[ORM\ManyToOne]
    private ?Supplier $supplier = null;

    #[ORM\ManyToOne(inversedBy: 'receiveHeaders')]
    private ?PurchaseOrderHeader $purchaseOrderHeader = null;

    #[ORM\OneToMany(mappedBy: 'receiveHeader', targetEntity: ReceiveDetail::class)]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $receiveDetails;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Warehouse $warehouse = null;

    #[ORM\ManyToOne(inversedBy: 'receiveHeaders')]
    private ?PurchaseOrderPaperHeader $purchaseOrderPaperHeader = null;

    #[ORM\OneToOne(mappedBy: 'receiveHeader', cascade: ['persist', 'remove'])]
    private ?PurchaseReturnHeader $purchaseReturnHeader = null;

    #[ORM\OneToOne(mappedBy: 'receiveHeader', cascade: ['persist', 'remove'])]
    private ?PurchaseInvoiceHeader $purchaseInvoiceHeader = null;

    #[ORM\Column]
    private ?int $purchaseOrderCodeNumberOrdinal = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $purchaseOrderCodeNumberMonth = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $purchaseOrderCodeNumberYear = 0;

    #[ORM\Column]
    private ?int $purchaseOrderId = null;

    public function __construct()
    {
        $this->receiveDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getSyncTotalQuantity(): int
    {
        $totalQuantity = 0;
        foreach ($this->receiveDetails as $receiveDetail) {
            if (!$receiveDetail->isIsCanceled()) {
                $totalQuantity += $receiveDetail->getReceivedQuantity();
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

    public function getSupplierDeliveryCodeNumber(): ?string
    {
        return $this->supplierDeliveryCodeNumber;
    }

    public function setSupplierDeliveryCodeNumber(string $supplierDeliveryCodeNumber): self
    {
        $this->supplierDeliveryCodeNumber = $supplierDeliveryCodeNumber;

        return $this;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;

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
            $receiveDetail->setReceiveHeader($this);
        }

        return $this;
    }

    public function removeReceiveDetail(ReceiveDetail $receiveDetail): self
    {
        if ($this->receiveDetails->removeElement($receiveDetail)) {
            // set the owning side to null (unless already changed)
            if ($receiveDetail->getReceiveHeader() === $this) {
                $receiveDetail->setReceiveHeader(null);
            }
        }

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

    public function getPurchaseOrderPaperHeader(): ?PurchaseOrderPaperHeader
    {
        return $this->purchaseOrderPaperHeader;
    }

    public function setPurchaseOrderPaperHeader(?PurchaseOrderPaperHeader $purchaseOrderPaperHeader): self
    {
        $this->purchaseOrderPaperHeader = $purchaseOrderPaperHeader;

        return $this;
    }

    public function getPurchaseReturnHeader(): ?PurchaseReturnHeader
    {
        return $this->purchaseReturnHeader;
    }

    public function setPurchaseReturnHeader(?PurchaseReturnHeader $purchaseReturnHeader): self
    {
        // unset the owning side of the relation if necessary
        if ($purchaseReturnHeader === null && $this->purchaseReturnHeader !== null) {
            $this->purchaseReturnHeader->setReceiveHeader(null);
        }

        // set the owning side of the relation if necessary
        if ($purchaseReturnHeader !== null && $purchaseReturnHeader->getReceiveHeader() !== $this) {
            $purchaseReturnHeader->setReceiveHeader($this);
        }

        $this->purchaseReturnHeader = $purchaseReturnHeader;

        return $this;
    }

    public function getPurchaseInvoiceHeader(): ?PurchaseInvoiceHeader
    {
        return $this->purchaseInvoiceHeader;
    }

    public function setPurchaseInvoiceHeader(?PurchaseInvoiceHeader $purchaseInvoiceHeader): self
    {
        // unset the owning side of the relation if necessary
        if ($purchaseInvoiceHeader === null && $this->purchaseInvoiceHeader !== null) {
            $this->purchaseInvoiceHeader->setReceiveHeader(null);
        }

        // set the owning side of the relation if necessary
        if ($purchaseInvoiceHeader !== null && $purchaseInvoiceHeader->getReceiveHeader() !== $this) {
            $purchaseInvoiceHeader->setReceiveHeader($this);
        }

        $this->purchaseInvoiceHeader = $purchaseInvoiceHeader;

        return $this;
    }

    public function getPurchaseOrderCodeNumberOrdinal(): ?int
    {
        return $this->purchaseOrderCodeNumberOrdinal;
    }

    public function setPurchaseOrderCodeNumberOrdinal(int $purchaseOrderCodeNumberOrdinal): self
    {
        $this->purchaseOrderCodeNumberOrdinal = $purchaseOrderCodeNumberOrdinal;

        return $this;
    }

    public function getPurchaseOrderCodeNumberMonth(): ?int
    {
        return $this->purchaseOrderCodeNumberMonth;
    }

    public function setPurchaseOrderCodeNumberMonth(int $purchaseOrderCodeNumberMonth): self
    {
        $this->purchaseOrderCodeNumberMonth = $purchaseOrderCodeNumberMonth;

        return $this;
    }

    public function getPurchaseOrderCodeNumberYear(): ?int
    {
        return $this->purchaseOrderCodeNumberYear;
    }

    public function setPurchaseOrderCodeNumberYear(int $purchaseOrderCodeNumberYear): self
    {
        $this->purchaseOrderCodeNumberYear = $purchaseOrderCodeNumberYear;

        return $this;
    }

    public function getPurchaseOrderId(): ?int
    {
        return $this->purchaseOrderId;
    }

    public function setPurchaseOrderId(int $purchaseOrderId): self
    {
        $this->purchaseOrderId = $purchaseOrderId;

        return $this;
    }
}
