<?php

namespace App\Entity\Accounting;

use App\Entity\AccountingHeader;
use App\Entity\Master\Account;
use App\Repository\Accounting\DepositHeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepositHeaderRepository::class)]
#[ORM\Table(name: 'accounting_deposit_header')]
class DepositHeader extends AccountingHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\OneToMany(mappedBy: 'depositHeader', targetEntity: DepositDetail::class)]
    private Collection $depositDetails;

    public function __construct()
    {
        $this->depositDetails = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return 'DPS';
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
     * @return Collection<int, DepositDetail>
     */
    public function getDepositDetails(): Collection
    {
        return $this->depositDetails;
    }

    public function addDepositDetail(DepositDetail $depositDetail): self
    {
        if (!$this->depositDetails->contains($depositDetail)) {
            $this->depositDetails->add($depositDetail);
            $depositDetail->setDepositHeader($this);
        }

        return $this;
    }

    public function removeDepositDetail(DepositDetail $depositDetail): self
    {
        if ($this->depositDetails->removeElement($depositDetail)) {
            // set the owning side to null (unless already changed)
            if ($depositDetail->getDepositHeader() === $this) {
                $depositDetail->setDepositHeader(null);
            }
        }

        return $this;
    }
}
