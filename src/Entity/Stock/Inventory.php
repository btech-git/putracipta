<?php

namespace App\Entity\Stock;

use App\Entity\Master\Product;
use App\Repository\Stock\InventoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryRepository::class)]
#[ORM\Table(name: 'stock_inventory')]
class Inventory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $transactionDate = null;

    #[ORM\Column(length: 20)]
    private ?string $transactionType = null;

    #[ORM\Column(length: 100)]
    private ?string $transactionSubject = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $note = null;

    #[ORM\Column]
    private ?int $quantityIn = null;

    #[ORM\Column]
    private ?int $quantityOut = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $purchasePrice = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

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

    public function getTransactionType(): ?string
    {
        return $this->transactionType;
    }

    public function setTransactionType(string $transactionType): self
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    public function getTransactionSubject(): ?string
    {
        return $this->transactionSubject;
    }

    public function setTransactionSubject(string $transactionSubject): self
    {
        $this->transactionSubject = $transactionSubject;

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

    public function getQuantityIn(): ?int
    {
        return $this->quantityIn;
    }

    public function setQuantityIn(int $quantityIn): self
    {
        $this->quantityIn = $quantityIn;

        return $this;
    }

    public function getQuantityOut(): ?int
    {
        return $this->quantityOut;
    }

    public function setQuantityOut(int $quantityOut): self
    {
        $this->quantityOut = $quantityOut;

        return $this;
    }

    public function getPurchasePrice(): ?string
    {
        return $this->purchasePrice;
    }

    public function setPurchasePrice(string $purchasePrice): self
    {
        $this->purchasePrice = $purchasePrice;

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
}
