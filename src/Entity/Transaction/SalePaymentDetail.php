<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Account;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\SalePaymentDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SalePaymentDetailRepository::class)]
#[ORM\Table(name: 'transaction_sale_payment_detail')]
class SalePaymentDetail extends TransactionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $amount = '0.00';

    #[ORM\Column(length: 100)]
    private ?string $memo = '';

    #[ORM\ManyToOne]
    private ?Account $account = null;

    #[ORM\ManyToOne(inversedBy: 'salePaymentDetails')]
    private ?SaleInvoiceHeader $saleInvoiceHeader = null;

    #[ORM\ManyToOne(inversedBy: 'salePaymentDetails')]
    private ?SalePaymentHeader $salePaymentHeader = null;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->salePaymentHeader->isIsCanceled() ? true : $this->isCanceled;
        return $isCanceled;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

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

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

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

    public function getSalePaymentHeader(): ?SalePaymentHeader
    {
        return $this->salePaymentHeader;
    }

    public function setSalePaymentHeader(?SalePaymentHeader $salePaymentHeader): self
    {
        $this->salePaymentHeader = $salePaymentHeader;

        return $this;
    }
}
