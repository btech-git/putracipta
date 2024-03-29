<?php

namespace App\Entity\Master;

use App\Entity\Master;
use App\Repository\Master\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'master_product')]
#[UniqueEntity(['code', 'name'])]
class Product extends Master
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    private ?string $code = '';

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?ProductCategory $productCategory = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Unit $unit = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Customer $customer = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull]
    private ?string $note = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $length = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $width = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $height = '0.00';

    #[ORM\Column(length: 100)]
    private ?string $variant = '';

    #[ORM\Column(length: 20)]
    private ?string $fileExtension = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $weight = '0.00';

    #[ORM\Column(length: 100)]
    private ?string $material = '';

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: DiecutKnife::class)]
    private Collection $diecutKnives;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: DielineMillar::class)]
    private Collection $dielineMillars;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: DesignCodeProductDetail::class)]
    private Collection $designCodeProductDetails;

    public function __construct()
    {
        $this->diecutKnives = new ArrayCollection();
        $this->dielineMillars = new ArrayCollection();
        $this->designCodeProductDetails = new ArrayCollection();
    }
    
    public function getProductLengthWidthHeightCombination() {
        return $this->length . " x " . $this->width . " x " . $this->height;
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

    public function getProductCategory(): ?ProductCategory
    {
        return $this->productCategory;
    }

    public function setProductCategory(?ProductCategory $productCategory): self
    {
        $this->productCategory = $productCategory;

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

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

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

    public function getLength(): ?string
    {
        return $this->length;
    }

    public function setLength(string $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(string $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(string $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getVariant(): ?string
    {
        return $this->variant;
    }

    public function setVariant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    public function getFileExtension(): ?string
    {
        return $this->fileExtension;
    }

    public function setFileExtension(string $fileExtension): self
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getMaterial(): ?string
    {
        return $this->material;
    }

    public function setMaterial(string $material): self
    {
        $this->material = $material;

        return $this;
    }

    /**
     * @return Collection<int, DiecutKnife>
     */
    public function getDiecutKnives(): Collection
    {
        return $this->diecutKnives;
    }

    public function addDiecutKnife(DiecutKnife $diecutKnife): self
    {
        if (!$this->diecutKnives->contains($diecutKnife)) {
            $this->diecutKnives->add($diecutKnife);
            $diecutKnife->setProduct($this);
        }

        return $this;
    }

    public function removeDiecutKnife(DiecutKnife $diecutKnife): self
    {
        if ($this->diecutKnives->removeElement($diecutKnife)) {
            // set the owning side to null (unless already changed)
            if ($diecutKnife->getProduct() === $this) {
                $diecutKnife->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DielineMillar>
     */
    public function getDielineMillars(): Collection
    {
        return $this->dielineMillars;
    }

    public function addDielineMillar(DielineMillar $dielineMillar): self
    {
        if (!$this->dielineMillars->contains($dielineMillar)) {
            $this->dielineMillars->add($dielineMillar);
            $dielineMillar->setProduct($this);
        }

        return $this;
    }

    public function removeDielineMillar(DielineMillar $dielineMillar): self
    {
        if ($this->dielineMillars->removeElement($dielineMillar)) {
            // set the owning side to null (unless already changed)
            if ($dielineMillar->getProduct() === $this) {
                $dielineMillar->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DesignCodeProductDetail>
     */
    public function getDesignCodeProductDetails(): Collection
    {
        return $this->designCodeProductDetails;
    }

    public function addDesignCodeProductDetail(DesignCodeProductDetail $designCodeProductDetail): self
    {
        if (!$this->designCodeProductDetails->contains($designCodeProductDetail)) {
            $this->designCodeProductDetails->add($designCodeProductDetail);
            $designCodeProductDetail->setProduct($this);
        }

        return $this;
    }

    public function removeDesignCodeProductDetail(DesignCodeProductDetail $designCodeProductDetail): self
    {
        if ($this->designCodeProductDetails->removeElement($designCodeProductDetail)) {
            // set the owning side to null (unless already changed)
            if ($designCodeProductDetail->getProduct() === $this) {
                $designCodeProductDetail->setProduct(null);
            }
        }

        return $this;
    }
}
