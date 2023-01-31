<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Paper;
use App\Entity\Master\Unit;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\PurchaseOrderPaperDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseOrderPaperDetailRepository::class)]
#[ORM\Table(name: 'transaction_purchase_order_paper_detail')]
class PurchaseOrderPaperDetail extends TransactionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    private ?int $quantity = 0;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $apkiValue = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    private ?string $associationPrice = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    private ?string $weightPrice = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    private ?string $unitPrice = '0.00';

    #[ORM\Column]
    private ?int $totalReceive = 0;

    #[ORM\Column]
    private ?int $totalReturn = 0;

    #[ORM\Column]
    private ?int $remainingReceive = 0;

    #[ORM\ManyToOne(inversedBy: 'purchaseOrderPaperDetails')]
    #[Assert\NotNull]
    private ?PurchaseOrderPaperHeader $purchaseOrderPaperHeader = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Unit $unit = null;

    #[ORM\OneToMany(mappedBy: 'purchaseOrderPaperDetail', targetEntity: ReceiveDetail::class)]
    private Collection $receiveDetails;

    #[ORM\Column]
    private ?int $length = 0;

    #[ORM\Column]
    private ?int $width = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $weight = '0.00';

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Paper $paper = null;

    #[ORM\OneToOne(inversedBy: 'purchaseOrderPaperDetail', cascade: ['persist', 'remove'])]
    private ?PurchaseRequestPaperDetail $purchaseRequestPaperDetail = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotNull]
    private ?\DateTimeInterface $deliveryDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $unitPriceBeforeTax = '0.00';

    public function __construct()
    {
        $this->receiveDetails = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->purchaseOrderPaperHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncRemainingReceive(): int
    {
        return $this->quantity - $this->totalReceive;
    }

    public function getSyncWeightPrice(): string
    {
        return (1 + $this->apkiValue / 100) * $this->associationPrice;
    }

    public function getSyncUnitPrice(): string
    {
        $weight = empty($this->paper) ? 1 : $this->paper->getWeight();
        $length = empty($this->paper) ? 1 : $this->paper->getLength();
        $width = empty($this->paper) ? 1 : $this->paper->getWidth();

        return $weight * $length * $width / 20000 * $this->getWeightPrice();
    }

    public function getSyncUnitPriceBeforeTax(): string
    {
        return $this->purchaseOrderPaperHeader->getTaxMode() === $this->purchaseOrderPaperHeader::TAX_MODE_TAX_INCLUSION ? $this->unitPrice / (1 + $this->purchaseOrderPaperHeader->getTaxPercentage() / 100) : $this->unitPrice;
    }

    public function getTotal(): int
    {
        return $this->quantity * $this->getUnitPriceBeforeTax();
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

    public function getApkiValue(): ?int
    {
        return $this->apkiValue;
    }

    public function setApkiValue(int $apkiValue): self
    {
        $this->apkiValue = $apkiValue;

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

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

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

    public function getPurchaseOrderPaperHeader(): ?PurchaseOrderPaperHeader
    {
        return $this->purchaseOrderPaperHeader;
    }

    public function setPurchaseOrderPaperHeader(?PurchaseOrderPaperHeader $purchaseOrderPaperHeader): self
    {
        $this->purchaseOrderPaperHeader = $purchaseOrderPaperHeader;

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
            $receiveDetail->setPurchaseOrderPaperDetail($this);
        }

        return $this;
    }

    public function removeReceiveDetail(ReceiveDetail $receiveDetail): self
    {
        if ($this->receiveDetails->removeElement($receiveDetail)) {
            // set the owning side to null (unless already changed)
            if ($receiveDetail->getPurchaseOrderPaperDetail() === $this) {
                $receiveDetail->setPurchaseOrderPaperDetail(null);
            }
        }

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
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

    public function getPaper(): ?Paper
    {
        return $this->paper;
    }

    public function setPaper(?Paper $paper): self
    {
        $this->paper = $paper;

        return $this;
    }

    public function getPurchaseRequestPaperDetail(): ?PurchaseRequestPaperDetail
    {
        return $this->purchaseRequestPaperDetail;
    }

    public function setPurchaseRequestPaperDetail(?PurchaseRequestPaperDetail $purchaseRequestPaperDetail): self
    {
        $this->purchaseRequestPaperDetail = $purchaseRequestPaperDetail;

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
}
