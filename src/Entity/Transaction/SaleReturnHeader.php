<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Customer;
use App\Entity\TransactionHeader;
use App\Repository\Transaction\SaleReturnHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SaleReturnHeaderRepository::class)]
#[ORM\Table(name: 'transaction_sale_return_header')]
class SaleReturnHeader extends TransactionHeader
{
    public const TAX_MODE_NON_TAX = 'non_tax';
    public const TAX_MODE_TAX_EXCLUSION = 'tax_exclusion';
    public const TAX_MODE_TAX_INCLUSION = 'tax_inclusion';
    public const VAT_PERCENTAGE = 11;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    private ?string $taxMode = self::TAX_MODE_NON_TAX;

    #[ORM\Column]
    private ?int $taxPercentage = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $taxNominal = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $subTotal = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $subTotalAfterTaxInclusion = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $grandTotal = '0.00';

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Customer $customer = null;

    #[ORM\ManyToOne(inversedBy: 'saleReturnHeaders')]
    #[Assert\NotNull]
    private ?DeliveryHeader $deliveryHeader = null;

    #[ORM\OneToMany(mappedBy: 'saleReturnHeader', targetEntity: SaleReturnDetail::class)]
    private Collection $saleReturnDetails;

    public function __construct()
    {
        $this->saleReturnDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return 'SRT';
    }

    public function getSyncTaxPercentage(): int
    {
        $taxPercentage = $this->taxMode === self::TAX_MODE_NON_TAX ? 0 : self::VAT_PERCENTAGE;
        return $taxPercentage;
    }

    public function getSyncTaxNominal(): string
    {
        $taxNominal = $this->subTotalAfterTaxInclusion * $this->taxPercentage / 100;
        return $taxNominal;
    }

    public function getSyncSubTotal(): string
    {
        $subTotal = '0.00';
        foreach ($this->saleReturnDetails as $saleReturnDetail) {
            if (!$saleReturnDetail->isIsCanceled()) {
                $subTotal += $saleReturnDetail->getTotal();
            }
        }
        return $subTotal;
    }

    public function getSyncSubTotalAfterTaxInclusion(): string
    {
        $subTotalAfterTaxInclusion = $this->taxMode === self::TAX_MODE_TAX_INCLUSION ? $this->subTotal / (1 + $this->taxPercentage / 100) : $this->subTotal;
        return $subTotalAfterTaxInclusion;
    }

    public function getSyncGrandTotal(): string
    {
        $grandTotal = $this->subTotalAfterTaxInclusion + $this->taxNominal;
        return $grandTotal;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaxMode(): ?string
    {
        return $this->taxMode;
    }

    public function setTaxMode(string $taxMode): self
    {
        $this->taxMode = $taxMode;

        return $this;
    }

    public function getTaxPercentage(): ?int
    {
        return $this->taxPercentage;
    }

    public function setTaxPercentage(int $taxPercentage): self
    {
        $this->taxPercentage = $taxPercentage;

        return $this;
    }

    public function getTaxNominal(): ?string
    {
        return $this->taxNominal;
    }

    public function setTaxNominal(string $taxNominal): self
    {
        $this->taxNominal = $taxNominal;

        return $this;
    }

    public function getSubTotal(): ?string
    {
        return $this->subTotal;
    }

    public function setSubTotal(string $subTotal): self
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    public function getSubTotalAfterTaxInclusion(): ?string
    {
        return $this->subTotalAfterTaxInclusion;
    }

    public function setSubTotalAfterTaxInclusion(string $subTotalAfterTaxInclusion): self
    {
        $this->subTotalAfterTaxInclusion = $subTotalAfterTaxInclusion;

        return $this;
    }

    public function getGrandTotal(): ?string
    {
        return $this->grandTotal;
    }

    public function setGrandTotal(string $grandTotal): self
    {
        $this->grandTotal = $grandTotal;

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

    public function getDeliveryHeader(): ?DeliveryHeader
    {
        return $this->deliveryHeader;
    }

    public function setDeliveryHeader(?DeliveryHeader $deliveryHeader): self
    {
        $this->deliveryHeader = $deliveryHeader;

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
            $saleReturnDetail->setSaleReturnHeader($this);
        }

        return $this;
    }

    public function removeSaleReturnDetail(SaleReturnDetail $saleReturnDetail): self
    {
        if ($this->saleReturnDetails->removeElement($saleReturnDetail)) {
            // set the owning side to null (unless already changed)
            if ($saleReturnDetail->getSaleReturnHeader() === $this) {
                $saleReturnDetail->setSaleReturnHeader(null);
            }
        }

        return $this;
    }
}
