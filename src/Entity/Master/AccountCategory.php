<?php

namespace App\Entity\Master;

use App\Repository\Master\AccountCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountCategoryRepository::class)]
class AccountCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $code = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $isInactive = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'accountCategories')]
    private ?self $accountCategory = null;

    #[ORM\OneToMany(mappedBy: 'accountCategory', targetEntity: self::class)]
    private Collection $accountCategories;

    public function __construct()
    {
        $this->accountCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isIsInactive(): ?bool
    {
        return $this->isInactive;
    }

    public function setIsInactive(bool $isInactive): self
    {
        $this->isInactive = $isInactive;

        return $this;
    }

    public function getAccountCategory(): ?self
    {
        return $this->accountCategory;
    }

    public function setAccountCategory(?self $accountCategory): self
    {
        $this->accountCategory = $accountCategory;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getAccountCategories(): Collection
    {
        return $this->accountCategories;
    }

    public function addAccountCategory(self $accountCategory): self
    {
        if (!$this->accountCategories->contains($accountCategory)) {
            $this->accountCategories->add($accountCategory);
            $accountCategory->setAccountCategory($this);
        }

        return $this;
    }

    public function removeAccountCategory(self $accountCategory): self
    {
        if ($this->accountCategories->removeElement($accountCategory)) {
            // set the owning side to null (unless already changed)
            if ($accountCategory->getAccountCategory() === $this) {
                $accountCategory->setAccountCategory(null);
            }
        }

        return $this;
    }
}
