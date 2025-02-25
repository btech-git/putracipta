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
}
