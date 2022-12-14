<?php

namespace App\Entity\Transaction;

use App\Entity\Master\Account;
use App\Entity\TransactionDetail;
use App\Repository\Transaction\PurchasePaymentDetailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchasePaymentDetailRepository::class)]
#[ORM\Table(name: 'transaction_purchase_payment_detail')]
class PurchasePaymentDetail extends TransactionDetail
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

    #[ORM\ManyToOne(inversedBy: 'purchasePaymentDetails')]
    private ?PurchaseInvoiceHeader $purchaseInvoiceHeader = null;

    #[ORM\ManyToOne(inversedBy: 'purchasePaymentDetails')]
    private ?PurchasePaymentHeader $purchasePaymentHeader = null;

    public function getSyncIsCanceled(): bool
    {
        $isCanceled = $this->purchasePaymentHeader->isIsCanceled() ? true : $this->isCanceled;
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

    public function getPurchaseInvoiceHeader(): ?PurchaseInvoiceHeader
    {
        return $this->purchaseInvoiceHeader;
    }

    public function setPurchaseInvoiceHeader(?PurchaseInvoiceHeader $purchaseInvoiceHeader): self
    {
        $this->purchaseInvoiceHeader = $purchaseInvoiceHeader;

        return $this;
    }

    public function getPurchasePaymentHeader(): ?PurchasePaymentHeader
    {
        return $this->purchasePaymentHeader;
    }

    public function setPurchasePaymentHeader(?PurchasePaymentHeader $purchasePaymentHeader): self
    {
        $this->purchasePaymentHeader = $purchasePaymentHeader;

        return $this;
    }
}
