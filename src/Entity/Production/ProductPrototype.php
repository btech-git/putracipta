<?php

namespace App\Entity\Production;

use App\Entity\Master\Customer;
use App\Entity\Master\Employee;
use App\Entity\Master\Paper;
use App\Entity\ProductionHeader;
use App\Repository\Production\ProductPrototypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductPrototypeRepository::class)]
#[ORM\Table(name: 'production_product_prototype')]
class ProductPrototype extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'RNP';
    public const DATA_SOURCE_HARD_FA = 'hard_fa';
    public const DATA_SOURCE_EMAIL = 'email';
    public const DATA_SOURCE_CD = 'cd';
    public const DATA_SOURCE_PRINT_SAMPLE = 'print_sample';
    public const COATING_OPV_MATT = 'opv_matt';
    public const COATING_OPV_GLOSSY = 'opv_glossy';
    public const COATING_WB_MATT = 'wb_matt';
    public const COATING_WB_GLOSSY_FULL = 'wb_glossy_full';
    public const COATING_WB_GLOSSY_FREE = 'wb_glossy_free';
    public const COATING_WB_CALENDERING = 'wb_glossy_calendering';
    public const COATING_UV_GLOSSY_FULL = 'uv_glossy_full';
    public const COATING_UV_GLOSSY_FREE = 'uv_glossy_free';
    public const COATING_UV_GLOSSY_SPOT = 'uv_glossy_spot';
    public const LAMINATING_MATT = 'matt';
    public const LAMINATING_DOV = 'dov';
    public const PROCESS_PRINTING = 'printing';
    public const PROCESS_COATING = 'coating';
    public const PROCESS_DIECUT = 'diecut';
    public const PROCESS_EMBOSS = 'emboss';
    public const PROCESS_HOTSTAMP = 'hotstamp';
    public const PROCESS_LOCK_BOTTOM = 'lem_lock_bottom';
    public const PROCESS_STRAIGHT_JOINT = 'lem_straight_joint';
    public const PROCESS_JILID = 'jilid_buku';
    public const DEVELOPMENT_TYPE_EP = 'ep';
    public const DEVELOPMENT_TYPE_FEP = 'fep';
    public const DEVELOPMENT_TYPE_PP = 'pp';
    public const DEVELOPMENT_TYPE_PS = 'ps';
    
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
    private ?string $color = '';

    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $quantityBlade = 0;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    private ?string $productionFileExtension = '';

    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $quantityProduction = 0;

    #[ORM\Column(type: Types::ARRAY)]
    private array $dataSource = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $laminatingList = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $processList = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $developmentType = [];

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $coatingList = [];

    #[ORM\Column(length: 60)]
    private ?string $designCode = '';

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

    public function getProductionFileExtension(): ?string
    {
        return $this->productionFileExtension;
    }

    public function setProductionFileExtension(string $productionFileExtension): self
    {
        $this->productionFileExtension = $productionFileExtension;

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

    public function getDataSource(): array
    {
        return $this->dataSource;
    }

    public function setDataSource(array $dataSource): self
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    public function getLaminatingList(): array
    {
        return $this->laminatingList;
    }

    public function setLaminatingList(array $laminatingList): self
    {
        $this->laminatingList = $laminatingList;

        return $this;
    }

    public function getProcessList(): array
    {
        return $this->processList;
    }

    public function setProcessList(array $processList): self
    {
        $this->processList = $processList;

        return $this;
    }

    public function getDevelopmentType(): array
    {
        return $this->developmentType;
    }

    public function setDevelopmentType(array $developmentType): self
    {
        $this->developmentType = $developmentType;

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

    public function getCoatingList(): array
    {
        return $this->coatingList;
    }

    public function setCoatingList(array $coatingList): self
    {
        $this->coatingList = $coatingList;

        return $this;
    }

    public function getDesignCode(): ?string
    {
        return $this->designCode;
    }

    public function setDesignCode(string $designCode): self
    {
        $this->designCode = $designCode;

        return $this;
    }
}
