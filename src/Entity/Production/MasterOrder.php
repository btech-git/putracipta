<?php

namespace App\Entity\Production;

use App\Entity\Master\Customer;
use App\Entity\Master\Paper;
use App\Entity\Master\Product;
use App\Entity\ProductionHeader;
use App\Entity\Transaction\PurchaseOrderPaperHeader;
use App\Entity\Transaction\SaleOrderDetail;
use App\Repository\Production\MasterOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MasterOrderRepository::class)]
#[ORM\Table(name: 'production_master_order')]
class MasterOrder extends ProductionHeader
{
    public const CODE_NUMBER_CONSTANT = 'PMO';
    public const WORK_ORDER_DESIGN = 'design';
    public const WORK_ORDER_MOUNTAGE = 'mountage';
    public const WORK_ORDER_CUTTING_MACHINE = 'cutting_machine';
    public const WORK_ORDER_PRINTING = 'printing';
    public const WORK_ORDER_PON = 'pon';
    public const WORK_ORDER_CUTTING_FINISHING = 'cutting_finishing';
    public const WORK_ORDER_VARNISH = 'varnish';
    public const WORK_ORDER_GLUEING = 'glueing';
    public const WORK_ORDER_STITCHING = 'stitching';
    public const WORK_ORDER_FINISHING = 'finishing';
    public const WORK_ORDER_PACKING = 'packing';
    public const WORK_ORDER_QC_PRINTING = 'qc_printing';
    public const WORK_ORDER_QC_FINISHING = 'qc_finishing';
    public const WORK_ORDER_QC_SORTING = 'qc_sorting';
    public const WORK_ORDER_QC_OUTGOING = 'qc_outgoing';
    public const WORK_ORDER_PRINTING_CHECKLIST = 'printing_checklist';
    public const WORK_ORDER_MOUNTAGE_CHECKLIST = 'mountage_checklist';
    public const WORK_ORDER_POND_CHECKLIST = 'pond_checklist';
    public const WORK_ORDER_REWORK = 'rework';
    public const WORK_ORDER_DEMOLITION = 'demolition';
    public const PRINTING_STATUS_PROOF_PRINT = 'proof_print';
    public const PRINTING_STATUS_NEW_ORDER = 'new_order';
    public const PRINTING_STATUS_REPEAT_ORDER = 'repeat_order';
    public const PRINTING_STATUS_REVISE_DESIGN = 'revise_design';
    public const DIECUT_BLADE_NEW = 'new';
    public const DIECUT_BLADE_OLD = 'old';
    public const DIECUT_BLADE_REVISION = 'revision';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $printingMachine = '';

    #[ORM\ManyToOne]
    private ?Customer $customer = null;

    #[ORM\Column(length: 60)]
    private ?string $designCode = '';

    #[ORM\Column]
    private ?int $quantityOrder = 0;

    #[ORM\Column]
    private ?int $quantityStock = 0;

    #[ORM\Column(length: 60)]
    private ?string $color = '';

    #[ORM\Column(length: 60)]
    private ?string $finishing = '';

    #[ORM\ManyToOne]
    private ?Paper $paper = null;

    #[ORM\Column]
    private ?int $quantityPrinting = 0;

    #[ORM\Column]
    private ?bool $isUsingHotStamping = false;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deliveryDate = null;

    #[ORM\Column(length: 60)]
    private ?string $printingStatus = '';

    #[ORM\Column(length: 60)]
    private ?string $dieCutBlade = '';

    #[ORM\Column(length: 20)]
    private ?string $dieCutBladeNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $dieLineFilmNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $layoutModelFileExtension = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $insitPercentage = '0.00';

    #[ORM\Column]
    private ?int $quantityPaper = 0;

    #[ORM\Column]
    private ?int $paperMountage = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $paperPlanoLength = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $paperPlanoWidth = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $cuttingLengthSize1 = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $cuttingWidthSize1 = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $cuttingLengthSize2 = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $cuttingWidthSize2 = '0.00';

    #[ORM\Column(length: 100)]
    private ?string $paperRequirement = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $printingInsit = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $paperTotal = '0.00';

