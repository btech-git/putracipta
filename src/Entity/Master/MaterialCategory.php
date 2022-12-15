<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\MaterialCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialCategoryRepository::class)]
#[ORM\Table(name: 'master_material_category')]
class MaterialCategory extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'materialCategory', targetEntity: MaterialSubCategory::class)]
    private Collection $materialSubCategories;

    public function __construct()
    {
        $this->materialSubCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, MaterialSubCategory>
     */
    public function getMaterialSubCategories(): Collection
    {
        return $this->materialSubCategories;
    }

    public function addMaterialSubCategory(MaterialSubCategory $materialSubCategory): self
    {
        if (!$this->materialSubCategories->contains($materialSubCategory)) {
            $this->materialSubCategories->add($materialSubCategory);
            $materialSubCategory->setMaterialCategory($this);
        }

        return $this;
    }

    public function removeMaterialSubCategory(MaterialSubCategory $materialSubCategory): self
    {
        if ($this->materialSubCategories->removeElement($materialSubCategory)) {
            // set the owning side to null (unless already changed)
            if ($materialSubCategory->getMaterialCategory() === $this) {
                $materialSubCategory->setMaterialCategory(null);
            }
        }

        return $this;
    }
}
