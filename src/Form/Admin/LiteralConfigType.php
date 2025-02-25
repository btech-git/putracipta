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
            ->add('formPlateNumber', null, ['label' => 'FORM PLATE #'])
            ->add('formPlateRevision', null, ['label' => 'Revisi'])
            ->add('formPlateDate', FormattedDateType::class, ['label' => 'Tanggal'])
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LiteralConfig::class,
        ]);
    }
}
