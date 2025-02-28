<?php

namespace App\Entity\Admin;

use App\Repository\Admin\LiteralConfigRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LiteralConfigRepository::class)]
#[ORM\Table(name: 'admin_literal_config')]
class LiteralConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $vatPercentage = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $serviceTaxPercentage = '0.00';

    #[ORM\Column(length: 60)]
    private ?string $ifscCode = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 2)]
    private ?string $paymentRemainingTolerance = '0.00';

    #[ORM\Column(length: 100)]
    private ?string $diecutApprovalNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $diecutApprovalRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $diecutApprovalDate = null;

    #[ORM\Column(length: 100)]
    private ?string $checklistDiecutNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $checklistDiecutRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $checklistDiecutDate = null;

    #[ORM\Column(length: 100)]
    private ?string $clearanceDiecutNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $clearanceDiecutRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $clearanceDiecutDate = null;

    #[ORM\Column(length: 100)]
    private ?string $checklistGlueingNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $checklistGlueingRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $checklistGlueingDate = null;

    #[ORM\Column(length: 100)]
    private ?string $packingHandoverNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $packingHandoverRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $packingHandoverDate = null;

    #[ORM\Column(length: 100)]
    private ?string $outgoingQualitySheetNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $outgoingQualitySheetRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $outgoingQualitySheetDate = null;

    #[ORM\Column(length: 100)]
    private ?string $formPlateNumber = '';

    #[ORM\Column(length: 100)]
    private ?string $formPlateRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $formPlateDate = null;

    #[ORM\Column(length: 100)]
    private ?string $checklistPrepressNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $checklistPrepressRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $checklistPrepressDate = null;

    #[ORM\Column(length: 100)]
    private ?string $printingApprovalNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $printingApprovalRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $printingApprovalDate = null;

    #[ORM\Column(length: 100)]
    private ?string $checklistPrintingNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $checklistPrintingRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $checklistPrintingDate = null;

    #[ORM\Column(length: 100)]
    private ?string $printingInspectionNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $printingInspectionRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $printingInspectionDate = null;

    #[ORM\Column(length: 100)]
    private ?string $checklistProductionNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $checklistProductionRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $checklistProductionDate = null;

    #[ORM\Column(length: 100)]
    private ?string $checklistSortingNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $checklistSortingRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $checklistSortingDate = null;

    #[ORM\Column(length: 100)]
    private ?string $qualityInspectionNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $qualityInspectionRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $qualityInspectionDate = null;

    #[ORM\Column(length: 100)]
    private ?string $masterOrderNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $masterOrderRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $masterOrderDate = null;

    #[ORM\Column(length: 100)]
    private ?string $workOrderNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $workOrderRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $workOrderDate = null;

    #[ORM\Column(length: 100)]
    private ?string $colorMixingNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $colorMixingRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $colorMixingDate = null;

    #[ORM\Column(length: 100)]
    private ?string $cuttingMaterialNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $cuttingMaterialRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $cuttingMaterialDate = null;

    #[ORM\Column(length: 100)]
    private ?string $diecutBobstNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $diecutBobstRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $diecutBobstDate = null;

    #[ORM\Column(length: 100)]
    private ?string $finishingNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $finishingRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $finishingDate = null;

    #[ORM\Column(length: 100)]
    private ?string $glueingMachineNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $glueingMachineRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $glueingMachineDate = null;

    #[ORM\Column(length: 100)]
    private ?string $hotStampingNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $hotStampingRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $hotStampingDate = null;

    #[ORM\Column(length: 100)]
    private ?string $packingNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $packingRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $packingDate = null;

    #[ORM\Column(length: 100)]
    private ?string $prepressNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $prepressRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $prepressDate = null;

    #[ORM\Column(length: 100)]
    private ?string $offsetPrintingNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $offsetPrintingRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $offsetPrintingDate = null;

    #[ORM\Column(length: 100)]
    private ?string $sortingNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $sortingRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sortingDate = null;

    #[ORM\Column(length: 100)]
    private ?string $stitchingNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $stitchingRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $stitchingDate = null;

    #[ORM\Column(length: 100)]
    private ?string $coatingNumber = '';

    #[ORM\Column(length: 20)]
    private ?string $coatingRevision = '';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $coatingDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVatPercentage(): ?int
    {
        return $this->vatPercentage;
    }

    public function setVatPercentage(int $vatPercentage): self
    {
        $this->vatPercentage = $vatPercentage;

        return $this;
    }

    public function getServiceTaxPercentage(): ?string
    {
        return $this->serviceTaxPercentage;
    }

    public function setServiceTaxPercentage(string $serviceTaxPercentage): self
    {
        $this->serviceTaxPercentage = $serviceTaxPercentage;

        return $this;
    }

    public function getIfscCode(): ?string
    {
        return $this->ifscCode;
    }

    public function setIfscCode(string $ifscCode): self
    {
        $this->ifscCode = $ifscCode;

        return $this;
    }

    public function getPaymentRemainingTolerance(): ?string
    {
        return $this->paymentRemainingTolerance;
    }

    public function setPaymentRemainingTolerance(string $paymentRemainingTolerance): self
    {
        $this->paymentRemainingTolerance = $paymentRemainingTolerance;

        return $this;
    }

    public function getDiecutApprovalNumber(): ?string
    {
        return $this->diecutApprovalNumber;
    }

    public function setDiecutApprovalNumber(string $diecutApprovalNumber): self
    {
        $this->diecutApprovalNumber = $diecutApprovalNumber;

        return $this;
    }

    public function getDiecutApprovalRevision(): ?string
    {
        return $this->diecutApprovalRevision;
    }

    public function setDiecutApprovalRevision(string $diecutApprovalRevision): self
    {
        $this->diecutApprovalRevision = $diecutApprovalRevision;

        return $this;
    }

    public function getDiecutApprovalDate(): ?\DateTimeInterface
    {
        return $this->diecutApprovalDate;
    }

    public function setDiecutApprovalDate(?\DateTimeInterface $diecutApprovalDate): self
    {
        $this->diecutApprovalDate = $diecutApprovalDate;

        return $this;
    }

    public function getChecklistDiecutNumber(): ?string
    {
        return $this->checklistDiecutNumber;
    }

    public function setChecklistDiecutNumber(string $checklistDiecutNumber): self
    {
        $this->checklistDiecutNumber = $checklistDiecutNumber;

        return $this;
    }

    public function getChecklistDiecutRevision(): ?string
    {
        return $this->checklistDiecutRevision;
    }

    public function setChecklistDiecutRevision(string $checklistDiecutRevision): self
    {
        $this->checklistDiecutRevision = $checklistDiecutRevision;

        return $this;
    }

    public function getChecklistDiecutDate(): ?\DateTimeInterface
    {
        return $this->checklistDiecutDate;
    }

    public function setChecklistDiecutDate(?\DateTimeInterface $checklistDiecutDate): self
    {
        $this->checklistDiecutDate = $checklistDiecutDate;

        return $this;
    }

    public function getClearanceDiecutNumber(): ?string
    {
        return $this->clearanceDiecutNumber;
    }

    public function setClearanceDiecutNumber(string $clearanceDiecutNumber): self
    {
        $this->clearanceDiecutNumber = $clearanceDiecutNumber;

        return $this;
    }

    public function getClearanceDiecutRevision(): ?string
    {
        return $this->clearanceDiecutRevision;
    }

    public function setClearanceDiecutRevision(string $clearanceDiecutRevision): self
    {
        $this->clearanceDiecutRevision = $clearanceDiecutRevision;

        return $this;
    }

    public function getClearanceDiecutDate(): ?\DateTimeInterface
    {
        return $this->clearanceDiecutDate;
    }

    public function setClearanceDiecutDate(?\DateTimeInterface $clearanceDiecutDate): self
    {
        $this->clearanceDiecutDate = $clearanceDiecutDate;

        return $this;
    }

    public function getChecklistGlueingNumber(): ?string
    {
        return $this->checklistGlueingNumber;
    }

    public function setChecklistGlueingNumber(string $checklistGlueingNumber): self
    {
        $this->checklistGlueingNumber = $checklistGlueingNumber;

        return $this;
    }

    public function getChecklistGlueingRevision(): ?string
    {
        return $this->checklistGlueingRevision;
    }

    public function setChecklistGlueingRevision(string $checklistGlueingRevision): self
    {
        $this->checklistGlueingRevision = $checklistGlueingRevision;

        return $this;
    }

    public function getChecklistGlueingDate(): ?\DateTimeInterface
    {
        return $this->checklistGlueingDate;
    }

    public function setChecklistGlueingDate(?\DateTimeInterface $checklistGlueingDate): self
    {
        $this->checklistGlueingDate = $checklistGlueingDate;

        return $this;
    }

    public function getPackingHandoverNumber(): ?string
    {
        return $this->packingHandoverNumber;
    }

    public function setPackingHandoverNumber(string $packingHandoverNumber): self
    {
        $this->packingHandoverNumber = $packingHandoverNumber;

        return $this;
    }

    public function getPackingHandoverRevision(): ?string
    {
        return $this->packingHandoverRevision;
    }

    public function setPackingHandoverRevision(string $packingHandoverRevision): self
    {
        $this->packingHandoverRevision = $packingHandoverRevision;

        return $this;
    }

    public function getPackingHandoverDate(): ?\DateTimeInterface
    {
        return $this->packingHandoverDate;
    }

    public function setPackingHandoverDate(?\DateTimeInterface $packingHandoverDate): self
    {
        $this->packingHandoverDate = $packingHandoverDate;

        return $this;
    }

    public function getOutgoingQualitySheetNumber(): ?string
    {
        return $this->outgoingQualitySheetNumber;
    }

    public function setOutgoingQualitySheetNumber(string $outgoingQualitySheetNumber): self
    {
        $this->outgoingQualitySheetNumber = $outgoingQualitySheetNumber;

        return $this;
    }

    public function getOutgoingQualitySheetRevision(): ?string
    {
        return $this->outgoingQualitySheetRevision;
    }

    public function setOutgoingQualitySheetRevision(string $outgoingQualitySheetRevision): self
    {
        $this->outgoingQualitySheetRevision = $outgoingQualitySheetRevision;

        return $this;
    }

    public function getOutgoingQualitySheetDate(): ?\DateTimeInterface
    {
        return $this->outgoingQualitySheetDate;
    }

    public function setOutgoingQualitySheetDate(?\DateTimeInterface $outgoingQualitySheetDate): self
    {
        $this->outgoingQualitySheetDate = $outgoingQualitySheetDate;

        return $this;
    }

    public function getFormPlateNumber(): ?string
    {
        return $this->formPlateNumber;
    }

    public function setFormPlateNumber(string $formPlateNumber): self
    {
        $this->formPlateNumber = $formPlateNumber;

        return $this;
    }

    public function getFormPlateRevision(): ?string
    {
        return $this->formPlateRevision;
    }

    public function setFormPlateRevision(string $formPlateRevision): self
    {
        $this->formPlateRevision = $formPlateRevision;

        return $this;
    }

    public function getFormPlateDate(): ?\DateTimeInterface
    {
        return $this->formPlateDate;
    }

    public function setFormPlateDate(?\DateTimeInterface $formPlateDate): self
    {
        $this->formPlateDate = $formPlateDate;

        return $this;
    }

    public function getChecklistPrepressNumber(): ?string
    {
        return $this->checklistPrepressNumber;
    }

    public function setChecklistPrepressNumber(string $checklistPrepressNumber): self
    {
        $this->checklistPrepressNumber = $checklistPrepressNumber;

        return $this;
    }

    public function getChecklistPrepressRevision(): ?string
    {
        return $this->checklistPrepressRevision;
    }

    public function setChecklistPrepressRevision(string $checklistPrepressRevision): self
    {
        $this->checklistPrepressRevision = $checklistPrepressRevision;

        return $this;
    }

    public function getChecklistPrepressDate(): ?\DateTimeInterface
    {
        return $this->checklistPrepressDate;
    }

    public function setChecklistPrepressDate(?\DateTimeInterface $checklistPrepressDate): self
    {
        $this->checklistPrepressDate = $checklistPrepressDate;

        return $this;
    }

    public function getPrintingApprovalNumber(): ?string
    {
        return $this->printingApprovalNumber;
    }

    public function setPrintingApprovalNumber(string $printingApprovalNumber): self
    {
        $this->printingApprovalNumber = $printingApprovalNumber;

        return $this;
    }

    public function getPrintingApprovalRevision(): ?string
    {
        return $this->printingApprovalRevision;
    }

    public function setPrintingApprovalRevision(string $printingApprovalRevision): self
    {
        $this->printingApprovalRevision = $printingApprovalRevision;

        return $this;
    }

    public function getPrintingApprovalDate(): ?\DateTimeInterface
    {
        return $this->printingApprovalDate;
    }

    public function setPrintingApprovalDate(?\DateTimeInterface $printingApprovalDate): self
    {
        $this->printingApprovalDate = $printingApprovalDate;

        return $this;
    }

    public function getChecklistPrintingNumber(): ?string
    {
        return $this->checklistPrintingNumber;
    }

    public function setChecklistPrintingNumber(string $checklistPrintingNumber): self
    {
        $this->checklistPrintingNumber = $checklistPrintingNumber;

        return $this;
    }

    public function getChecklistPrintingRevision(): ?string
    {
        return $this->checklistPrintingRevision;
    }

    public function setChecklistPrintingRevision(string $checklistPrintingRevision): self
    {
        $this->checklistPrintingRevision = $checklistPrintingRevision;

        return $this;
    }

    public function getChecklistPrintingDate(): ?\DateTimeInterface
    {
        return $this->checklistPrintingDate;
    }

    public function setChecklistPrintingDate(?\DateTimeInterface $checklistPrintingDate): self
    {
        $this->checklistPrintingDate = $checklistPrintingDate;

        return $this;
    }

    public function getPrintingInspectionNumber(): ?string
    {
        return $this->printingInspectionNumber;
    }

    public function setPrintingInspectionNumber(string $printingInspectionNumber): self
    {
        $this->printingInspectionNumber = $printingInspectionNumber;

        return $this;
    }

    public function getPrintingInspectionRevision(): ?string
    {
        return $this->printingInspectionRevision;
    }

    public function setPrintingInspectionRevision(string $printingInspectionRevision): self
    {
        $this->printingInspectionRevision = $printingInspectionRevision;

        return $this;
    }

    public function getPrintingInspectionDate(): ?\DateTimeInterface
    {
        return $this->printingInspectionDate;
    }

    public function setPrintingInspectionDate(?\DateTimeInterface $printingInspectionDate): self
    {
        $this->printingInspectionDate = $printingInspectionDate;

        return $this;
    }

    public function getChecklistProductionNumber(): ?string
    {
        return $this->checklistProductionNumber;
    }

    public function setChecklistProductionNumber(string $checklistProductionNumber): self
    {
        $this->checklistProductionNumber = $checklistProductionNumber;

        return $this;
    }

    public function getChecklistProductionRevision(): ?string
    {
        return $this->checklistProductionRevision;
    }

    public function setChecklistProductionRevision(string $checklistProductionRevision): self
    {
        $this->checklistProductionRevision = $checklistProductionRevision;

        return $this;
    }

    public function getChecklistProductionDate(): ?\DateTimeInterface
    {
        return $this->checklistProductionDate;
    }

    public function setChecklistProductionDate(?\DateTimeInterface $checklistProductionDate): self
    {
        $this->checklistProductionDate = $checklistProductionDate;

        return $this;
    }

    public function getChecklistSortingNumber(): ?string
    {
        return $this->checklistSortingNumber;
    }

    public function setChecklistSortingNumber(string $checklistSortingNumber): self
    {
        $this->checklistSortingNumber = $checklistSortingNumber;

        return $this;
    }

    public function getChecklistSortingRevision(): ?string
    {
        return $this->checklistSortingRevision;
    }

    public function setChecklistSortingRevision(string $checklistSortingRevision): self
    {
        $this->checklistSortingRevision = $checklistSortingRevision;

        return $this;
    }

    public function getChecklistSortingDate(): ?\DateTimeInterface
    {
        return $this->checklistSortingDate;
    }

    public function setChecklistSortingDate(?\DateTimeInterface $checklistSortingDate): self
    {
        $this->checklistSortingDate = $checklistSortingDate;

        return $this;
    }

    public function getQualityInspectionNumber(): ?string
    {
        return $this->qualityInspectionNumber;
    }

    public function setQualityInspectionNumber(string $qualityInspectionNumber): self
    {
        $this->qualityInspectionNumber = $qualityInspectionNumber;

        return $this;
    }

    public function getQualityInspectionRevision(): ?string
    {
        return $this->qualityInspectionRevision;
    }

    public function setQualityInspectionRevision(string $qualityInspectionRevision): self
    {
        $this->qualityInspectionRevision = $qualityInspectionRevision;

        return $this;
    }

    public function getQualityInspectionDate(): ?\DateTimeInterface
    {
        return $this->qualityInspectionDate;
    }

    public function setQualityInspectionDate(?\DateTimeInterface $qualityInspectionDate): self
    {
        $this->qualityInspectionDate = $qualityInspectionDate;

        return $this;
    }

    public function getMasterOrderNumber(): ?string
    {
        return $this->masterOrderNumber;
    }

    public function setMasterOrderNumber(string $masterOrderNumber): self
    {
        $this->masterOrderNumber = $masterOrderNumber;

        return $this;
    }

    public function getMasterOrderRevision(): ?string
    {
        return $this->masterOrderRevision;
    }

    public function setMasterOrderRevision(string $masterOrderRevision): self
    {
        $this->masterOrderRevision = $masterOrderRevision;

        return $this;
    }

    public function getMasterOrderDate(): ?\DateTimeInterface
    {
        return $this->masterOrderDate;
    }

    public function setMasterOrderDate(?\DateTimeInterface $masterOrderDate): self
    {
        $this->masterOrderDate = $masterOrderDate;

        return $this;
    }

    public function getWorkOrderNumber(): ?string
    {
        return $this->workOrderNumber;
    }

    public function setWorkOrderNumber(string $workOrderNumber): self
    {
        $this->workOrderNumber = $workOrderNumber;

        return $this;
    }

    public function getWorkOrderRevision(): ?string
    {
        return $this->workOrderRevision;
    }

    public function setWorkOrderRevision(string $workOrderRevision): self
    {
        $this->workOrderRevision = $workOrderRevision;

        return $this;
    }

    public function getWorkOrderDate(): ?\DateTimeInterface
    {
        return $this->workOrderDate;
    }

    public function setWorkOrderDate(?\DateTimeInterface $workOrderDate): self
    {
        $this->workOrderDate = $workOrderDate;

        return $this;
    }

    public function getColorMixingNumber(): ?string
    {
        return $this->colorMixingNumber;
    }

    public function setColorMixingNumber(string $colorMixingNumber): self
    {
        $this->colorMixingNumber = $colorMixingNumber;

        return $this;
    }

    public function getColorMixingRevision(): ?string
    {
        return $this->colorMixingRevision;
    }

    public function setColorMixingRevision(string $colorMixingRevision): self
    {
        $this->colorMixingRevision = $colorMixingRevision;

        return $this;
    }

    public function getColorMixingDate(): ?\DateTimeInterface
    {
        return $this->colorMixingDate;
    }

    public function setColorMixingDate(?\DateTimeInterface $colorMixingDate): self
    {
        $this->colorMixingDate = $colorMixingDate;

        return $this;
    }

    public function getCuttingMaterialNumber(): ?string
    {
        return $this->cuttingMaterialNumber;
    }

    public function setCuttingMaterialNumber(string $cuttingMaterialNumber): self
    {
        $this->cuttingMaterialNumber = $cuttingMaterialNumber;

        return $this;
    }

    public function getCuttingMaterialRevision(): ?string
    {
        return $this->cuttingMaterialRevision;
    }

    public function setCuttingMaterialRevision(string $cuttingMaterialRevision): self
    {
        $this->cuttingMaterialRevision = $cuttingMaterialRevision;

        return $this;
    }

    public function getCuttingMaterialDate(): ?\DateTimeInterface
    {
        return $this->cuttingMaterialDate;
    }

    public function setCuttingMaterialDate(?\DateTimeInterface $cuttingMaterialDate): self
    {
        $this->cuttingMaterialDate = $cuttingMaterialDate;

        return $this;
    }

    public function getDiecutBobstNumber(): ?string
    {
        return $this->diecutBobstNumber;
    }

    public function setDiecutBobstNumber(string $diecutBobstNumber): self
    {
        $this->diecutBobstNumber = $diecutBobstNumber;

        return $this;
    }

    public function getDiecutBobstRevision(): ?string
    {
        return $this->diecutBobstRevision;
    }

    public function setDiecutBobstRevision(string $diecutBobstRevision): self
    {
        $this->diecutBobstRevision = $diecutBobstRevision;

        return $this;
    }

    public function getDiecutBobstDate(): ?\DateTimeInterface
    {
        return $this->diecutBobstDate;
    }

    public function setDiecutBobstDate(?\DateTimeInterface $diecutBobstDate): self
    {
        $this->diecutBobstDate = $diecutBobstDate;

        return $this;
    }

    public function getFinishingNumber(): ?string
    {
        return $this->finishingNumber;
    }

    public function setFinishingNumber(string $finishingNumber): self
    {
        $this->finishingNumber = $finishingNumber;

        return $this;
    }

    public function getFinishingRevision(): ?string
    {
        return $this->finishingRevision;
    }

    public function setFinishingRevision(string $finishingRevision): self
    {
        $this->finishingRevision = $finishingRevision;

        return $this;
    }

    public function getFinishingDate(): ?\DateTimeInterface
    {
        return $this->finishingDate;
    }

    public function setFinishingDate(?\DateTimeInterface $finishingDate): self
    {
        $this->finishingDate = $finishingDate;

        return $this;
    }

    public function getGlueingMachineNumber(): ?string
    {
        return $this->glueingMachineNumber;
    }

    public function setGlueingMachineNumber(string $glueingMachineNumber): self
    {
        $this->glueingMachineNumber = $glueingMachineNumber;

        return $this;
    }

    public function getGlueingMachineRevision(): ?string
    {
        return $this->glueingMachineRevision;
    }

    public function setGlueingMachineRevision(string $glueingMachineRevision): self
    {
        $this->glueingMachineRevision = $glueingMachineRevision;

        return $this;
    }

    public function getGlueingMachineDate(): ?\DateTimeInterface
    {
        return $this->glueingMachineDate;
    }

    public function setGlueingMachineDate(?\DateTimeInterface $glueingMachineDate): self
    {
        $this->glueingMachineDate = $glueingMachineDate;

        return $this;
    }

    public function getHotStampingNumber(): ?string
    {
        return $this->hotStampingNumber;
    }

    public function setHotStampingNumber(string $hotStampingNumber): self
    {
        $this->hotStampingNumber = $hotStampingNumber;

        return $this;
    }

    public function getHotStampingRevision(): ?string
    {
        return $this->hotStampingRevision;
    }

    public function setHotStampingRevision(string $hotStampingRevision): self
    {
        $this->hotStampingRevision = $hotStampingRevision;

        return $this;
    }

    public function getHotStampingDate(): ?\DateTimeInterface
    {
        return $this->hotStampingDate;
    }

    public function setHotStampingDate(?\DateTimeInterface $hotStampingDate): self
    {
        $this->hotStampingDate = $hotStampingDate;

        return $this;
    }

    public function getPackingNumber(): ?string
    {
        return $this->packingNumber;
    }

    public function setPackingNumber(string $packingNumber): self
    {
        $this->packingNumber = $packingNumber;

        return $this;
    }

    public function getPackingRevision(): ?string
    {
        return $this->packingRevision;
    }

    public function setPackingRevision(string $packingRevision): self
    {
        $this->packingRevision = $packingRevision;

        return $this;
    }

    public function getPackingDate(): ?\DateTimeInterface
    {
        return $this->packingDate;
    }

    public function setPackingDate(?\DateTimeInterface $packingDate): self
    {
        $this->packingDate = $packingDate;

        return $this;
    }

    public function getPrepressNumber(): ?string
    {
        return $this->prepressNumber;
    }

    public function setPrepressNumber(string $prepressNumber): self
    {
        $this->prepressNumber = $prepressNumber;

        return $this;
    }

    public function getPrepressRevision(): ?string
    {
        return $this->prepressRevision;
    }

    public function setPrepressRevision(string $prepressRevision): self
    {
        $this->prepressRevision = $prepressRevision;

        return $this;
    }

    public function getPrepressDate(): ?\DateTimeInterface
    {
        return $this->prepressDate;
    }

    public function setPrepressDate(?\DateTimeInterface $prepressDate): self
    {
        $this->prepressDate = $prepressDate;

        return $this;
    }

    public function getOffsetPrintingNumber(): ?string
    {
        return $this->offsetPrintingNumber;
    }

    public function setOffsetPrintingNumber(string $offsetPrintingNumber): self
    {
        $this->offsetPrintingNumber = $offsetPrintingNumber;

        return $this;
    }

    public function getOffsetPrintingRevision(): ?string
    {
        return $this->offsetPrintingRevision;
    }

    public function setOffsetPrintingRevision(string $offsetPrintingRevision): self
    {
        $this->offsetPrintingRevision = $offsetPrintingRevision;

        return $this;
    }

    public function getOffsetPrintingDate(): ?\DateTimeInterface
    {
        return $this->offsetPrintingDate;
    }

    public function setOffsetPrintingDate(?\DateTimeInterface $offsetPrintingDate): self
    {
        $this->offsetPrintingDate = $offsetPrintingDate;

        return $this;
    }

    public function getSortingNumber(): ?string
    {
        return $this->sortingNumber;
    }

    public function setSortingNumber(string $sortingNumber): self
    {
        $this->sortingNumber = $sortingNumber;

        return $this;
    }

    public function getSortingRevision(): ?string
    {
        return $this->sortingRevision;
    }

    public function setSortingRevision(string $sortingRevision): self
    {
        $this->sortingRevision = $sortingRevision;

        return $this;
    }

    public function getSortingDate(): ?\DateTimeInterface
    {
        return $this->sortingDate;
    }

    public function setSortingDate(?\DateTimeInterface $sortingDate): self
    {
        $this->sortingDate = $sortingDate;

        return $this;
    }

    public function getStitchingNumber(): ?string
    {
        return $this->stitchingNumber;
    }

    public function setStitchingNumber(string $stitchingNumber): self
    {
        $this->stitchingNumber = $stitchingNumber;

        return $this;
    }

    public function getStitchingRevision(): ?string
    {
        return $this->stitchingRevision;
    }

    public function setStitchingRevision(string $stitchingRevision): self
    {
        $this->stitchingRevision = $stitchingRevision;

        return $this;
    }

    public function getStitchingDate(): ?\DateTimeInterface
    {
        return $this->stitchingDate;
    }

    public function setStitchingDate(?\DateTimeInterface $stitchingDate): self
    {
        $this->stitchingDate = $stitchingDate;

        return $this;
    }

    public function getCoatingNumber(): ?string
    {
        return $this->coatingNumber;
    }

    public function setCoatingNumber(string $coatingNumber): self
    {
        $this->coatingNumber = $coatingNumber;

        return $this;
    }

    public function getCoatingRevision(): ?string
    {
        return $this->coatingRevision;
    }

    public function setCoatingRevision(string $coatingRevision): self
    {
        $this->coatingRevision = $coatingRevision;

        return $this;
    }

    public function getCoatingDate(): ?\DateTimeInterface
    {
        return $this->coatingDate;
    }

    public function setCoatingDate(?\DateTimeInterface $coatingDate): self
    {
        $this->coatingDate = $coatingDate;

        return $this;
    }
}
