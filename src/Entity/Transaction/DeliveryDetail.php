<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Product;
use App\Entity\Master\Unit;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\DeliveryDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: DeliveryDetailRepository::class)]
#[ORM\Table(name: 'transaction_delivery_detail')]
class DeliveryDetail extends TransactionDetail
{
    public const FSC_NONE = '';
    public const FSC_A = 'FSC_100%';
    public const FSC_B = 'FSC_Mix_Credit';
    public const FSC_C = 'FSC_Mix_%';
    public const FSC_D = 'FSC_Recycle';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $deliveredQuantity = 0;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $lotNumber = '';

    #[ORM\Column(length: 60)]
    #[Assert\NotNull]
    private ?string $packaging = '';

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'deliveryDetails')]
    #[Assert\NotNull]
    private ?DeliveryHeader $deliveryHeader = null;

    #[ORM\ManyToOne(inversedBy: 'deliveryDetails')]
    #[Assert\NotNull]
    private ?SaleOrderDetail $saleOrderDetail = null;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $fscCode = '';

    #[ORM\OneToMany(mappedBy: 'deliveryDetail', targetEntity: SaleReturnDetail::class)]
    private Collection $saleReturnDetails;

    #[ORM\OneToOne(mappedBy: 'deliveryDetail', cascade: ['persist', 'remove'])]
    private ?SaleInvoiceDetail $saleInvoiceDetail = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $linePO = '';

    #[ORM\Column]
    private ?int $remainingQuantity = null;

    #[ORM\Column]
    private ?int $quantity = null;

    public function __construct()
    {
        $this->saleReturnDetails = new ArrayCollection();
    }

    #[Assert\Callback]
    public function validateQuantityRemaining(ExecutionContextInterface $context, $payload)
    {
        if ($this->deliveryHeader->getId() === null) {
            if ($this->quantity > $this->saleOrderDetail->getRemainingDelivery()) {
                $context->buildViolation('Quantity must be < remaining')->atPath('quantity')->addViolation();
            }
        }
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->deliveryHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncTotalReturn(): int
    {
        $total = 0;
        foreach ($this->saleReturnDetails as $saleReturnDetail) {
            if (!$saleReturnDetail->isIsCanceled()) {
                $total += $saleReturnDetail->getTotal();
            }
        }
        return $total;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSaleInvoiceDetail(): ?SaleInvoiceDetail
    {
        return $this->saleInvoiceDetail;
    }

    public function setSaleInvoiceDetail(?SaleInvoiceDetail $saleInvoiceDetail): self
    {
        // unset the owning side of the relation if necessary
        if ($saleInvoiceDetail === null && $this->saleInvoiceDetail !== null) {
            $this->saleInvoiceDetail->setDeliveryDetail(null);
        }

        // set the owning side of the relation if necessary
        if ($saleInvoiceDetail !== null && $saleInvoiceDetail->getDeliveryDetail() !== $this) {
            $saleInvoiceDetail->setDeliveryDetail($this);
        }

        $this->saleInvoiceDetail = $saleInvoiceDetail;

        return $this;
    }

    public function getLinePO(): ?string
    {
        return $this->linePO;
    }

    public function setLinePO(string $linePO): self
    {
        $this->linePO = $linePO;

        return $this;
    }

    public function getRemainingQuantity(): ?int
    {
        return $this->remainingQuantity;
    }

    public function setRemainingQuantity(int $remainingQuantity): self
    {
        $this->remainingQuantity = $remainingQuantity;

        return $this;
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
}
