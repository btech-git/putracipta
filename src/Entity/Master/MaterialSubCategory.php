<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\MaterialSubCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialSubCategoryRepository::class)]
#[ORM\Table(name: 'master_material_sub_category')]
class MaterialSubCategory extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'materialSubCategories')]
    private ?MaterialCategory $materialCategory = null;

    #[ORM\OneToMany(mappedBy: 'materialSubCategory', targetEntity: Material::class)]
    private Collection $materials;

    public function __construct()
    {
        $this->materials = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaterialCategory(): ?MaterialCategory
    {
        return $this->materialCategory;
    }

    public function setMaterialCategory(?MaterialCategory $materialCategory): self
    {
        $this->materialCategory = $materialCategory;

        return $this;
    }

    /**
     * @return Collection<int, Material>
     */
    public function getMaterials(): Collection
    {
        return $this->materials;
    }

    public function addMaterial(Material $material): self
    {
        if (!$this->materials->contains($material)) {
            $this->materials->add($material);
            $material->setMaterialSubCategory($this);
        }

        return $this;
    }

    public function removeMaterial(Material $material): self
    {
        if ($this->materials->removeElement($material)) {
            // set the owning side to null (unless already changed)
            if ($material->getMaterialSubCategory() === $this) {
                $material->setMaterialSubCategory(null);
            }
        }

        return $this;
    }
}