    #[ORM\ManyToOne(inversedBy: 'masterOrders')]
    private ?PurchaseOrderPaperHeader $purchaseOrderPaperHeader = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkCyanPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkCyanWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkMagentaPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkMagentaWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkYellowPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkYellowWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkBlackPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkBlackWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkK1Percentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkK1Weight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkK2Percentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkK2Weight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkK3Percentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkK3Weight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkK4Percentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkK4Weight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkOpvPercentage = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkOpvWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkLaminatingSize = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $inkLaminatingQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingGlueQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingGlueWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingRubberQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingRubberWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingPaperQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingPaperWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingBoxQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingBoxWeight = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingTapeLargeQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingTapeLargeSize = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingTapeSmallQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingTapeSmallSize = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingPlasticQuantity = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $packagingPlasticSize = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $dimensionLength = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $dimensionWidth = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $dimensionHeight = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $mountageSizeLength = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $mountageSizeWidth = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $workOrderDistribution = [];

    #[ORM\Column(length: 100)]
    private ?string $printingStatusData = null;

    #[ORM\Column(length: 100)]
    private ?string $diecutBladeData = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'masterOrders')]
    private ?SaleOrderDetail $saleOrderDetail = null;

    #[ORM\OneToMany(mappedBy: 'masterOrder', targetEntity: WorkOrderHeader::class)]
    private Collection $workOrderHeaders;

    #[ORM\Column(type: Types::ARRAY)]
    private array $processList = [];

    public function __construct()
    {
        $this->workOrderHeaders = new ArrayCollection();
    }

    public function getCodeNumberConstant(): string
    {
        return self::CODE_NUMBER_CONSTANT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrintingMachine(): ?string
    {
        return $this->printingMachine;
    }

    public function setPrintingMachine(string $printingMachine): self
    {
        $this->printingMachine = $printingMachine;

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

    public function getDesignCode(): ?string
    {
        return $this->designCode;
    }

    public function setDesignCode(string $designCode): self
    {
        $this->designCode = $designCode;

        return $this;
    }

    public function getQuantityOrder(): ?int
    {
        return $this->quantityOrder;
    }

    public function setQuantityOrder(int $quantityOrder): self
    {
        $this->quantityOrder = $quantityOrder;

        return $this;
    }

    public function getQuantityStock(): ?int
    {
        return $this->quantityStock;
    }

    public function setQuantityStock(int $quantityStock): self
    {
        $this->quantityStock = $quantityStock;

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

    public function getFinishing(): ?string
    {
        return $this->finishing;
    }

    public function setFinishing(string $finishing): self
    {
        $this->finishing = $finishing;

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

    public function getQuantityPrinting(): ?int
    {
        return $this->quantityPrinting;
    }

    public function setQuantityPrinting(int $quantityPrinting): self
    {
        $this->quantityPrinting = $quantityPrinting;

        return $this;
    }

    public function isIsUsingHotStamping(): ?bool
    {
        return $this->isUsingHotStamping;
    }

    public function setIsUsingHotStamping(bool $isUsingHotStamping): self
    {
        $this->isUsingHotStamping = $isUsingHotStamping;

        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getPrintingStatus(): ?string
    {
        return $this->printingStatus;
    }

    public function setPrintingStatus(string $printingStatus): self
    {
        $this->printingStatus = $printingStatus;

        return $this;
    }

    public function getDieCutBlade(): ?string
    {
        return $this->dieCutBlade;
    }

    public function setDieCutBlade(string $dieCutBlade): self
    {
        $this->dieCutBlade = $dieCutBlade;

        return $this;
    }

    public function getDieCutBladeNumber(): ?string
    {
        return $this->dieCutBladeNumber;
    }

    public function setDieCutBladeNumber(string $dieCutBladeNumber): self
    {
        $this->dieCutBladeNumber = $dieCutBladeNumber;

        return $this;
    }

    public function getDieLineFilmNumber(): ?string
    {
        return $this->dieLineFilmNumber;
    }

    public function setDieLineFilmNumber(string $dieLineFilmNumber): self
    {
        $this->dieLineFilmNumber = $dieLineFilmNumber;

        return $this;
    }

    public function getLayoutModelFileExtension(): ?string
    {
        return $this->layoutModelFileExtension;
    }

    public function setLayoutModelFileExtension(string $layoutModelFileExtension): self
    {
        $this->layoutModelFileExtension = $layoutModelFileExtension;

        return $this;
    }

    public function getInsitPercentage(): ?string
    {
        return $this->insitPercentage;
    }

    public function setInsitPercentage(string $insitPercentage): self
    {
        $this->insitPercentage = $insitPercentage;

        return $this;
    }

    public function getQuantityPaper(): ?int
    {
        return $this->quantityPaper;
    }

    public function setQuantityPaper(int $quantityPaper): self
    {
        $this->quantityPaper = $quantityPaper;

        return $this;
    }

    public function getPaperMountage(): ?int
    {
        return $this->paperMountage;
    }

    public function setPaperMountage(int $paperMountage): self
    {
        $this->paperMountage = $paperMountage;

        return $this;
    }

    public function getPaperPlanoLength(): ?string
    {
        return $this->paperPlanoLength;
    }

    public function setPaperPlanoLength(string $paperPlanoLength): self
    {
        $this->paperPlanoLength = $paperPlanoLength;

        return $this;
    }

    public function getPaperPlanoWidth(): ?string
    {
        return $this->paperPlanoWidth;
    }

    public function setPaperPlanoWidth(string $paperPlanoWidth): self
    {
        $this->paperPlanoWidth = $paperPlanoWidth;

        return $this;
    }

    public function getCuttingLengthSize1(): ?string
    {
        return $this->cuttingLengthSize1;
    }

    public function setCuttingLengthSize1(string $cuttingLengthSize1): self
    {
        $this->cuttingLengthSize1 = $cuttingLengthSize1;

        return $this;
    }

    public function getCuttingWidthSize1(): ?string
    {
        return $this->cuttingWidthSize1;
    }

    public function setCuttingWidthSize1(string $cuttingWidthSize1): self
    {
        $this->cuttingWidthSize1 = $cuttingWidthSize1;

        return $this;
    }

    public function getCuttingLengthSize2(): ?string
    {
        return $this->cuttingLengthSize2;
    }

    public function setCuttingLengthSize2(string $cuttingLengthSize2): self
    {
        $this->cuttingLengthSize2 = $cuttingLengthSize2;

        return $this;
    }

    public function getCuttingWidthSize2(): ?string
    {
        return $this->cuttingWidthSize2;
    }

    public function setCuttingWidthSize2(string $cuttingWidthSize2): self
    {
        $this->cuttingWidthSize2 = $cuttingWidthSize2;

        return $this;
    }

    public function getPaperRequirement(): ?string
    {
        return $this->paperRequirement;
    }

    public function setPaperRequirement(string $paperRequirement): self
    {
        $this->paperRequirement = $paperRequirement;

        return $this;
    }

    public function getPrintingInsit(): ?string
    {
        return $this->printingInsit;
    }

    public function setPrintingInsit(string $printingInsit): self
    {
        $this->printingInsit = $printingInsit;

        return $this;
    }

    public function getPaperTotal(): ?string
    {
        return $this->paperTotal;
    }

    public function setPaperTotal(string $paperTotal): self
    {
        $this->paperTotal = $paperTotal;

        return $this;
    }

    public function getPurchaseOrderPaperHeader(): ?PurchaseOrderPaperHeader
    {
        return $this->purchaseOrderPaperHeader;
    }

    public function setPurchaseOrderPaperHeader(?PurchaseOrderPaperHeader $purchaseOrderPaperHeader): self
    {
        $this->purchaseOrderPaperHeader = $purchaseOrderPaperHeader;

        return $this;
    }

    public function getInkCyanPercentage(): ?string
    {
        return $this->inkCyanPercentage;
    }

    public function setInkCyanPercentage(string $inkCyanPercentage): self
    {
        $this->inkCyanPercentage = $inkCyanPercentage;

        return $this;
    }

    public function getInkCyanWeight(): ?string
    {
        return $this->inkCyanWeight;
    }

    public function setInkCyanWeight(string $inkCyanWeight): self
    {
        $this->inkCyanWeight = $inkCyanWeight;

        return $this;
    }

    public function getInkMagentaPercentage(): ?string
    {
        return $this->inkMagentaPercentage;
    }

    public function setInkMagentaPercentage(string $inkMagentaPercentage): self
    {
        $this->inkMagentaPercentage = $inkMagentaPercentage;

        return $this;
    }

    public function getInkMagentaWeight(): ?string
    {
        return $this->inkMagentaWeight;
    }

    public function setInkMagentaWeight(string $inkMagentaWeight): self
    {
        $this->inkMagentaWeight = $inkMagentaWeight;

        return $this;
    }

    public function getInkYellowPercentage(): ?string
    {
        return $this->inkYellowPercentage;
    }

    public function setInkYellowPercentage(string $inkYellowPercentage): self
    {
        $this->inkYellowPercentage = $inkYellowPercentage;

        return $this;
    }

    public function getInkYellowWeight(): ?string
    {
        return $this->inkYellowWeight;
    }

    public function setInkYellowWeight(string $inkYellowWeight): self
    {
        $this->inkYellowWeight = $inkYellowWeight;

        return $this;
    }

    public function getInkBlackPercentage(): ?string
    {
        return $this->inkBlackPercentage;
    }

    public function setInkBlackPercentage(string $inkBlackPercentage): self
    {
        $this->inkBlackPercentage = $inkBlackPercentage;

        return $this;
    }

    public function getInkBlackWeight(): ?string
    {
        return $this->inkBlackWeight;
    }

    public function setInkBlackWeight(string $inkBlackWeight): self
    {
        $this->inkBlackWeight = $inkBlackWeight;

        return $this;
    }

    public function getInkK1Percentage(): ?string
    {
        return $this->inkK1Percentage;
    }

    public function setInkK1Percentage(string $inkK1Percentage): self
    {
        $this->inkK1Percentage = $inkK1Percentage;

        return $this;
    }

    public function getInkK1Weight(): ?string
    {
        return $this->inkK1Weight;
    }

    public function setInkK1Weight(string $inkK1Weight): self
    {
        $this->inkK1Weight = $inkK1Weight;

        return $this;
    }

    public function getInkK2Percentage(): ?string
    {
        return $this->inkK2Percentage;
    }

    public function setInkK2Percentage(string $inkK2Percentage): self
    {
        $this->inkK2Percentage = $inkK2Percentage;

        return $this;
    }

    public function getInkK2Weight(): ?string
    {
        return $this->inkK2Weight;
    }

    public function setInkK2Weight(string $inkK2Weight): self
    {
        $this->inkK2Weight = $inkK2Weight;

        return $this;
    }

    public function getInkK3Percentage(): ?string
    {
        return $this->inkK3Percentage;
    }

    public function setInkK3Percentage(string $inkK3Percentage): self
    {
        $this->inkK3Percentage = $inkK3Percentage;

        return $this;
    }

    public function getInkK3Weight(): ?string
    {
        return $this->inkK3Weight;
    }

    public function setInkK3Weight(string $inkK3Weight): self
    {
        $this->inkK3Weight = $inkK3Weight;

        return $this;
    }

    public function getInkK4Percentage(): ?string
    {
        return $this->inkK4Percentage;
    }

    public function setInkK4Percentage(string $inkK4Percentage): self
    {
        $this->inkK4Percentage = $inkK4Percentage;

        return $this;
    }

    public function getInkK4Weight(): ?string
    {
        return $this->inkK4Weight;
    }

    public function setInkK4Weight(string $inkK4Weight): self
    {
        $this->inkK4Weight = $inkK4Weight;

        return $this;
    }

    public function getInkOpvPercentage(): ?string
    {
        return $this->inkOpvPercentage;
    }

    public function setInkOpvPercentage(string $inkOpvPercentage): self
    {
        $this->inkOpvPercentage = $inkOpvPercentage;

        return $this;
    }

    public function getInkOpvWeight(): ?string
    {
        return $this->inkOpvWeight;
    }

    public function setInkOpvWeight(string $inkOpvWeight): self
    {
        $this->inkOpvWeight = $inkOpvWeight;

        return $this;
    }

    public function getInkLaminatingSize(): ?string
    {
        return $this->inkLaminatingSize;
    }

    public function setInkLaminatingSize(string $inkLaminatingSize): self
    {
        $this->inkLaminatingSize = $inkLaminatingSize;

        return $this;
    }

    public function getInkLaminatingQuantity(): ?string
    {
        return $this->inkLaminatingQuantity;
    }

    public function setInkLaminatingQuantity(string $inkLaminatingQuantity): self
    {
        $this->inkLaminatingQuantity = $inkLaminatingQuantity;

        return $this;
    }

    public function getPackagingGlueQuantity(): ?string
    {
        return $this->packagingGlueQuantity;
    }

    public function setPackagingGlueQuantity(string $packagingGlueQuantity): self
    {
        $this->packagingGlueQuantity = $packagingGlueQuantity;

        return $this;
    }

    public function getPackagingGlueWeight(): ?string
    {
        return $this->packagingGlueWeight;
    }

    public function setPackagingGlueWeight(string $packagingGlueWeight): self
    {
        $this->packagingGlueWeight = $packagingGlueWeight;

        return $this;
    }

    public function getPackagingRubberQuantity(): ?string
    {
        return $this->packagingRubberQuantity;
    }

    public function setPackagingRubberQuantity(string $packagingRubberQuantity): self
    {
        $this->packagingRubberQuantity = $packagingRubberQuantity;

        return $this;
    }

    public function getPackagingRubberWeight(): ?string
    {
        return $this->packagingRubberWeight;
    }

    public function setPackagingRubberWeight(string $packagingRubberWeight): self
    {
        $this->packagingRubberWeight = $packagingRubberWeight;

        return $this;
    }

    public function getPackagingPaperQuantity(): ?string
    {
        return $this->packagingPaperQuantity;
    }

    public function setPackagingPaperQuantity(string $packagingPaperQuantity): self
    {
        $this->packagingPaperQuantity = $packagingPaperQuantity;

        return $this;
    }

    public function getPackagingPaperWeight(): ?string
    {
        return $this->packagingPaperWeight;
    }

    public function setPackagingPaperWeight(string $packagingPaperWeight): self
    {
        $this->packagingPaperWeight = $packagingPaperWeight;

        return $this;
    }

    public function getPackagingBoxQuantity(): ?string
    {
        return $this->packagingBoxQuantity;
    }

    public function setPackagingBoxQuantity(string $packagingBoxQuantity): self
    {
        $this->packagingBoxQuantity = $packagingBoxQuantity;

        return $this;
    }

    public function getPackagingBoxWeight(): ?string
    {
        return $this->packagingBoxWeight;
    }

    public function setPackagingBoxWeight(string $packagingBoxWeight): self
    {
        $this->packagingBoxWeight = $packagingBoxWeight;

        return $this;
    }

    public function getPackagingTapeLargeQuantity(): ?string
    {
        return $this->packagingTapeLargeQuantity;
    }

    public function setPackagingTapeLargeQuantity(string $packagingTapeLargeQuantity): self
    {
        $this->packagingTapeLargeQuantity = $packagingTapeLargeQuantity;

        return $this;
    }

    public function getPackagingTapeLargeSize(): ?string
    {
        return $this->packagingTapeLargeSize;
    }

    public function setPackagingTapeLargeSize(string $packagingTapeLargeSize): self
    {
        $this->packagingTapeLargeSize = $packagingTapeLargeSize;

        return $this;
    }

    public function getPackagingTapeSmallQuantity(): ?string
    {
        return $this->packagingTapeSmallQuantity;
    }

    public function setPackagingTapeSmallQuantity(string $packagingTapeSmallQuantity): self
    {
        $this->packagingTapeSmallQuantity = $packagingTapeSmallQuantity;

        return $this;
    }

    public function getPackagingTapeSmallSize(): ?string
    {
        return $this->packagingTapeSmallSize;
    }

    public function setPackagingTapeSmallSize(string $packagingTapeSmallSize): self
    {
        $this->packagingTapeSmallSize = $packagingTapeSmallSize;

        return $this;
    }

    public function getPackagingPlasticQuantity(): ?string
    {
        return $this->packagingPlasticQuantity;
    }

    public function setPackagingPlasticQuantity(string $packagingPlasticQuantity): self
    {
        $this->packagingPlasticQuantity = $packagingPlasticQuantity;

        return $this;
    }

    public function getPackagingPlasticSize(): ?string
    {
        return $this->packagingPlasticSize;
    }

    public function setPackagingPlasticSize(string $packagingPlasticSize): self
    {
        $this->packagingPlasticSize = $packagingPlasticSize;

        return $this;
    }

    public function getDimensionLength(): ?string
    {
        return $this->dimensionLength;
    }

    public function setDimensionLength(string $dimensionLength): self
    {
        $this->dimensionLength = $dimensionLength;

        return $this;
    }

    public function getDimensionWidth(): ?string
    {
        return $this->dimensionWidth;
    }

    public function setDimensionWidth(string $dimensionWidth): self
    {
        $this->dimensionWidth = $dimensionWidth;

        return $this;
    }

    public function getDimensionHeight(): ?string
    {
        return $this->dimensionHeight;
    }

    public function setDimensionHeight(string $dimensionHeight): self
    {
        $this->dimensionHeight = $dimensionHeight;

        return $this;
    }

    public function getMountageSizeLength(): ?string
    {
        return $this->mountageSizeLength;
    }

    public function setMountageSizeLength(string $mountageSizeLength): self
    {
        $this->mountageSizeLength = $mountageSizeLength;

        return $this;
    }

    public function getMountageSizeWidth(): ?string
    {
        return $this->mountageSizeWidth;
    }

    public function setMountageSizeWidth(string $mountageSizeWidth): self
    {
        $this->mountageSizeWidth = $mountageSizeWidth;

        return $this;
    }

    public function getWorkOrderDistribution(): array
    {
        return $this->workOrderDistribution;
    }

    public function setWorkOrderDistribution(array $workOrderDistribution): self
    {
        $this->workOrderDistribution = $workOrderDistribution;

        return $this;
    }

    public function getPrintingStatusData(): ?string
    {
        return $this->printingStatusData;
    }

    public function setPrintingStatusData(string $printingStatusData): self
    {
        $this->printingStatusData = $printingStatusData;

        return $this;
    }

    public function getDiecutBladeData(): ?string
    {
        return $this->diecutBladeData;
    }

    public function setDiecutBladeData(string $diecutBladeData): self
    {
        $this->diecutBladeData = $diecutBladeData;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getSaleOrderDetail(): ?SaleOrderDetail
    {
        return $this->saleOrderDetail;
    }

    public function setSaleOrderDetail(?SaleOrderDetail $saleOrderDetail): self
    {
        $this->saleOrderDetail = $saleOrderDetail;

        return $this;
    }

    /**
     * @return Collection<int, WorkOrderHeader>
     */
    public function getWorkOrderHeaders(): Collection
    {
        return $this->workOrderHeaders;
    }

    public function addWorkOrderHeader(WorkOrderHeader $workOrderHeader): self
    {
        if (!$this->workOrderHeaders->contains($workOrderHeader)) {
            $this->workOrderHeaders->add($workOrderHeader);
            $workOrderHeader->setMasterOrder($this);
        }

        return $this;
    }

    public function removeWorkOrderHeader(WorkOrderHeader $workOrderHeader): self
    {
        if ($this->workOrderHeaders->removeElement($workOrderHeader)) {
            // set the owning side to null (unless already changed)
            if ($workOrderHeader->getMasterOrder() === $this) {
                $workOrderHeader->setMasterOrder(null);
            }
        }

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
}
