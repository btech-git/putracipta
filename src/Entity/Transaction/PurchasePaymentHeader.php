<?php

namespace App\Entity\Transaction;

use App\Entity\Master\PaymentType;
use App\Entity\Master\Supplier;
use App\Repository\Transaction\PurchasePaymentHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchasePaymentHeaderRepository::class)]
class PurchasePaymentHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $transactionDate = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Supplier $supplier = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PaymentType $paymentType = null;

    #[ORM\OneToMany(mappedBy: 'purchasePaymentHeader', targetEntity: PurchasePaymentDetail::class)]
    private Collection $purchasePaymentDetails;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdTransactionDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $modifiedTransactionDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $approvedTransactionDateTime = null;

    public function __construct()
    {
        $this->purchasePaymentDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransactionDate(): ?\DateTimeInterface
    {
        return $this->transactionDate;
    }

    public function setTransactionDate(\DateTimeInterface $transactionDate): self
    {
        $this->transactionDate = $transactionDate;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

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

    public function getPaymentType(): ?PaymentType
    {
        return $this->paymentType;
    }

    public function setPaymentType(?PaymentType $paymentType): self
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * @return Collection<int, PurchasePaymentDetail>
     */
    public function getPurchasePaymentDetails(): Collection
    {
        return $this->purchasePaymentDetails;
    }

    public function addPurchasePaymentDetail(PurchasePaymentDetail $purchasePaymentDetail): self
    {
        if (!$this->purchasePaymentDetails->contains($purchasePaymentDetail)) {
            $this->purchasePaymentDetails->add($purchasePaymentDetail);
            $purchasePaymentDetail->setPurchasePaymentHeader($this);
        }

        return $this;
    }

    public function removePurchasePaymentDetail(PurchasePaymentDetail $purchasePaymentDetail): self
    {
        if ($this->purchasePaymentDetails->removeElement($purchasePaymentDetail)) {
            // set the owning side to null (unless already changed)
            if ($purchasePaymentDetail->getPurchasePaymentHeader() === $this) {
                $purchasePaymentDetail->setPurchasePaymentHeader(null);
            }
        }

        return $this;
    }

    public function getCreatedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->createdTransactionDateTime;
    }

    public function setCreatedTransactionDateTime(\DateTimeInterface $createdTransactionDateTime): self
    {
        $this->createdTransactionDateTime = $createdTransactionDateTime;

        return $this;
    }

    public function getModifiedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->modifiedTransactionDateTime;
    }

    public function setModifiedTransactionDateTime(?\DateTimeInterface $modifiedTransactionDateTime): self
    {
        $this->modifiedTransactionDateTime = $modifiedTransactionDateTime;

        return $this;
    }

    public function getApprovedTransactionDateTime(): ?\DateTimeInterface
    {
        return $this->approvedTransactionDateTime;
    }

    public function setApprovedTransactionDateTime(?\DateTimeInterface $approvedTransactionDateTime): self
    {
        $this->approvedTransactionDateTime = $approvedTransactionDateTime;

        return $this;
    }
}
