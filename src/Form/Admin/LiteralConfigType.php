<?php

namespace App\Form\Admin;

use App\Common\Form\Type\FormattedDateType;
use App\Entity\Admin\LiteralConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LiteralConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ifscCode', null, ['label' => 'FSC Code'])
            ->add('vatPercentage', null, ['label' => 'VAT'])
            ->add('serviceTaxPercentage', null, ['label' => 'PPh 23'])
            ->add('paymentRemainingTolerance', null, ['label' => 'Toleransi Sisa Pembayaran'])
            ->add('diecutApprovalNumber', null, ['label' => 'DIECUT & EMBOSS APPROVAL #'])
            ->add('diecutApprovalRevision', null, ['label' => 'Revisi'])
            ->add('diecutApprovalDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('checklistDiecutNumber', null, ['label' => 'CHECKLIST DIECUT #'])
            ->add('checklistDiecutRevision', null, ['label' => 'Revisi'])
            ->add('checklistDiecutDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('clearanceDiecutNumber', null, ['label' => 'CHECKLIST LINE CLEARANCE DIECUT #'])
            ->add('clearanceDiecutRevision', null, ['label' => 'Revisi'])
            ->add('clearanceDiecutDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('checklistGlueingNumber', null, ['label' => 'GLUEING LINE CLEARANCE CHECKLIST #'])
            ->add('checklistGlueingRevision', null, ['label' => 'Revisi'])
            ->add('checklistGlueingDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('packingHandoverNumber', null, ['label' => 'SERAH TERIMA PACKING #'])
            ->add('packingHandoverRevision', null, ['label' => 'Revisi'])
            ->add('packingHandoverDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('outgoingQualitySheetNumber', null, ['label' => 'OUTGOING QUALITY SHEET #'])
            ->add('outgoingQualitySheetRevision', null, ['label' => 'Revisi'])
            ->add('outgoingQualitySheetDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('checklistPrepressNumber', null, ['label' => 'PREPRESS CHECK LIST #'])
            ->add('checklistPrepressRevision', null, ['label' => 'Revisi'])
            ->add('checklistPrepressDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('printingApprovalNumber', null, ['label' => 'PRINTING APPROVAL #'])
            ->add('printingApprovalRevision', null, ['label' => 'Revisi'])
            ->add('printingApprovalDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('checklistPrintingNumber', null, ['label' => 'CHECKLIST PRINTING #'])
            ->add('checklistPrintingRevision', null, ['label' => 'Revisi'])
            ->add('checklistPrintingDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('printingInspectionNumber', null, ['label' => 'PRINTING QUALITY INSPECTION SHEET #'])
            ->add('printingInspectionRevision', null, ['label' => 'Revisi'])
            ->add('printingInspectionDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('checklistProductionNumber', null, ['label' => 'CHECKLIST PERSIAPAN PRODUKSI #'])
            ->add('checklistProductionRevision', null, ['label' => 'Revisi'])
            ->add('checklistProductionDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('checklistSortingNumber', null, ['label' => 'CHECKLIST LINE CLEARANCE SORTIR #'])
            ->add('checklistSortingRevision', null, ['label' => 'Revisi'])
            ->add('checklistSortingDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('qualityInspectionNumber', null, ['label' => 'WIP QUALITY INSPECTION SHEET #'])
            ->add('qualityInspectionRevision', null, ['label' => 'Revisi'])
            ->add('qualityInspectionDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('masterOrderNumber', null, ['label' => 'MASTER ORDER #'])
            ->add('masterOrderRevision', null, ['label' => 'Revisi'])
            ->add('masterOrderDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('workOrderNumber', null, ['label' => 'WORK ORDER #'])
            ->add('workOrderRevision', null, ['label' => 'Revisi'])
            ->add('workOrderDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('colorMixingNumber', null, ['label' => 'WORK ORDER COLOR MIXING #'])
            ->add('colorMixingRevision', null, ['label' => 'Revisi'])
            ->add('colorMixingDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('cuttingMaterialNumber', null, ['label' => 'WORK ORDER POTONG BAHAN/JADI #'])
            ->add('cuttingMaterialRevision', null, ['label' => 'Revisi'])
            ->add('cuttingMaterialDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('diecutBobstNumber', null, ['label' => 'WORK ORDER DIECUT BOBST/MANUAL #'])
            ->add('diecutBobstRevision', null, ['label' => 'Revisi'])
            ->add('diecutBobstDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('finishingNumber', null, ['label' => 'WORK ORDER FINISHING #'])
            ->add('finishingRevision', null, ['label' => 'Revisi'])
            ->add('finishingDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('glueingMachineNumber', null, ['label' => 'WORK ORDER MESIN LEM #'])
            ->add('glueingMachineRevision', null, ['label' => 'Revisi'])
            ->add('glueingMachineDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('hotStampingNumber', null, ['label' => 'WORK ORDER HOT STAMPING #'])
            ->add('hotStampingRevision', null, ['label' => 'Revisi'])
            ->add('hotStampingDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('packingNumber', null, ['label' => 'WORK ORDER PACKING #'])
            ->add('packingRevision', null, ['label' => 'Revisi'])
            ->add('packingDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('prepressNumber', null, ['label' => 'WORK ORDER PREPRESS #'])
            ->add('prepressRevision', null, ['label' => 'Revisi'])
            ->add('prepressDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('offsetPrintingNumber', null, ['label' => 'WORK ORDER OFFSET PRINTING #'])
            ->add('offsetPrintingRevision', null, ['label' => 'Revisi'])
            ->add('offsetPrintingDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('sortingNumber', null, ['label' => 'WORK ORDER SORTIR #'])
            ->add('sortingRevision', null, ['label' => 'Revisi'])
            ->add('sortingDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('stitchingNumber', null, ['label' => 'WORK ORDER STITCHING (JAHIT KAWAT) #'])
            ->add('stitchingRevision', null, ['label' => 'Revisi'])
            ->add('stitchingDate', FormattedDateType::class, ['label' => 'Tanggal'])
            ->add('coatingNumber', null, ['label' => 'WORK ORDER COATING #'])
            ->add('coatingRevision', null, ['label' => 'Revisi'])
            ->add('coatingDate', FormattedDateType::class, ['label' => 'Tanggal'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LiteralConfig::class,
        ]);
    }
}
