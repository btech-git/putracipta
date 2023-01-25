<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Product;
use App\Entity\Master\Unit;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\SaleInvoiceDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SaleInvoiceDetailRepository::class)]
#[ORM\Table(name: 'transaction_sale_invoice_detail')]
class SaleInvoiceDetail extends TransactionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    private ?int $quantity = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    private ?string $unitPrice = '0.00';

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(inversedBy: 'saleInvoiceDetails')]
    #[Assert\NotNull]
    private ?DeliveryDetail $deliveryDetail = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'saleInvoiceDetails')]
    #[Assert\NotNull]
    private ?SaleInvoiceHeader $saleInvoiceHeader = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $serviceTaxNominal = '0.00';

    #[ORM\Column]
    private ?bool $isServiceTax = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $serviceTaxPercentage = null;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->saleInvoiceHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getSyncServiceTaxNominal(): string
    {
        return $this->isIsServiceTax() ? $this->unitPrice * $this->serviceTaxPercentage / 100 : 0;
    }

    public function getTotal(): string
    {
        return $this->quantity * $this->unitPrice - $this->serviceTaxNominal;
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

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

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

    public function getDeliveryDetail(): ?DeliveryDetail
    {
        return $this->deliveryDetail;
    }

    public function setDeliveryDetail(?DeliveryDetail $deliveryDetail): self
    {
        $this->deliveryDetail = $deliveryDetail;

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

    public function getSaleInvoiceHeader(): ?SaleInvoiceHeader
    {
        return $this->saleInvoiceHeader;
    }

    public function setSaleInvoiceHeader(?SaleInvoiceHeader $saleInvoiceHeader): self
    {
        $this->saleInvoiceHeader = $saleInvoiceHeader;

        return $this;
    }

    public function getServiceTaxNominal(): ?string
    {
        return $this->serviceTaxNominal;
    }

    public function setServiceTaxNominal(string $serviceTaxNominal): self
    {
        $this->serviceTaxNominal = $serviceTaxNominal;

        return $this;
    }

    public function isIsServiceTax(): ?bool
    {
        return $this->isServiceTax;
    }

    public function setIsServiceTax(bool $isServiceTax): self
    {
        $this->isServiceTax = $isServiceTax;

        return $this;
    }

    public function getServiceTaxPercentage(): ?string
    {
        return $this->serviceTaxPercentage;
    }

    public function setServiceTaxPercentage(string $serviceTaxPercentage): self
    {
        $this->serviceTaxPercentage = $serviceTaxPercentage;

        return $this;
    }
}
