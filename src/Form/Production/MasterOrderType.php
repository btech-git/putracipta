<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Entity\Master\Customer;
use App\Entity\Master\Paper;
use App\Entity\Production\MasterOrder;
use App\Entity\Transaction\SaleOrderHeader;
use App\Entity\Transaction\PurchaseOrderPaperHeader;
use Symfony\Component\Form\AbstractType;
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
            ->add('dimensionLength')
            ->add('dimensionWidth')
            ->add('dimensionHeight')
            ->add('color')
            ->add('finishing')
            ->add('quantityPrinting')
            ->add('mountageSizeLength')
            ->add('mountageSizeWidth')
            ->add('isUsingHotStamping')
            ->add('printingStatus')
            ->add('dieCutBlade')
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
            ->add('inkK1Percentage')
            ->add('inkK1Weight')
            ->add('inkK2Percentage')
            ->add('inkK2Weight')
            ->add('inkK3Percentage')
            ->add('inkK3Weight')
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
            ->add('saleOrderHeader', EntityHiddenType::class, ['class' => SaleOrderHeader::class])
            ->add('purchaseOrderPaperHeader', EntityHiddenType::class, ['class' => PurchaseOrderPaperHeader::class])
            ->add('deliveryDate', null, ['widget' => 'single_text'])
            ->add('productionDate', null, ['widget' => 'single_text'])
            ->add('customer', EntityHiddenType::class, ['class' => Customer::class])
            ->add('paper', EntityHiddenType::class, array('class' => Paper::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MasterOrder::class,
        ]);
    }
}
