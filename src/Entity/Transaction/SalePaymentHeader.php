<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Customer;
use App\Entity\Master\PaymentType;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\SalePaymentHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SalePaymentHeaderRepository::class)]
#[ORM\Table(name: 'transaction_sale_payment_header')]
class SalePaymentHeader extends TransactionHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $totalAmount = '0.00';

    #[ORM\Column(length: 60)]
    #[Assert\NotNull]
    private ?string $referenceNumber = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $referenceDate = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Customer $customer = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?PaymentType $paymentType = null;

    #[ORM\OneToMany(mappedBy: 'salePaymentHeader', targetEntity: SalePaymentDetail::class)]
    private Collection $salePaymentDetails;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $administrationFee = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $receivedAmount = null;

    public function __construct()
    {
        $this->salePaymentDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return 'SPY';
    }

    public function getSyncTotalAmount(): string
    {
        $totalAmount = '0.00';
        foreach ($this->salePaymentDetails as $salePaymentDetail) {
            if (!$salePaymentDetail->isIsCanceled()) {
                $totalAmount += $salePaymentDetail->getAmount();
            }
        }
        return $totalAmount;
    }

    public function getSyncReceivedAmount(): string
    {
        return $this->totalAmount - $this->administrationFee;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getReferenceNumber(): ?string
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(string $referenceNumber): self
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    public function getReferenceDate(): ?\DateTimeInterface
    {
        return $this->referenceDate;
    }

    public function setReferenceDate(?\DateTimeInterface $referenceDate): self
    {
        $this->referenceDate = $referenceDate;

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
     * @return Collection<int, SalePaymentDetail>
     */
    public function getSalePaymentDetails(): Collection
    {
        return $this->salePaymentDetails;
    }

    public function addSalePaymentDetail(SalePaymentDetail $salePaymentDetail): self
    {
        if (!$this->salePaymentDetails->contains($salePaymentDetail)) {
            $this->salePaymentDetails->add($salePaymentDetail);
            $salePaymentDetail->setSalePaymentHeader($this);
        }

        return $this;
    }

    public function removeSalePaymentDetail(SalePaymentDetail $salePaymentDetail): self
    {
        if ($this->salePaymentDetails->removeElement($salePaymentDetail)) {
            // set the owning side to null (unless already changed)
            if ($salePaymentDetail->getSalePaymentHeader() === $this) {
                $salePaymentDetail->setSalePaymentHeader(null);
            }
        }

        return $this;
    }

    public function getAdministrationFee(): ?string
    {
        return $this->administrationFee;
    }

    public function setAdministrationFee(string $administrationFee): self
    {
        $this->administrationFee = $administrationFee;

        return $this;
    }

    public function getReceivedAmount(): ?string
    {
        return $this->receivedAmount;
    }

    public function setReceivedAmount(string $receivedAmount): self
    {
        $this->receivedAmount = $receivedAmount;

        return $this;
    }
}
