<?php

namespace App\Entity\Accounting;

use App\Entity\Master\Account;
use App\Repository\Accounting\ExpenseHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExpenseHeaderRepository::class)]
class ExpenseHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\OneToMany(mappedBy: 'expenseHeader', targetEntity: ExpenseDetail::class)]
    private Collection $expenseDetails;

    public function __construct()
    {
        $this->expenseDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, ExpenseDetail>
     */
    public function getExpenseDetails(): Collection
    {
        return $this->expenseDetails;
    }

    public function addExpenseDetail(ExpenseDetail $expenseDetail): self
    {
        if (!$this->expenseDetails->contains($expenseDetail)) {
            $this->expenseDetails->add($expenseDetail);
            $expenseDetail->setExpenseHeader($this);
        }

        return $this;
    }

    public function removeExpenseDetail(ExpenseDetail $expenseDetail): self
    {
        if ($this->expenseDetails->removeElement($expenseDetail)) {
            // set the owning side to null (unless already changed)
            if ($expenseDetail->getExpenseHeader() === $this) {
                $expenseDetail->setExpenseHeader(null);
            }
        }

        return $this;
    }
}
