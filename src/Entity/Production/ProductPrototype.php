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
    public const VARNISH_WB = 'wb';
    public const VARNISH_UV = 'uv';
    public const VARNISH_OPV = 'opv';
    public const LAMINATING_GLOSS = 'gloss';
    public const LAMINATING_DOFF = 'doff';
    public const PROCESS_DIECUT = 'diecut';
    public const PROCESS_EMBOSS = 'emboss';
    public const PROCESS_LOCK = 'lock_bottom';
    public const PROCESS_JOINT = 'lem_joint';
    public const PROCESS_HOTSTAMP = 'hotstamp';
    public const PROCESS_JILID = 'jilid_buku';
    public const DEVELOPMENT_TYPE_EP = 'ep';
    public const DEVELOPMENT_TYPE_FEP = 'fep';
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
    private ?int $quantityEngineering = 0;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?int $quantityProduction = 0;

    #[ORM\Column(type: Types::ARRAY)]
    private array $dataSource = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $varnishList = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $laminatingList = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $processList = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $developmentType = [];

    #[ORM\ManyToOne]
    private ?Paper $paperEp = null;

    #[ORM\ManyToOne]
    private ?Paper $paperFep = null;

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

    public function getDataSource(): array
    {
        return $this->dataSource;
    }

    public function setDataSource(array $dataSource): self
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    public function getVarnishList(): array
    {
        return $this->varnishList;
    }

    public function setVarnishList(array $varnishList): self
    {
        $this->varnishList = $varnishList;

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

    public function getPaperEp(): ?Paper
    {
        return $this->paperEp;
    }

    public function setPaperEp(?Paper $paperEp): self
    {
        $this->paperEp = $paperEp;

        return $this;
    }

    public function getPaperFep(): ?Paper
    {
        return $this->paperFep;
    }

    public function setPaperFep(?Paper $paperFep): self
    {
        $this->paperFep = $paperFep;

        return $this;
    }
}
