<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Material;
use App\Entity\Master\Paper;
use App\Entity\Master\Unit;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\ReceiveDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReceiveDetailRepository::class)]
#[ORM\Table(name: 'transaction_receive_detail')]
class ReceiveDetail extends TransactionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $orderedQuantity = 0;

    #[ORM\Column]
    private ?int $receivedQuantity = 0;

    #[ORM\ManyToOne]
    private ?Material $material = null;

    #[ORM\ManyToOne(inversedBy: 'receiveDetails')]
    private ?ReceiveHeader $receiveHeader = null;

    #[ORM\ManyToOne(inversedBy: 'receiveDetails')]
    private ?PurchaseOrderDetail $purchaseOrderDetail = null;

    #[ORM\OneToMany(mappedBy: 'receiveDetail', targetEntity: PurchaseReturnDetail::class)]
    private Collection $purchaseReturnDetails;

    #[ORM\ManyToOne]
    private ?Unit $unit = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $usageDate = null;

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    #[ORM\ManyToOne(inversedBy: 'receiveDetails')]
    private ?PurchaseOrderPaperDetail $purchaseOrderPaperDetail = null;

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    public function __construct()
    {
        $this->purchaseReturnDetails = new ArrayCollection();
    }

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->receiveHeader->isIsCanceled() ? true : $this->isCanceled;
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

    public function getReceivedQuantity(): ?int
    {
        return $this->receivedQuantity;
    }

    public function setReceivedQuantity(int $receivedQuantity): self
    {
        $this->receivedQuantity = $receivedQuantity;

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

    public function getReceiveHeader(): ?ReceiveHeader
    {
        return $this->receiveHeader;
    }

    public function setReceiveHeader(?ReceiveHeader $receiveHeader): self
    {
        $this->receiveHeader = $receiveHeader;

        return $this;
    }

    public function getPurchaseOrderDetail(): ?PurchaseOrderDetail
    {
        return $this->purchaseOrderDetail;
    }

    public function setPurchaseOrderDetail(?PurchaseOrderDetail $purchaseOrderDetail): self
    {
        $this->purchaseOrderDetail = $purchaseOrderDetail;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseReturnDetail>
     */
    public function getPurchaseReturnDetails(): Collection
    {
        return $this->purchaseReturnDetails;
    }

    public function addPurchaseReturnDetail(PurchaseReturnDetail $purchaseReturnDetail): self
    {
        if (!$this->purchaseReturnDetails->contains($purchaseReturnDetail)) {
            $this->purchaseReturnDetails->add($purchaseReturnDetail);
            $purchaseReturnDetail->setReceiveDetail($this);
        }

        return $this;
    }

    public function removePurchaseReturnDetail(PurchaseReturnDetail $purchaseReturnDetail): self
    {
        if ($this->purchaseReturnDetails->removeElement($purchaseReturnDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchaseReturnDetail->getReceiveDetail() === $this) {
                $purchaseReturnDetail->setReceiveDetail(null);
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

    public function getUsageDate(): ?\DateTimeInterface
    {
        return $this->usageDate;
    }

    public function setUsageDate(?\DateTimeInterface $usageDate): self
    {
        $this->usageDate = $usageDate;

        return $this;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(string $memo): self
    {
        $this->memo = $memo;

        return $this;
    }

    public function getPurchaseOrderPaperDetail(): ?PurchaseOrderPaperDetail
    {
        return $this->purchaseOrderPaperDetail;
    }

    public function setPurchaseOrderPaperDetail(?PurchaseOrderPaperDetail $purchaseOrderPaperDetail): self
    {
        $this->purchaseOrderPaperDetail = $purchaseOrderPaperDetail;

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
}
