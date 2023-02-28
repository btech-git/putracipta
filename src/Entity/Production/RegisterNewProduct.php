<?php

namespace App\Entity\Production;

use App\Entity\Master\Customer;
use App\Entity\Master\Employee;
use App\Entity\Master\Material;
use App\Entity\Master\Paper;
use App\Entity\ProductionHeader;
use App\Repository\Production\RegisterNewProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RegisterNewProductRepository::class)]
#[ORM\Table(name: 'production_register_new_product')]
class RegisterNewProduct extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'RNP';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Employee $employee = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Customer $customer = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotNull]
    private ?string $productCode = '';

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $productName = '';

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $measurement = '';

    #[ORM\Column(length: 60)]
    #[Assert\NotNull]
    private ?string $dataSource = '';

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Paper $paper = null;

    #[ORM\Column]
    private ?bool $isVarnish = false;

    #[ORM\Column(length: 60)]
    private ?string $color = '';

    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $quantityBlade = 0;

    #[ORM\Column(length: 60)]
    #[Assert\NotNull]
    private ?string $finishing = '';

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $varnish = '';

    #[ORM\Column(length: 60)]
    #[Assert\NotNull]
    private ?string $laminating = '';

    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $processList = '';

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $productionFileExtension = '';

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    private ?Material $material = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $quantityEngineering = 0;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $quantityProduction = 0;

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): self
    {
        $this->employee = $employee;

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

    public function getProductCode(): ?string
    {
        return $this->productCode;
    }

    public function setProductCode(string $productCode): self
    {
        $this->productCode = $productCode;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): self
    {
        $this->productName = $productName;

        return $this;
    }

    public function getMeasurement(): ?string
    {
        return $this->measurement;
    }

    public function setMeasurement(string $measurement): self
    {
        $this->measurement = $measurement;

        return $this;
    }

    public function getDataSource(): ?string
    {
        return $this->dataSource;
    }

    public function setDataSource(string $dataSource): self
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    public function getPaper(): ?Paper
    {
        return $this->paper;
    }

    public function setPaper(?Paper $paper): self
    {
        $this->paper = $paper;

        return $this;
    }

    public function isIsVarnish(): ?bool
    {
        return $this->isVarnish;
    }

    public function setIsVarnish(bool $isVarnish): self
    {
        $this->isVarnish = $isVarnish;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getQuantityBlade(): ?int
    {
        return $this->quantityBlade;
    }

    public function setQuantityBlade(int $quantityBlade): self
    {
        $this->quantityBlade = $quantityBlade;

        return $this;
    }

    public function getFinishing(): ?string
    {
        return $this->finishing;
    }

    public function setFinishing(string $finishing): self
    {
        $this->finishing = $finishing;

        return $this;
    }

    public function getVarnish(): ?string
    {
        return $this->varnish;
    }

    public function setVarnish(string $varnish): self
    {
        $this->varnish = $varnish;

        return $this;
    }

    public function getLaminating(): ?string
    {
        return $this->laminating;
    }

    public function setLaminating(string $laminating): self
    {
        $this->laminating = $laminating;

        return $this;
    }

    public function getProcessList(): ?string
    {
        return $this->processList;
    }

    public function setProcessList(string $processList): self
    {
        $this->processList = $processList;

        return $this;
    }

    public function getProductionFileExtension(): ?string
    {
        return $this->productionFileExtension;
    }

    public function setProductionFileExtension(string $productionFileExtension): self
    {
        $this->productionFileExtension = $productionFileExtension;

        return $this;
    }

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

        return $this;
    }

    public function getQuantityEngineering(): ?int
    {
        return $this->quantityEngineering;
    }

    public function setQuantityEngineering(int $quantityEngineering): self
    {
        $this->quantityEngineering = $quantityEngineering;

        return $this;
    }

    public function getQuantityProduction(): ?int
    {
        return $this->quantityProduction;
    }

    public function setQuantityProduction(int $quantityProduction): self
    {
        $this->quantityProduction = $quantityProduction;

        return $this;
    }
}
