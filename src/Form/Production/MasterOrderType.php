<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Customer;
use App\Entity\Master\Paper;
use App\Entity\Production\MasterOrder;
use App\Entity\Transaction\SaleOrderDetail;
use App\Entity\Transaction\PurchaseOrderPaperHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MasterOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('printingMachine')
            ->add('designCode')
//            ->add('productCode')
            ->add('quantityOrder')
            ->add('quantityStock')
            ->add('quantityProduction')
            ->add('dimensionLength')
            ->add('dimensionWidth')
            ->add('dimensionHeight')
            ->add('color')
            ->add('pantone')
            ->add('colorPantoneAdditional')
            ->add('finishing')
            ->add('quantityPrinting')
            ->add('quantityPrinting2')
            ->add('mountageSizeLength')
            ->add('mountageSizeWidth')
            ->add('isUsingHotStamping')
            ->add('printingStatus', ChoiceType::class, ['choices' => [
                'Proof Print' => MasterOrder::PRINTING_STATUS_PROOF_PRINT,
                'New Order' => MasterOrder::PRINTING_STATUS_NEW_ORDER,
                'Repeat Order' => MasterOrder::PRINTING_STATUS_REPEAT_ORDER,
                'Revisi Design' => MasterOrder::PRINTING_STATUS_REVISE_DESIGN,
            ]])
            ->add('printingStatusData')
            ->add('dieCutBlade', ChoiceType::class, ['choices' => [
                'Baru' => MasterOrder::DIECUT_BLADE_NEW,
                'Lama' => MasterOrder::DIECUT_BLADE_OLD,
                'Revisi' => MasterOrder::DIECUT_BLADE_REVISION,
            ]])
            ->add('dieCutBladeData')
            ->add('dieCutBladeNumber')
            ->add('dieLineFilmNumber')
//            ->add('layoutModelFileExtension')
            ->add('insitPercentage')
            ->add('quantityPaper')
            ->add('paperMountage')
            ->add('paperPlanoLength')
            ->add('paperPlanoWidth')
            ->add('cuttingLengthSize1')
            ->add('cuttingWidthSize1')
            ->add('cuttingLengthSize2')
            ->add('cuttingWidthSize2')
            ->add('paperRequirement')
            ->add('printingInsit')
            ->add('paperTotal')
            ->add('inkCyanPercentage')
            ->add('inkCyanWeight')
            ->add('inkMagentaPercentage')
            ->add('inkMagentaWeight')
            ->add('inkYellowPercentage')
            ->add('inkYellowWeight')
            ->add('inkBlackPercentage')
            ->add('inkBlackWeight')
            ->add('inkK1Color')
            ->add('inkK1Percentage')
            ->add('inkK1Weight')
            ->add('inkK2Color')
            ->add('inkK2Percentage')
            ->add('inkK2Weight')
            ->add('inkK3Color')
            ->add('inkK3Percentage')
            ->add('inkK3Weight')
            ->add('inkK4Color')
            ->add('inkK4Percentage')
            ->add('inkK4Weight')
            ->add('inkOpvPercentage')
            ->add('inkOpvWeight')
            ->add('inkLaminatingSize')
            ->add('inkLaminatingQuantity')
            ->add('packagingGlueQuantity')
            ->add('packagingGlueWeight')
            ->add('packagingRubberQuantity')
            ->add('packagingRubberWeight')
            ->add('packagingPaperQuantity')
            ->add('packagingPaperWeight')
            ->add('packagingBoxQuantity')
            ->add('packagingBoxWeight')
            ->add('packagingTapeLargeQuantity')
            ->add('packagingTapeLargeSize')
            ->add('packagingTapeSmallQuantity')
            ->add('packagingTapeSmallSize')
            ->add('packagingPlasticQuantity')
            ->add('packagingPlasticSize')
            ->add('note')
            ->add('saleOrderDetail', EntityHiddenType::class, ['class' => SaleOrderDetail::class])
            ->add('purchaseOrderPaperHeader', EntityHiddenType::class, ['class' => PurchaseOrderPaperHeader::class])
            ->add('deliveryDate', null, ['widget' => 'single_text'])
            ->add('productionDate', null, ['widget' => 'single_text'])
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
            ->add('workOrderDistribution', ChoiceType::class, ['multiple' => true, 'expanded' => true, 'choices' => [
                'WO Design' => MasterOrder::WORK_ORDER_DESIGN,
                'WO Mountage / Prepress' => MasterOrder::WORK_ORDER_MOUNTAGE,
                'WO Mesin Potong' => MasterOrder::WORK_ORDER_CUTTING_MACHINE,
                'WO Cetak/Sablon' => MasterOrder::WORK_ORDER_PRINTING,
                'WO PON/BOBST' => MasterOrder::WORK_ORDER_PON,
                'WO Potong Jadi' => MasterOrder::WORK_ORDER_CUTTING_FINISHING,
                'WO Varnish/Laminating' => MasterOrder::WORK_ORDER_VARNISH,
                'WO Mesin Lem' => MasterOrder::WORK_ORDER_GLUEING,
                'WO Mesin Stitching' => MasterOrder::WORK_ORDER_STITCHING,
                'WO Finishing' => MasterOrder::WORK_ORDER_FINISHING,
                'WO Packing' => MasterOrder::WORK_ORDER_PACKING,
                'WO QC Printing' => MasterOrder::WORK_ORDER_QC_PRINTING,
                'WO QC Finishing' => MasterOrder::WORK_ORDER_QC_FINISHING,
                'WO QC Sortir' => MasterOrder::WORK_ORDER_QC_SORTING,
                'WO QC Outgoing' => MasterOrder::WORK_ORDER_QC_OUTGOING,
                'WO Printing Checklist' => MasterOrder::WORK_ORDER_PRINTING_CHECKLIST,
                'WO Mountage Checklist' => MasterOrder::WORK_ORDER_MOUNTAGE_CHECKLIST,
                'WO Pond Checklist' => MasterOrder::WORK_ORDER_POND_CHECKLIST,
                'WO Penghancuran' => MasterOrder::WORK_ORDER_DEMOLITION,
                'WO Rework' => MasterOrder::WORK_ORDER_REWORK,
            ]])
            ->add('processList', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => '',
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MasterOrder::class,
        ]);
    }
}
