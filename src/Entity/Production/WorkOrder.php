<?php

namespace App\Entity\Production;

use App\Entity\Master\Material;
use App\Entity\ProductionHeader;
use App\Repository\Production\WorkOrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderRepository::class)]
#[ORM\Table(name: 'production_work_order')]
class WorkOrder extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'PWO';
    public const STATUS_NEW = 'new';
    public const STATUS_REPEAT = 'repeat';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'workOrders')]
    private ?MasterOrder $masterOrder = null;

    #[ORM\Column(length: 60)]
    private ?string $orderStatus = '';

    #[ORM\Column(length: 60)]
    private ?string $color = '';

    #[ORM\Column(length: 60)]
    private ?string $orderUp = '';

    #[ORM\Column(length: 60)]
    private ?string $insit = '';

    #[ORM\ManyToOne]
    private ?Material $material = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $printingKrisLength = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $printingKrisWidth = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $planoSizeLength = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $planoSizeWidth = '0.00';

    #[ORM\Column]
    private ?int $planoQuantity = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $cuttingLength1 = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $cuttingWidth1 = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $cuttingLength2 = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $cuttingWidth2 = '0.00';

    #[ORM\Column]
    private ?int $cuttingQuantity = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $cuttingInsit = '0.00';

    #[ORM\Column(length: 60)]
    private ?string $packingMaterialBox = '';

    #[ORM\Column(length: 60)]
    private ?string $packingMaterialPaper = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $planoConversion = '0.00';

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMasterOrder(): ?MasterOrder
    {
        return $this->masterOrder;
    }

    public function setMasterOrder(?MasterOrder $masterOrder): self
    {
        $this->masterOrder = $masterOrder;

        return $this;
    }

    public function getOrderStatus(): ?string
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(string $orderStatus): self
    {
        $this->orderStatus = $orderStatus;

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

    public function getOrderUp(): ?string
    {
        return $this->orderUp;
    }

    public function setOrderUp(string $orderUp): self
    {
        $this->orderUp = $orderUp;

        return $this;
    }

    public function getInsit(): ?string
    {
        return $this->insit;
    }

    public function setInsit(string $insit): self
    {
        $this->insit = $insit;

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

    public function getPrintingKrisLength(): ?string
    {
        return $this->printingKrisLength;
    }

    public function setPrintingKrisLength(string $printingKrisLength): self
    {
        $this->printingKrisLength = $printingKrisLength;

        return $this;
    }

    public function getPrintingKrisWidth(): ?string
    {
        return $this->printingKrisWidth;
    }

    public function setPrintingKrisWidth(string $printingKrisWidth): self
    {
        $this->printingKrisWidth = $printingKrisWidth;

        return $this;
    }

    public function getPlanoSizeLength(): ?string
    {
        return $this->planoSizeLength;
    }

    public function setPlanoSizeLength(string $planoSizeLength): self
    {
        $this->planoSizeLength = $planoSizeLength;

        return $this;
    }

    public function getPlanoSizeWidth(): ?string
    {
        return $this->planoSizeWidth;
    }

    public function setPlanoSizeWidth(string $planoSizeWidth): self
    {
        $this->planoSizeWidth = $planoSizeWidth;

        return $this;
    }

    public function getPlanoQuantity(): ?int
    {
        return $this->planoQuantity;
    }

    public function setPlanoQuantity(int $planoQuantity): self
    {
        $this->planoQuantity = $planoQuantity;

        return $this;
    }

    public function getCuttingLength1(): ?string
    {
        return $this->cuttingLength1;
    }

    public function setCuttingLength1(string $cuttingLength1): self
    {
        $this->cuttingLength1 = $cuttingLength1;

        return $this;
    }

    public function getCuttingWidth1(): ?string
    {
        return $this->cuttingWidth1;
    }

    public function setCuttingWidth1(string $cuttingWidth1): self
    {
        $this->cuttingWidth1 = $cuttingWidth1;

        return $this;
    }

    public function getCuttingLength2(): ?string
    {
        return $this->cuttingLength2;
    }

    public function setCuttingLength2(string $cuttingLength2): self
    {
        $this->cuttingLength2 = $cuttingLength2;

        return $this;
    }

    public function getCuttingWidth2(): ?string
    {
        return $this->cuttingWidth2;
    }

    public function setCuttingWidth2(string $cuttingWidth2): self
    {
        $this->cuttingWidth2 = $cuttingWidth2;

        return $this;
    }

    public function getCuttingQuantity(): ?int
    {
        return $this->cuttingQuantity;
    }

    public function setCuttingQuantity(int $cuttingQuantity): self
    {
        $this->cuttingQuantity = $cuttingQuantity;

        return $this;
    }

    public function getCuttingInsit(): ?string
    {
        return $this->cuttingInsit;
    }

    public function setCuttingInsit(string $cuttingInsit): self
    {
        $this->cuttingInsit = $cuttingInsit;

        return $this;
    }

    public function getPackingMaterialBox(): ?string
    {
        return $this->packingMaterialBox;
    }

    public function setPackingMaterialBox(string $packingMaterialBox): self
    {
        $this->packingMaterialBox = $packingMaterialBox;

        return $this;
    }

    public function getPackingMaterialPaper(): ?string
    {
        return $this->packingMaterialPaper;
    }

    public function setPackingMaterialPaper(string $packingMaterialPaper): self
    {
        $this->packingMaterialPaper = $packingMaterialPaper;

        return $this;
    }

    public function getPlanoConversion(): ?string
    {
        return $this->planoConversion;
    }

    public function setPlanoConversion(string $planoConversion): self
    {
        $this->planoConversion = $planoConversion;

        return $this;
    }
}
