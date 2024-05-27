<?php

namespace App\Form\Production;

use App\Common\Form\Type\EntityHiddenType;
use App\Common\Form\Type\FormattedNumberType;
use App\Entity\Master\Product;
use App\Entity\Production\MasterOrderProductDetail;
use App\Entity\Production\QualityControlSortingDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QualityControlSortingDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantityOrder', FormattedNumberType::class, ['decimals' => 2])
            ->add('quantityGood', FormattedNumberType::class, ['decimals' => 2])
            ->add('quantityRejectPrinting', FormattedNumberType::class, ['decimals' => 2])
            ->add('quantityRejectCoating', FormattedNumberType::class, ['decimals' => 2])
            ->add('quantityRejectCutting', FormattedNumberType::class, ['decimals' => 2])
            ->add('quantityRejectDiecutting', FormattedNumberType::class, ['decimals' => 2])
            ->add('quantityRejectGlueing', FormattedNumberType::class, ['decimals' => 2])
            ->add('quantityRejectFinishing', FormattedNumberType::class, ['decimals' => 2])
            ->add('quantityRejectOthers', FormattedNumberType::class, ['decimals' => 2])
            ->add('totalQuantitySorting', FormattedNumberType::class, ['decimals' => 2])
            ->add('totalQuantityReject', FormattedNumberType::class, ['decimals' => 2])
            ->add('quantityRemaining', FormattedNumberType::class, ['decimals' => 2])
            ->add('memo')
            ->add('isCanceled')
            ->add('product', EntityHiddenType::class, array('class' => Product::class))
            ->add('masterOrderProductDetail', EntityHiddenType::class, ['class' => MasterOrderProductDetail::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QualityControlSortingDetail::class,
        ]);
    }
}
